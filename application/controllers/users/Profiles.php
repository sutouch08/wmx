<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profiles extends PS_Controller{
	public $menu_code = 'SCPROF'; //--- Add/Edit Profile
	public $menu_group_code = 'SC'; //--- System security
	public $title = 'Profiles';
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/profiles';
    $this->load->model('users/profile_model');
  }


  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'profile_name', '')
		);

		$perpage = get_rows();
		$rows = $this->profile_model->count_rows($filter);
		$filter['list'] = $this->profile_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$this->pagination->initialize($init);

    $this->load->view('users/profile_list', $filter);
  }


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$this->load->view('users/profile_add');
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
			$name = trim($this->input->post('name'));

			if( ! empty($name))
			{
				if( ! $this->profile_model->is_extsts($name))
				{
					$arr = array(
						'name' => $name
					);

					if( ! $this->profile_model->add($arr))
					{
						$sc = FALSE;
						set_error('insert');
					}
				}
				else
				{
					$sc = FALSE;
					set_error('exists', $name);
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


  public function edit_profile($id)
  {
    $data['data'] = $this->profile_model->get_profile($id);
    $this->title = 'Edit Profile';
    $this->load->view('users/profile_edit_view', $data);
  }




  public function update_profile()
  {
    if($this->input->post('profile_id'))
    {
      $id = $this->input->post('profile_id');
      $name = $this->input->post('profileName');

      if($this->profile_model->is_extsts($name, $id) === FALSE)
      {
        if($this->profile_model->update($id, $name))
        {
          set_message('Profile updated');
        }
        else
        {
          set_error('Update profile not successfull');
        }
      }
      else
      {
        set_error("Profile '".$name."' already exists please choose another");
      }
    }
    else
    {
      set_error('Not found : profile_id');
    }

    redirect($this->home.'/edit_profile/'.$id);
  }





  public function new_profile()
  {
    if($this->input->post('profileName'))
    {
      $name = $this->input->post('profileName');

      if($this->profile_model->is_extsts($name) === FALSE)
      {
        if($this->profile_model->add($name))
        {
          set_message('Profile created successfully');
        }
      }
      else
      {
        set_error('Profile name already exists, please choose another');
        $this->session->set_flashdata('profileName', $name); //--- ไว้แสดงผลหน้าต่อไป
      }
    }
    else
    {
      set_error('Invalid profile name');
    }

    redirect($this->home.'/add_profile');
  }





  public function delete_profile($id)
  {
    if($this->profile_model->delete($id))
    {
      set_message("Profile has been deleted");
    }
    else
    {
      set_error("Failed to delete profile");
    }

    redirect($this->home);
  }




  public function clear_filter()
	{
		clear_filter('profileName');
		echo 'done';
	}
}
?>
