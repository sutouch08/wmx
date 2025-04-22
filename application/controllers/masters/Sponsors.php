<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sponsors extends PS_Controller
{
  public $menu_code = 'DBSPON';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข รายชื่ออภินันท์';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/sponsors';
    $this->load->model('masters/sponsors_model');
    $this->load->model('masters/sponsor_budget_model');
    $this->load->model('masters/customers_model');

    $this->load->helper('customer');
    $this->load->helper('sponsors');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sp_code', ''),
      'reference' => get_filter('reference', 'sp_reference', ''),
      'year' => get_filter('year', 'sp_year', 'all'),
      'active' => get_filter('active', 'sp_active', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $rows = $this->sponsors_model->count_rows($filter);
      $filter['data'] = $this->sponsors_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('masters/sponsors/sponsors_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/sponsors/sponsors_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if($this->pm->can_add)
      {
        if( ! $this->sponsors_model->is_exists($ds->customer_code))
        {
          $bd = $this->sponsor_budget_model->get($ds->budget_id);

          $arr = array(
            'customer_code' => $ds->customer_code,
            'active' => $ds->active,
            'budget_id' => empty($bd) ? NULL : $bd->id,
            'year' => empty($bd) ? NULL : $bd->budget_year,
            'budget_code' => empty($bd) ? NULL : $bd->code,
            'user' => $this->_user->uname
          );

          if( ! $this->sponsors_model->add($arr))
          {
            $sc = FALSE;
            $this->error = get_error_message('insert');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = get_error_message('duplicated', $ds->customer_code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('permission');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $this->_response($sc);
  }


  public function edit($id)
  {
    if($this->pm->can_edit)
    {
      $ds = $this->sponsors_model->get($id);

      if( ! empty($ds))
      {
        $bd = $this->sponsor_budget_model->get($ds->budget_id);

        $this->load->view('masters/sponsors/sponsors_edit', ['ds' => $ds, 'budget' => $bd]);
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
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if($this->pm->can_edit)
      {
        $sp = $this->sponsors_model->get($ds->id);

        if( ! empty($sp))
        {
          $bd = $this->sponsor_budget_model->get($ds->budget_id);

          $arr = array(
            'active' => $ds->active,
            'budget_id' => empty($bd) ? NULL : $bd->id,
            'year' => empty($bd) ? NULL : $bd->budget_year,
            'budget_code' => empty($bd) ? NULL : $bd->code,
            'update_user' => $this->_user->uname,
            'date_upd' => now()
          );

          if( ! $this->sponsors_model->update($ds->id, $arr))
          {
            $sc = FALSE;
            $this->error = get_error_message('update');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Customer not found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = get_error_message('permission');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $this->_response($sc);
  }


  public function view_detail($id)
  {
    if($this->pm->can_edit)
    {
      $ds = $this->sponsors_model->get($id);

      if( ! empty($ds))
      {
        $bd = $this->sponsor_budget_model->get($ds->budget_id);

        $this->load->view('masters/sponsors/sponsors_view_detail', ['ds' => $ds, 'budget' => $bd]);
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


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if($id)
      {
        $sp = $this->sponsors_model->get($id);

        if( ! empty($sp))
        {
          if( ! $this->sponsors_model->delete($id))
          {
            $sc = FALSE;
            $this->error = get_error_message('delete');
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


  public function clear_filter()
	{
    $filter = array(
      'sp_code',
      'sp_reference',
      'sp_year',
      'sp_active'
    );

    clear_filter($filter);
	}
}

?>
