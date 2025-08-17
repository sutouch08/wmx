<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channels extends PS_Controller
{
  public $menu_code = 'DBCHAN';
	public $menu_group_code = 'DB';
	public $title = 'ช่องทางการขาย';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/channels';
    $this->load->model('masters/channels_model');
		$this->load->helper('channels');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'channels_code', ''),
      'is_online' => get_filter('is_online', 'channels_is_online', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		$rows = $this->channels_model->count_rows($filter);
    $filter['list'] = $this->channels_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$this->pagination->initialize($init);
    $this->load->view('masters/channels/channels_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/channels/channels_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if($this->channels_model->is_exists($ds->code))
      {
        $sc = FALSE;
        set_error('exists', $ds->code);
      }

      if($sc === TRUE)
      {
        if($this->channels_model->is_exists_name($ds->name))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => $ds->code,
          'name' => $ds->name,
          'is_online' => $ds->is_online,
          'position' => $ds->position,
          'user' => $this->_user->uname
        );

        if( ! $this->channels_model->add($arr))
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
    $data['data'] = $this->channels_model->get_channels($code);
    $this->load->view('masters/channels/channels_edit', $data);
  }



  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      if( ! $this->channels_model->is_exists_name($ds->name, $ds->id))
      {
        $arr = array(
          'name' => $ds->name,
          'is_online' => $ds->is_online,
          'position' => $ds->position,
          'update_user' => $this->_user->uname,
          'date_upd' => now()
        );

        if( ! $this->channels_model->update_by_id($ds->id, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "{$ds->name} already exists";
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
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        if( ! $this->channels_model->has_transection($code))
        {
          if( ! $this->channels_model->delete($code))
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


  public function toggle_online()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      $current = $this->input->post('is_online');

      $option = empty($current) ? 1 : 0;
      $arr = array(
        'is_online' => $option
      );

      if($this->pm->can_add OR $this->pm->can_edit)
      {
        if(! $this->channels_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Update failed";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "No Permission";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Channels Code not found";
    }

    echo $sc === TRUE ? $option : $this->error;
  }


  public function clear_filter()
	{
		return clear_filter(['channels_code', 'channels_is_online']);
	}

}//--- end class
 ?>
