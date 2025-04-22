<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee extends PS_Controller
{
  public $menu_code = 'DBEMPL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข พนักงาน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/employee';
    $this->load->model('masters/employee_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'emp_code', ''),
      'name' => get_filter('name', 'emp_name', ''),
      'active' => get_filter('active', 'emp_active', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->employee_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$emps = $this->employee_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $emps;

		$this->pagination->initialize($init);
    $this->load->view('masters/employee/employee_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/employee/employee_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
    {
      if($this->employee_model->is_exists($ds->code))
      {
        $sc = FALSE;
        set_error('exists', $ds->code);
      }

      if($sc === TRUE && $this->employee_model->is_exists_name($ds->name))
      {
        $sc = FALSE;
        set_error('exists', $ds->name);
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => $ds->code,
          'name' => $ds->name,
          'active' => $ds->active,
          'user' => $this->_user->uname
        );

        if( ! $this->employee_model->add($arr))
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


  public function edit($id)
  {
    $ds = $this->employee_model->get_by_id($id);

    if( ! empty($ds))
    {
      $this->load->view('masters/employee/employee_edit', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->name))
    {
      if($this->employee_model->is_exists_name($ds->name, $ds->id))
      {
        $sc = FALSE;
        set_error('exists', $ds->name);
      }

      if($sc === TRUE)
      {
        $arr = array(
          'name' => $ds->name,
          'active' => $ds->active,
          'update_user' => $this->_user->uname
        );

        if( ! $this->employee_model->update($ds->id, $arr))
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

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if( ! empty($id))
      {
        if( ! $this->employee_model->has_transection($id))
        {
          if( ! $this->employee_model->delete($id))
          {
            $sc = FALSE;
            set_error('delete');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('transection');
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
		$filter = array('emp_code', 'emp_name', 'emp_active');
    clear_filter($filter);
		echo 'done';
	}
}

?>
