<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_collection extends PS_Controller
{
  public $menu_code = 'DBPDCT';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข คอลเล็คชั่น';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_collection';
    $this->load->model('masters/product_collection_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->product_collection_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['data'] = $this->product_collection_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $data = array();

    if(!empty($filter['data']))
    {
      foreach($filter['data'] as $rs)
      {
        $rs->member = $this->product_collection_model->count_members($rs->code);
      }
    }

		$this->pagination->initialize($init);

    $this->load->view('masters/product_collection/product_collection_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_collection/product_collection_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $name = trim($this->input->post('name'));

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_collection_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($sc === TRUE)
      {
        if($this->product_collection_model->is_exists_name($name))
        {
          $sc = FALSE;
          set_error('exists', $name);
        }
      }

      if($sc === TRUE)
      {
        $ds = array(
          'code' => $code,
          'name' => $name
        );

        if( ! $this->product_collection_model->add($ds))
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

    $this->_response($sc);
  }


  public function edit($code)
  {
    $data = $this->product_collection_model->get($code);

    if( ! empty($data))
    {
      $this->load->view('masters/product_collection/product_collection_edit', $data);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $name = trim($this->input->post('name'));

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_collection_model->is_exists_name($name, $code))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        $arr = array(
          'name' => $name
        );

        if( ! $this->product_collection_model->update($code, $arr))
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

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        if( ! $this->product_collection_model->delete($code))
        {
          $sc = FALSE;
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

    $this->_response($sc);
  }


  public function clear_filter()
	{
		$filter = array('code', 'name');
    return clear_filter($filter);
	}

}//--- end class
 ?>
