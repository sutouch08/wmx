<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saleman extends PS_Controller{
	public $menu_code = 'DBSALE'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'เพิ่ม/แก้ไข พนักงานขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/saleman';
		$this->load->model('masters/slp_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'slp_name', ''),
			'active' => get_filter('active', 'slp_active', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		$segment = 4; //-- url segment
		$rows = $this->slp_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->slp_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
		$this->load->view('masters/saleman/saleman_list', $filter);
	}


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$this->load->view('masters/saleman/saleman_add');
		}
	}


	public function add()
	{
		$sc = TRUE;

		$name = trim($this->input->post('name'));
		$active = $this->input->post('active') == 0 ? 0 : 1;

		if( ! empty($name))
		{
			if($this->slp_model->is_exists_name($name))
			{
				$sc = FALSE;
				set_error('exists', $name);
			}

			if($sc === TRUE)
			{
				$arr = array(
					'name' => $name,
					'active' => $active
				);

				if( ! $this->slp_model->add($arr))
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
		if($this->pm->can_edit)
		{
			$slp = $this->slp_model->get($id);

			if( ! empty($slp))
			{
				$this->load->view('masters/saleman/saleman_edit', ['slp' => $slp]);
			}
			else
			{
				$this->error_page();
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

		$id = $this->input->post('id');
		$name = trim($this->input->post('name'));
		$active = $this->input->post('active') == 0 ? 0 : 1;

		if( ! empty($id) && ! empty($name))
		{
			if($this->slp_model->is_exists_name($name, $id))
			{
				$sc = FALSE;
				set_error('exists', $name);
			}

			if($sc === TRUE)
			{
				$arr = array(
					'name' => $name,
					'active' => $active
				);

				if( ! $this->slp_model->update($id, $arr))
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
		$sc = FALSE;

		$id = $this->input->post('id');

		if( ! empty($id))
		{
			if($this->pm->can_delete)
			{
				if( ! $this->slp_model->has_transection($id))
				{
					if( ! $this->slp_model->delete($id))
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
		$filter = array('slp_name', 'slp_active');

		return clear_filter($filter);
	}

}//--- end class


 ?>
