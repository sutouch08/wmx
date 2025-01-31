<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends PS_Controller {
	public $menu_code = 'SCPERM';
	public $menu_group_code = 'SC';
	public $title = 'Permission';
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/permission';
    $this->load->model('users/profile_model');
    $this->load->model('users/permission_model');
  }


  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'profileNam', ''),
			'menu' => get_filter('menu', 'menux', 'all'),
			'permission' => get_filter('permission', 'permission', 'all')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			$perpage = get_rows();
			$rows = $this->profile_model->count_rows($filter);
			$filter['list'] = $this->profile_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
			$this->pagination->initialize($init);
			$this->load->view('users/permission_list', $filter);
		}
  }


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$data = array(
				'menus' => array()
			);

			$groups = $this->menu->get_menu_groups();

			if( ! empty($groups))
			{
				foreach($groups as $group)
	      {
					if($group->pm)
					{
						$ds = array(
							'group_code' => $group->code,
							'group_name' => $group->name,
							'menu' => ''
						);

						$menus = $this->menu->get_menus_by_group($group->code);

						if( ! empty($menus))
						{
							$item = array();

							foreach($menus as $menu)
							{
								if($menu->valid)
								{
									$arr = array(
										'menu_code' => $menu->code,
										'menu_name' => $menu->name
									);

									array_push($item, $arr);
								}
							}

							$ds['menu'] = $item;
						}

						array_push($data['menus'], $ds);
					}
	      }
			}

			$this->load->view('users/permission_add', $data);
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$ds = json_decode($this->input->post('data'));

			if( ! empty($ds))
			{
				if( ! $this->profile_model->is_exists($ds->name))
				{
					$arr = array(
						'name' => $ds->name,
						'update_by' => $this->_user->uname
					);

					$id = $this->profile_model->add($arr);

					if($id)
					{
						if( ! empty($ds->menu))
						{
							foreach($ds->menu as $rs)
							{
								$pm = array(
									'menu' => $rs->code,
									'id_profile' => $id,
									'can_view' => $rs->view,
									'can_add' => $rs->add,
									'can_edit' => $rs->edit,
									'can_delete' => $rs->delete,
									'can_approve' => $rs->approve
								);

								$this->permission_model->add($pm);
							}
						}
					}
					else
					{
						$sc = FALSE;
						set_error('insert');
					}
				}
				else
				{
					$sc = FALSE;
					set_error('exists', $ds->name);
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

		$this->_json_response($sc);
	}


	public function edit($id)
	{
		if($this->pm->can_edit)
		{
			$pf = $this->profile_model->get($id);

			if( ! empty($pf))
			{
				$data = array(
					'id' => $pf->id,
					'name' => $pf->name,
					'menus' => []
				);

				$groups = $this->menu->get_menu_groups();

				if( ! empty($groups))
				{
					foreach($groups as $group)
					{
						if($group->pm)
						{
							$ds = array(
								'group_code' => $group->code,
								'group_name' => $group->name,
								'menu' => []
							);

							$menus = $this->menu->get_menus_by_group($group->code);

							if( ! empty($menus))
							{
								foreach($menus as $menu)
								{
									if($menu->valid)
									{
										$ds['menu'][] = (object) array(
											'menu_code' => $menu->code,
											'menu_name' => $menu->name,
											'permission' => $this->permission_model->get_permission($menu->code, $id)
										);
									}
								}
							}

							$data['menus'][] = (object) $ds;
						}
					}
				}

				$this->load->view('users/permission_edit', $data);
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
				if( ! $this->profile_model->is_exists($ds->name, $ds->id))
				{
					$this->db->trans_begin();

					$arr = array(
						'name' => $ds->name,
						'date_update' => now(),
						'update_by' => $this->_user->uname
					);

					if( ! $this->profile_model->update($ds->id, $arr))
					{
						$sc = FALSE;
						set_error('update');
					}

					if($sc === TRUE)
					{
						if( ! $this->permission_model->drop_profile_permission($ds->id))
						{
							$sc = FALSE;
							$this->error = "Failed to remove previous permission";
						}

						if($sc === TRUE)
						{
							if( ! empty($ds->menu))
							{
								foreach($ds->menu as $rs)
								{
									$pm = array(
										'menu' => $rs->code,
										'id_profile' => $ds->id,
										'can_view' => $rs->view,
										'can_add' => $rs->add,
										'can_edit' => $rs->edit,
										'can_delete' => $rs->delete,
										'can_approve' => $rs->approve
									);

									$this->permission_model->add($pm);
								}
							}
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
					}
					else
					{
						$this->db->trans_rollback();
					}
				}
				else
				{
					$sc = FALSE;
					set_error('exists', $ds->name);
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

		$this->_json_response($sc);
	}


	public function view_detail($id)
	{
		$pf = $this->profile_model->get($id);

		if( ! empty($pf))
		{
			$data = array(
				'id' => $pf->id,
				'name' => $pf->name,
				'menus' => []
			);

			$groups = $this->menu->get_menu_groups();

			if( ! empty($groups))
			{
				foreach($groups as $group)
				{
					if($group->pm)
					{
						$ds = array(
							'group_code' => $group->code,
							'group_name' => $group->name,
							'menu' => []
						);

						$menus = $this->menu->get_menus_by_group($group->code);

						if( ! empty($menus))
						{
							foreach($menus as $menu)
							{
								if($menu->valid)
								{
									$ds['menu'][] = (object) array(
										'menu_code' => $menu->code,
										'menu_name' => $menu->name,
										'permission' => $this->permission_model->get_permission($menu->code, $id)
									);
								}
							}
						}

						$data['menus'][] = (object) $ds;
					}
				}
			}

			$this->load->view('users/permission_detail', $data);
		}
		else
		{
			$this->page_error();
		}
	}


	public function delete()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if($this->pm->can_delete)
		{
			$this->db->trans_begin();

			if( ! $this->profile_model->delete($id))
			{
				$sc = FALSE;
				set_error('delete');
			}

			if($sc === TRUE)
			{
				if( ! $this->permission_model->drop_profile_permission($id))
				{
					$sc = FALSE;
					$this->error = "Failed to remove permission";
				}
			}

			if($sc === TRUE)
			{
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
			}
		}
		else
		{
			$sc = FALSE;
			set_error('permission');
		}

		$this->_response($sc);
	}


	public function save_profile_permission()
	{
		if($this->input->post('id_profile'))
		{
			$id_profile = $this->input->post('id_profile');
			$menu = $this->input->post('menu');
			$view = $this->input->post('view');
			$add = $this->input->post('add');
			$edit = $this->input->post('edit');
			$delete = $this->input->post('delete');
			$approve = $this->input->post('approve');

			$this->permission_model->drop_profile_permission($id_profile);

			if(!empty($menu))
			{
				foreach($menu as $code)
				{
					$pm = array(
						'menu' => $code,
						'uid' => NULL,
						'id_profile' => $id_profile,
						'can_view' => isset($view[$code]) ? 1 : 0,
						'can_add' => isset($add[$code]) ? 1 : 0,
						'can_edit' => isset($edit[$code]) ? 1 : 0,
						'can_delete' => isset($delete[$code]) ? 1 : 0,
						'can_approve' => isset($approve[$code]) ? 1 : 0
					);

					$this->permission_model->add($pm);
				}
			}

			set_message('Done!');
			redirect($this->home.'/edit_permission/'.$id_profile);
		}
	}






  public function clear_filter()
  {
		$filter = array('profileName', 'menux', 'permission');
    clear_filter($filter);
    echo 'done';
  }

} //-- end class
  ?>
