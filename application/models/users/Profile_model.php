<?php
class Profile_model extends CI_Model
{
  private $tb = "profile";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_all()
  {
    $rs = $this->db->order_by('name', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
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


  public function is_exists($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_members($id)
  {
    return $this->db->where('id_profile', $id)->count_all_results('user');
  }


  public function get_profile($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->distinct()
    ->select('p.*')
    ->from('profile AS p')
    ->join('permission AS pm', 'pm.id_profile = p.id', 'left')
    ->join('menu AS m', 'pm.menu = m.code', 'left')
    ->where('p.id >', 0)
    ->where('p.name IS NOT NULL', NULL, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('p.name', $ds['name']);
    }

    if(!empty($ds['menu']) && $ds['menu'] != 'all')
    {
      $this->db->where_in('pm.menu', $ds['menu']);
    }

    if(!empty($ds['permission']) && $ds['permission'] != 'all')
    {
      if($ds['permission'] == 'view')
      {
        $this->db->where('pm.can_view', 1);
      }

      if($ds['permission'] == 'add')
      {
        $this->db->where('pm.can_add', 1);
      }

      if($ds['permission'] == 'edit')
      {
        $this->db->where('pm.can_edit', 1);
      }

      if($ds['permission'] == 'delete')
      {
        $this->db->where('pm.can_delete', 1);
      }

      if($ds['permission'] == 'approve')
      {
        $this->db->where('pm.can_approve', 1);
      }
    }

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows($ds = array())
  {
    $this->db
    ->distinct()
    ->select('p.*')
    ->from('profile AS p')
    ->join('permission AS pm', 'pm.id_profile = p.id', 'left')
    ->join('menu AS m', 'pm.menu = m.code', 'left')
    ->where('p.id >', 0)
    ->where('p.name IS NOT NULL', NULL, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('p.name', $ds['name']);
    }

    if(!empty($ds['menu']) && $ds['menu'] != 'all')
    {
      $this->db->where_in('pm.menu', $ds['menu']);
    }

    if(!empty($ds['permission']) && $ds['permission'] != 'all')
    {
      if($ds['permission'] == 'view')
      {
        $this->db->where('pm.can_view', 1);
      }

      if($ds['permission'] == 'add')
      {
        $this->db->where('pm.can_add', 1);
      }

      if($ds['permission'] == 'edit')
      {
        $this->db->where('pm.can_edit', 1);
      }

      if($ds['permission'] == 'delete')
      {
        $this->db->where('pm.can_delete', 1);
      }

      if($ds['permission'] == 'approve')
      {
        $this->db->where('pm.can_approve', 1);
      }
    }

    return $this->db->count_all_results();
  }

} //--- End class


 ?>
