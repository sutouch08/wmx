<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends PS_Controller {
	public $menu_code = 'SCUSER'; //--- Add/Edit Users
	public $menu_group_code = 'SC'; //--- System security
	public $title = 'Users';
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/users';
		$this->load->helper('profile');
  }


	public function index()
	{
		$filter = array(
			'uname' => get_filter('uname', 'user_uname', ''),
			'dname' => get_filter('dname', 'user_name', ''),
			'profile' => get_filter('profile', 'user_profile', 'all'),
			'active' => get_filter('active', 'user_active', 'all')
		);

		$perpage = get_rows();

		$rows = $this->user_model->count_rows($filter);

		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

		$filter['user'] = $this->user_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

		$this->pagination->initialize($init);

		$this->load->view('users/users_list', $filter);
	}


  public function add_new()
  {
    $this->load->view('users/user_add');
  }


	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			if( ! $this->user_model->is_exists_uname($ds->uname))
			{
				if( ! $this->user_model->is_exists_name($ds->dname))
				{
					$arr = array(
						'uname' => $ds->uname,
						'pwd' => password_hash($ds->pwd, PASSWORD_DEFAULT),
						'name' => $ds->dname,
						'uid' => genUid(),
						'id_profile' => $ds->id_profile,
						'active' => $ds->active,
						'force_reset' => $ds->force,
						'create_by' => $this->_user->id
					);

					if( ! $this->user_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Failed to create user";
					}
				}
				else
				{
					$sc = FALSE;
					set_error('exists', $ds->dname);
				}
			}
			else
			{
				$sc = FALSE;
				set_error('exists', $ds->uname);
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_json_response($sc);
	}


	public function edit($id)
	{
		$ds = array(
			'user' => $this->user_model->get_by_id($id)
		);

		$this->load->view('users/user_edit', $ds);
	}


	public function update()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$user = $this->user_model->get_by_id($ds->id);

			if( ! empty($user))
			{
				if($user->name != $ds->dname)
				{
					if($this->user_model->is_exists_name($ds->dname, $ds->id))
					{
						$sc = FALSE;
						set_error('exists', $ds->dname);
					}
				}


				if($sc === TRUE)
				{
					$arr = array(
						'name' => $ds->dname,
						'id_profile' => $ds->id_profile,
						'active' => $ds->active,
						'date_update' => now(),
						'update_by' => $this->_user->id
					);

					if( ! $this->user_model->update($ds->id, $arr))
					{
						$sc = FALSE;
						set_error('update');
					}
				}
			}
			else
			{
				$sc = FALSE;
				set_error('notfound');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_json_response($sc);
	}


	public function reset_password($id)
	{
		$this->title = 'Reset Password';
		$ds['user'] = $this->user_model->get_by_id($id);
		$this->load->view('users/user_reset_pwd', $ds);
	}


	public function change_password()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$user = $this->user_model->get_by_id($ds->id);

			if( ! empty($user))
			{
				$arr = array(
					'pwd' => password_hash($ds->pwd, PASSWORD_DEFAULT),
					'force_reset' => $ds->force,
					'last_pass_change' => date('Y-m-d')
				);

				if( ! $this->user_model->update($ds->id, $arr))
				{
					$sc = FALSE;
					$this->error = "Failed to update password";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "User not found !";
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_json_response($sc);
	}


	public function delete_user($id)
	{
		$sc = TRUE;
		$user = $this->user_model->get_user($id);
		if(!empty($user))
		{
			if(!$this->user_model->has_transection($user->uname))
			{
				if(!$this->user_model->delete_user($id))
				{
					$sc = FALSE;
					$this->error = "Delete user failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถลบ user ได้ เนื่องจากมี transection ในระบบแล้ว";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ User ที่ต้องการลบ";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function get_user_permissions($id)
	{
		$sc = TRUE;
		$this->load->model('users/permission_model');
		$ds = array();

		$user = $this->user_model->get_by_id($id);

		if( ! empty($user))
		{
			$ds['header'] = "Permission : \"{$user->uname}\"";
			$ds['group'] = array();

			$groups = $this->menu->get_active_menu_groups();

			if( ! empty($groups))
			{
				foreach($groups as $gp)
				{
					if($gp->pm)
					{
						$menuGroup = array(
							'group_code' => $gp->code,
							'group_name' => $gp->name,
							'menu' => ''
						);

						$menus = $this->menu->get_menus_by_group($gp->code);

						if( ! empty($menus))
						{
							$item = array();

							foreach($menus as $menu)
							{
								if($menu->valid)
								{
									$pm = $this->permission_model->get_permission($menu->code, $user->id_profile);

									$arr = array(
										'menu_code' => $menu->code,
										'menu_name' => $menu->name,
										'cv' => $pm->can_view ? 1 : 0,
										'ca' => $pm->can_add ? 1 : 0,
										'ce' => $pm->can_edit ? 1 : 0,
										'cd' => $pm->can_delete ? 1 : 0,
										'cp' => $pm->can_approve ? 1 : 0
									);

									array_push($item, $arr);
								}

							}

							$menuGroup['menu'] = $item;
						}

						array_push($ds['group'], $menuGroup);
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid user id";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	public function export_permission()
	{
		$this->load->model('users/permission_model');
		$this->load->model('users/profile_model');
		$token = $this->input->post('token');
		$id = $this->input->post('user_id');

		$user = $this->user_model->get_user($id);
		$uname = empty($user) ? 'no data' : $user->uname;

    //--- load excel library
    $this->load->library('excel');
    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle($uname);

		if( ! empty($user))
		{
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('30');
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('15');
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('15');
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('15');
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('15');
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('15');

			$this->excel->getActiveSheet()->setCellValue('A1', 'User : ')->getStyle('A1')->getAlignment()->setHorizontal('right');
			$this->excel->getActiveSheet()->setCellValue('B1', $user->uname);
			$this->excel->getActiveSheet()->mergeCells('B1:C1');

			$this->excel->getActiveSheet()->setCellValue('D1', 'Display name : ')->getStyle('D1')->getAlignment()->setHorizontal('right');
			$this->excel->getActiveSheet()->setCellValue('E1', $user->name);
			$this->excel->getActiveSheet()->mergeCells('E1:F1');

			$this->excel->getActiveSheet()->setCellValue('A2', 'Profile : ')->getStyle('A2')->getAlignment()->setHorizontal('right');
			$this->excel->getActiveSheet()->setCellValue('B2', $this->profile_model->get_name($user->id_profile));
			$this->excel->getActiveSheet()->mergeCells('B2:C2');
			$this->excel->getActiveSheet()->setCellValue('D2', "Status : ")->getStyle('D2')->getAlignment()->setHorizontal('right');
			$this->excel->getActiveSheet()->setCellValue('E2', ($user->active == 1 ? 'Active' : 'Inactive'));
			$this->excel->getActiveSheet()->mergeCells('E2:F2');

			$row = 4;


			$groups = $this->menu->get_active_menu_groups();

			if( ! empty($groups))
			{
				foreach($groups as $gp)
				{
					if($gp->pm)
					{
						$this->excel->getActiveSheet()->setCellValue("A{$row}", $gp->name);
						$this->excel->getActiveSheet()->setCellValue("B{$row}", 'ดู');
						$this->excel->getActiveSheet()->setCellValue("C{$row}", 'เพิ่ม');
						$this->excel->getActiveSheet()->setCellValue("D{$row}", 'แก้ไข');
						$this->excel->getActiveSheet()->setCellValue("E{$row}", 'ลบ');
						$this->excel->getActiveSheet()->setCellValue("F{$row}", 'อนุมัติ');


						$color = array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'startcolor' => array('rgb' => 'F28A8C')
						);

						$this->excel->getActiveSheet()->getStyle("A{$row}:F{$row}")->getFill()->applyFromArray($color);

						$row++;

						$menus = $this->menu->get_menus_by_group($gp->code);

						if( ! empty($menus))
						{
							foreach($menus as $menu)
							{
								if($menu->valid)
								{
									$pm = $this->permission_model->get_permission($menu->code, $user->id_profile);

									$this->excel->getActiveSheet()->setCellValue("A{$row}", $menu->name);
									$this->excel->getActiveSheet()->setCellValue("B{$row}", ($pm->can_view ? 'Y' : '-'));
									$this->excel->getActiveSheet()->setCellValue("C{$row}", ($pm->can_add ? 'Y' : '-'));
									$this->excel->getActiveSheet()->setCellValue("D{$row}", ($pm->can_edit ? 'Y' : '-'));
									$this->excel->getActiveSheet()->setCellValue("E{$row}", ($pm->can_delete ? 'Y' : '-'));
									$this->excel->getActiveSheet()->setCellValue("F{$row}", ($pm->can_approve ? 'Y' : '-'));

									$row++;
								}
							}
						}
					} //-- endif
				} //--- end foreach

				if($row > 3)
				{
					$this->excel->getActiveSheet()->getStyle("B3:F{$row}")->getAlignment()->setHorizontal('center');
				}
			} //--- endif group
		}

		setToken($token);
    $file_name = "{$uname} Permission.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
	}



	public function export_all_permission()
	{
		$this->load->model('users/permission_model');
		$this->load->model('users/profile_model');
		$token = $this->input->post('alltoken');
		$all = $this->input->post('all') == 1 ? TRUE : FALSE;

		$users = $this->user_model->get_all($all);

		$ds = array();

		$groups = $this->menu->get_active_menu_groups();

		if( ! empty($groups))
		{
			foreach($groups as $group)
			{
				if($group->pm)
				{
					$arr = array(
						'name' => $group->name,
						'menus' => NULL
					);

					$menus = $this->menu->get_menus_by_group($group->code);

					if( ! empty($menus))
					{
						$items = array();

						foreach($menus as $menu)
						{
							if($menu->valid)
							{
								$items[] = array(
									'code' => $menu->code,
									'name' => $menu->name
								);
							}
						}

						$arr['menus'] = $items;
					}
				}

				$ds[] = $arr;
			}

		}


    //--- load excel library
    $this->load->library('excel');

		if( ! empty($users))
		{
			$index = 0;

			foreach($users as $user)
			{
				$worksheet = new PHPExcel_Worksheet($this->excel, $user->uname);
				$this->excel->addSheet($worksheet, $index);
				$this->excel->setActiveSheetIndex($index);
				$tabColor = $user->active == 1 ? '54c784' : 'c96b65';
				$this->excel->getActiveSheet()->getTabColor()->setARGB($tabColor);

				$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('30');
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('15');
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('15');
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('15');
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('15');

				$this->excel->getActiveSheet()->setCellValue('A1', 'User : ')->getStyle('A1')->getAlignment()->setHorizontal('right');
				$this->excel->getActiveSheet()->setCellValue('B1', $user->uname);
				$this->excel->getActiveSheet()->mergeCells('B1:C1');

				$this->excel->getActiveSheet()->setCellValue('D1', 'Display name : ')->getStyle('D1')->getAlignment()->setHorizontal('right');
				$this->excel->getActiveSheet()->setCellValue('E1', $user->name);
				$this->excel->getActiveSheet()->mergeCells('E1:F1');

				$this->excel->getActiveSheet()->setCellValue('A2', 'Profile : ')->getStyle('A2')->getAlignment()->setHorizontal('right');
				$this->excel->getActiveSheet()->setCellValue('B2', $this->profile_model->get_name($user->id_profile));
				$this->excel->getActiveSheet()->mergeCells('B2:C2');
				$this->excel->getActiveSheet()->setCellValue('D2', "Status : ")->getStyle('D2')->getAlignment()->setHorizontal('right');
				$this->excel->getActiveSheet()->setCellValue('E2', ($user->active == 1 ? 'Active' : 'Inactive'));
				$this->excel->getActiveSheet()->mergeCells('E2:F2');

				$row = 4;

				if( ! empty($ds))
				{
					foreach($ds as $rs)
					{
						$this->excel->getActiveSheet()->setCellValue("A{$row}", $rs['name']);
						$this->excel->getActiveSheet()->setCellValue("B{$row}", 'ดู');
						$this->excel->getActiveSheet()->setCellValue("C{$row}", 'เพิ่ม');
						$this->excel->getActiveSheet()->setCellValue("D{$row}", 'แก้ไข');
						$this->excel->getActiveSheet()->setCellValue("E{$row}", 'ลบ');
						$this->excel->getActiveSheet()->setCellValue("F{$row}", 'อนุมัติ');


						$color = array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'startcolor' => array('rgb' => 'F28A8C')
						);

						$this->excel->getActiveSheet()->getStyle("A{$row}:F{$row}")->getFill()->applyFromArray($color);

						$row++;

						$menus = $rs['menus'];

						if( ! empty($menus))
						{
							foreach($menus as $menu)
							{
								$pm = $this->permission_model->get_permission($menu['code'], $user->id_profile);

								$this->excel->getActiveSheet()->setCellValue("A{$row}", $menu['name']);
								$this->excel->getActiveSheet()->setCellValue("B{$row}", ($pm->can_view ? 'Y' : '-'));
								$this->excel->getActiveSheet()->setCellValue("C{$row}", ($pm->can_add ? 'Y' : '-'));
								$this->excel->getActiveSheet()->setCellValue("D{$row}", ($pm->can_edit ? 'Y' : '-'));
								$this->excel->getActiveSheet()->setCellValue("E{$row}", ($pm->can_delete ? 'Y' : '-'));
								$this->excel->getActiveSheet()->setCellValue("F{$row}", ($pm->can_approve ? 'Y' : '-'));

								$row++;
							}
						}
					} //--- end foreach

					if($row > 4)
					{
						$this->excel->getActiveSheet()->getStyle("B3:F{$row}")->getAlignment()->setHorizontal('center');
					}
				} //--- endif group

				$index++;
			}
		}

		setToken($token);
    $file_name = "Users Permission.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
	}



	public function clear_filter()
	{
		return clear_filter(['user_uname', 'user_name', 'user_profile', 'user_active']);
	}

}//--- end class


 ?>
