<?php
class User_model extends CI_Model
{
  private $tb = "user";

  public function __construct()
  {
    parent::__construct();
  }

  public function get($uname)
  {
    $rs = $this->db->where('uname', $uname)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_user_by_uid($uid)
  {
    $rs = $this->db->where('uid', $uid)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all($all = TRUE)
  {
    $this->db
    ->select('u.*')
    ->select('p.name AS profile_name')
    ->from('user AS u')
    ->join('profile AS p', 'u.id_profile = p.id', 'left');

    if( ! $all)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->order_by('u.uname', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function get_name($uname)
  {
    $rs = $this->db->where('uname', $uname)->get('user');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('u.*, p.name AS profile_name')
    ->from('user AS u')
    ->join('profile AS p', 'u.id_profile = p.id', 'left')
    ->where('u.id >', 0);

    if( ! empty($ds['uname']))
    {
      $this->db->like('u.uname', $ds['uname']);
    }

    if( ! empty($ds['dname']))
    {
      $this->db->like('u.name', $ds['dname']);
    }

    if( ! empty($ds['profile']) && $ds['profile'] != 'all')
    {
      $this->db->where('u.id_profile', $ds['profile']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('u.active', $ds['active']);
    }

    $rs = $this->db->order_by('u.uname', 'DESC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    $this->db->where('id >', 0);

    if( ! empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if( ! empty($ds['dname']))
    {
      $this->db->like('name', $ds['dname']);
    }

    if( ! empty($ds['profile']) && $ds['profile'] != 'all')
    {
      $this->db->where('id_profile', $ds['profile']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_permission($menu, $uid, $id_profile)
  {
    if( ! empty($menu))
    {
      $rs = $this->db->where('code', $menu)->get('menu');

      if($rs->num_rows() === 1)
      {
        if($rs->row()->valid == 1)
        {
					if($id_profile == -987654321)
					{
						$ds = new stdClass();
	          $ds->can_view = 1;
	          $ds->can_add = 1;
	          $ds->can_edit = 1;
	          $ds->can_delete = 1;
	          $ds->can_approve = 1;
	          return $ds;
					}
					else
					{
						return $this->get_profile_permission($menu, $id_profile);
					}
        }
        else
        {
          $ds = new stdClass();
          $ds->can_view = 1;
          $ds->can_add = 1;
          $ds->can_edit = 1;
          $ds->can_delete = 1;
          $ds->can_approve = 1;
          return $ds;
        }
      }

    }

    return FALSE;
  }


  private function get_user_permission($menu, $uid)
  {
    $rs = $this->db->where('menu', $menu)->where('uid', $uid)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }


  private function get_profile_permission($menu, $id_profile)
  {
    $rs = $this->db->where('menu', $menu)->where('id_profile', $id_profile)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }


  public function is_exists_uname($uname, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('uname', $uname)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_user_credentials($uname)
  {
    $rs = $this->db->where('uname', $uname)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function change_password($id, $pwd)
  {
    return $this->db->set('pwd', $pwd)->where('id', $id)->update($this->tb);
  }


  public function verify_uid($uid)
  {
    if( $uid != NULL)
    {
      return $this->db->where('uid', $uid)->where('active', 1)->count_all_results($this->tb) == 1 ? TRUE : FALSE;
    }

    return FALSE;
  }


	public function has_transection($uname)
	{
		//-- all orders
		if($this->db->where('user', $uname)->count_all_results('orders') > 0)
		{
			return TRUE;
		}

		//--- Receive product
		if($this->db->where('user', $uname)->count_all_results('receive_product') > 0)
		{
			return TRUE;
		}

		//--- Receive transform
		if($this->db->where('user', $uname)->count_all_results('receive_transform') > 0)
		{
			return TRUE;
		}

		//--- Return order
		if($this->db->where('user', $uname)->count_all_results('return_order') > 0)
		{
			return TRUE;
		}

		//--- Transfer
		if($this->db->where('user', $uname)->count_all_results('transfer') > 0)
		{
			return TRUE;
		}

		//--- Move
		if($this->db->where('user', $uname)->count_all_results('move') > 0)
		{
			return TRUE;
		}

		//--- WD
		if($this->db->where('user', $uname)->count_all_results('consignment_order') > 0)
		{
			return TRUE;
		}

		//--- WM
		if($this->db->where('user', $uname)->count_all_results('consign_order') > 0)
		{
			return TRUE;
		}

		//--- AJ
		if($this->db->where('user', $uname)->count_all_results('adjust') > 0)
		{
			return TRUE;
		}

		//--- AC
		if($this->db->where('user', $uname)->count_all_results('adjust_consignment') > 0)
		{
			return TRUE;
		}

		//--- WG
		if($this->db->where('user', $uname)->count_all_results('adjust_transform') > 0)
		{
			return TRUE;
		}

		//--- WX
		if($this->db->where('user', $uname)->count_all_results('consign_check') > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


} //---- End class

 ?>
