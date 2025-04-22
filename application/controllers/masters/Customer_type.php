<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_type extends PS_Controller
{
  public $menu_code = 'DBCTYP';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข ชนิดลูกค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/customer_type';
    $this->load->model('masters/customer_type_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      $perpage = get_rows();
      $segment = 4; //-- url segment
      $rows = $this->customer_type_model->count_rows($filter);
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $filter['data'] = $this->customer_type_model->get_list($filter, $perpage, $this->uri->segment($segment));
      $this->pagination->initialize($init);
      $this->load->view('masters/customer_type/customer_type_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/customer_type/customer_type_add');
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

    if($this->pm->can_add)
    {
      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
      {
        if($this->customer_type_model->is_exists($ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
        }

        if($sc === TRUE)
        {
          if($this->customer_type_model->is_exists_name($ds->name))
          {
            $sc = FALSE;
            set_error('exists', $ds->name);
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $ds->code,
            'name' => $ds->name
          );

          if( ! $this->customer_type_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $ds = $this->customer_type_model->get($code);

      if( ! empty($ds))
      {
        $this->load->view('masters/customer_type/customer_type_edit', ['ds' => $ds]);
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

      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
      {
        if($this->customer_type_model->is_exists_name($ds->name, $ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array('name' => $ds->name);

          if( ! $this->customer_type_model->update($ds->code, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        if($this->customer_type_model->has_transection($code))
        {
          $sc = FALSE;
          set_error('transection');
        }

        if($sc === TRUE)
        {
          if( ! $this->customer_type_model->delete($code))
          {
            $sc = FALSE;
            set_error('delete');
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function clear_filter()
	{
		$filter = ['code', 'name'];

    return clear_filter($filter);
	}

}//--- end class
 ?>
