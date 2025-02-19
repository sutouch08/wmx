<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_model extends CI_Model
{
  private $tb = "warehouse";

  public function __construct()
  {
    parent::__construct();
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


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_name($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_name_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_code($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
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


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code'])->or_like('name', $ds['code']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( isset($ds['freeze']) && $ds['freeze'] != 'all')
    {
      $this->db->where('freeze', $ds['freeze']);
    }

    if( isset($ds['auz']) && $ds['auz'] != 'all')
    {
      $this->db->where('auz', $ds['auz']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code'])->or_like('name', $ds['code']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( isset($ds['freeze']) && $ds['freeze'] != 'all')
    {
      $this->db->where('freeze', $ds['freeze']);
    }

    if( isset($ds['auz']) && $ds['auz'] != 'all')
    {
      $this->db->where('auz', $ds['auz']);
    }

    $rs = $this->db->order_by('code', 'ASC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_zone($id)
  {
    return $this->db->where('warehouse_id', $id)->count_all_results('zone');
  }


  public function has_zone($id)
  {
    $count = $this->db->where('warehouse_id', $id)->count_all_results('zone');
    return $count > 0 ? TRUE : FALSE;
  }


  public function has_transection($id)
  {
    $zone = $this->has_zone($id);

    return $zone > 0 ? TRUE : FALSE;
  }


  public function is_exists_code($code, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    return $this->db->where('code', $code)->count_all_results($this->tb) > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    return $this->db->where('name', $name)->count_all_results($this->tb) > 0 ? TRUE : FALSE;
  }


  public function is_auz($id)
  {
    return $this->db->where('id', $id)->where('auz', 1)->count_all_results($this->tb) > 0 ? TRUE : FALSE;
  }
}
 ?>
