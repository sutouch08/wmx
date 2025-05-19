<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_gender extends PS_Controller
{
  public $menu_code = 'DBPDGD';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'Gender';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_gender';
    $this->load->model('masters/product_gender_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', '')
    );

    $perpage = get_rows();
		$rows = $this->product_gender_model->count_rows($filter);
		$filter['data'] = $this->product_gender_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$this->pagination->initialize($init);
    $this->load->view('masters/product_gender/product_gender_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_gender/product_gender_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = $this->input->post('name');

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_gender_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($this->product_gender_model->is_exists_name($name))
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

        if( ! $this->product_gender_model->add($ds))
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
    $rs = $this->product_gender_model->get($code);

    $this->load->view('masters/product_gender/product_gender_edit', $rs);
  }


  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $name = $this->input->post('name');

    if( ! empty($code) && ! empty($name))
    {
      if($this->product_gender_model->is_exists($name, $code))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->product_gender_model->update($code, ['name' => trim($name)]))
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
          if( ! $this->product_gender_model->delete($code))
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
