<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_class extends PS_Controller
{
  public $menu_code = 'DBPDCL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'Product Class';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_class';
    $this->load->model('masters/product_class_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->product_class_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$filter['data'] = $this->product_class_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
		$this->pagination->initialize($init);
    $this->load->view('masters/product_class/product_class_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_class/product_class_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = $this->input->post('name');

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_class_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($this->product_class_model->is_exists_name($name))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        $ds = array(
          'code' => $code,
          'name' => $name
        );

        if( ! $this->product_class_model->add($ds))
        {
          set_error('insert');
        }
      }
    }
    else
    {
      set_error('required');
    }

    $this->_response($sc);
  }


  public function edit($code)
  {
    $rs = $this->product_class_model->get($code);

    $this->load->view('masters/product_class/product_class_edit', $rs);
  }


  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = $this->input->post('name');

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_class_model->is_exists($name, $code))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->product_class_model->update($code, ['name' => trim($name)]))
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

    $this->_response($sc);
  }



  public function delete()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));

    if( ! empty($code))
    {
      if($this->pm->can_delete)
      {
        if( ! empty($code))
        {
          if( ! $this->product_class_model->delete($code))
          {
            set_error('delete');
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
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function clear_filter()
	{
		$filter = array('code', 'name');
    return clear_filter($filter);
	}

}//--- end class
 ?>
