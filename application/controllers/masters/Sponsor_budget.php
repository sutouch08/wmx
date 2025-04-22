<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sponsor_budget extends PS_Controller
{
  public $menu_code = 'DBSPBD';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข งบอภินันท์';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/sponsor_budget';
    $this->load->model('masters/sponsor_budget_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'bd_code', ''),
      'reference' => get_filter('reference', 'bd_reference', ''),
      'year' => get_filter('year', 'bd_year', 'all'),
      'active' => get_filter('active', 'bd_active', 'all'),
      'from_date' => get_filter('from_date', 'bd_from_date', ''),
      'to_date' => get_filter('to_date', 'bd_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $rows = $this->sponsor_budget_model->count_rows($filter);
      $filter['data'] = $this->sponsor_budget_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('masters/sponsor_budget/sponsor_budget_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('masters/sponsor_budget/sponsor_budget_add');
  }


  public function add()
  {
		$sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        if( ! empty($ds->amount) && ! empty($ds->from_date) && ! empty($ds->to_date) && ! empty($ds->budget_year))
        {
          if($ds->amount > 0)
          {
            $code = $this->get_new_code();

            if( ! empty($code))
            {
              $arr = array(
                'code' => $code,
                'reference' => get_null($ds->reference),
                'from_date' => db_date($ds->from_date),
                'to_date' => db_date($ds->to_date),
                'amount' => $ds->amount,
                'used' => 0,
                'balance' => $ds->amount,
                'budget_year' => $ds->budget_year,
                'active' => $ds->active == 1 ? 1 : 0,
                'remark' => get_null($ds->remark),
                'user' => $this->_user->uname
              );

              if( ! $this->sponsor_budget_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to create new budget";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to generate new budget code";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "งบประมาณจำเป็นต้องมากกว่า 0";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('required');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }


    $this->_response($sc);
  }


  public function edit($id)
  {
    $bd = $this->sponsor_budget_model->get($id);

    if($this->pm->can_edit)
    {
      if( ! empty($bd))
      {
        $this->load->view('masters/sponsor_budget/sponsor_budget_edit', ['budget' => $bd]);
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function update()
  {
		$sc = TRUE;

    if($this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        if( ! empty($ds->id) && ! empty($ds->amount) && ! empty($ds->from_date) && ! empty($ds->to_date) && ! empty($ds->budget_year))
        {
          if($ds->amount > 0)
          {
            $bd = $this->sponsor_budget_model->get($ds->id);

            if(empty($bd))
            {
              $sc = FALSE;
              $this->error = get_error_message('notfound');
            }

            if($sc === TRUE)
            {
              if($ds->amount < $bd->used)
              {
                $sc = FALSE;
                $this->error = "ไม่สามารถแก้ไขให้งบประมาณน้อยกว่ายอดที่ใช้ไปแล้วได้";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'reference' => get_null($ds->reference),
                  'from_date' => db_date($ds->from_date),
                  'to_date' => db_date($ds->to_date),
                  'amount' => $ds->amount,
                  'budget_year' => $ds->budget_year,
                  'active' => $ds->active == 1 ? 1 : 0,
                  'remark' => get_null($ds->remark),
                  'update_user' => $this->_user->uname,
                  'date_upd' => now()
                );

                if( ! $this->sponsor_budget_model->update($ds->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update budget";
                }

                if($sc === TRUE)
                {
                  $this->sponsor_budget_model->recal_balance($ds->id);
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "งบประมาณจำเป็นต้องมากกว่า 0";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('required');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $this->_response($sc);
  }


  public function view_detail($id)
  {
    $bd = $this->sponsor_budget_model->get($id);

    if( ! empty($bd))
    {
      $members = $this->sponsor_budget_model->get_members($id);

      $this->load->view('masters/sponsor_budget/sponsor_budget_view_detail', ['budget' => $bd, 'members' => $members]);
    }
    else
    {
      $this->page_error();
    }
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if($id)
      {
        $bd = $this->sponsor_budget_model->get($id);

        if( ! empty($bd))
        {
          if( ! $this->sponsor_budget_model->has_transection($id))
          {
            if( ! $this->sponsor_budget_model->delete($id))
            {
              $sc = FALSE;
              $this->error = get_error_message('delete');
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = get_error_message('transection', $bd->code);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('required');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('permission');
    }

    $this->_response($sc);
  }


  public function get_new_code()
  {
    $prefix = date('ym');
    $run_digit = 3;
    $code = $this->sponsor_budget_model->get_max_code($prefix);

    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
	{
    $filter = array(
      'bd_code',
      'bd_reference',
      'bd_active',
      'bd_year',
      'bd_from_date',
      'bd_to_date'
    );

    return clear_filter($filter);
	}
}

?>
