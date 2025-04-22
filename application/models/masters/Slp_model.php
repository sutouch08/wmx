<?php
class Slp_model extends CI_Model
{
  private $tb = "saleman";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_all_slp()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all($active = NULL)
  {
    if( ! empty($active))
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->get($this->tb);

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


  public function add($ds = array())
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


  public function update($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->select('name')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }

  public function count_rows($ds = array())
  {
    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list($ds = array(), $limit = 20, $offset = 0)
  {
    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    $rs = $this->db->limit($limit, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_data(){
    $rs = $this->db->where('id > ', 0)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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


  public function has_transection($id)
  {
    $cs = $this->db->where('sale_code', $id)->count_all_results('customers');
    $od = $this->db->where('sale_code', $id)->count_all_results('orders');
    $os = $this->db->where('sale_code', $id)->count_all_results('order_sold');
    $count = $cs + $od + $os;
    return $count > 0 ? TRUE : FALSE;
  }
} //--- End class

 ?>
