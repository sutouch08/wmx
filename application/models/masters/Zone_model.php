<?php
class Zone_model extends CI_Model
{
  private $tb = "zone";

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


  public function get_by_barcode($barcode)
  {
    $rs = $this->db->where('barcode IS NOT NULL', NULL, FALSE)->where('barcode', $barcode)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
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


  public function update($id, $ds = array())
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


  public function is_exists_code($code)
  {
    return $this->db->where('code', $code)->count_all_results($this->tb) > 0 ? TRUE : FALSE;
  }


  public function is_exists_barcode($barcode)
  {
    return $this->db->where('barcode', $barcode)->count_all_results($this->tb) > 0 ? TRUE : FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if( isset($ds['barcode']) && $ds['barcode'] != "" && $ds['barcode'] !== NULL)
    {
      $this->db->like('barcode', $ds['barcode']);
    }

    if( ! empty($ds['row']))
    {
      $this->db->where('row', $ds['row']);
    }

    if( ! empty($ds['col']))
    {
      $this->db->where('col', $ds['col']);
    }

    if( ! empty($ds['loc']))
    {
      $this->db->where('loc', $ds['loc']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if( isset($ds['barcode']) && $ds['barcode'] != "" && $ds['barcode'] !== NULL)
    {
      $this->db->like('barcode', $ds['barcode']);
    }

    if( ! empty($ds['row']))
    {
      $this->db->where('row', $ds['row']);
    }

    if( ! empty($ds['col']))
    {
      $this->db->where('col', $ds['col']);
    }

    if( ! empty($ds['loc']))
    {
      $this->db->where('loc', $ds['loc']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db
    ->order_by('warehouse_code', 'ASC')
    ->order_by('code', 'ASC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_warehouse_code($id)
  {
    $rs = $this->db->select('warehouse_code')->where('id', $id)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->warehouse_code;
    }

    return NULL;
  }


  public function get_warehouse_id($id)
  {
    $rs = $this->db->select('warehouse_id')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->warehouse_id;
    }

    return NULL;
  }


  public function get_code($id)
  {
    $rs = $this->db->select('code')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('name')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_zone_in_warehouse($warehouse_id)
  {
    $rs = $this->db->where('warehouse_id', $warehouse_id)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function has_transection($id)
  {
    return FALSE;
  }
} //--- end class

 ?>
