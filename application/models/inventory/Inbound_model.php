<?php
class Inbound_model extends CI_Model
{
  private $tb = "receive"; //--- หัวเอกสาร
  private $td = "receive_details"; //--- รายการในเอกสาร
  private $tr = "receive_transection"; //--- transection

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

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


  public function get_active_order_no($order_no)
  {
    $rs = $this->db->where('order_no', $order_no)->where('status !=', 'D')->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($receive_id)
  {
    $rs = $this->db->where('receive_id', $receive_id)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_transection($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_transections($receive_id)
  {
    $rs = $this->db->where('receive_id', $receive_id)->get($this->tr);

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
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_transection(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tr, $ds))
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


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_transection($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($receive_id)
  {
    return $this->db->where('receive_id', $receive_id)->delete($this->td);
  }


  public function delete_transection($id)
  {
    return $this->db->where('id', $id)->delete($this->tr);
  }


  public function delete_transections($receive_id)
  {
    return $this->db->where('receive_id', $receive_id)->delete($this->tr);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['order_from_date']))
    {
      $this->db->where('order_date >=', from_date($ds['order_from_date']));
    }

    if( ! empty($ds['order_to_date']))
    {
      $this->db->where('order_date <=', to_date($ds['order_to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['order_no']))
    {
      $this->db->like('order_no', $ds['order_no']);
    }

    if( ! empty($ds['ref_no1']))
    {
      $this->db->like('ref_no1', $ds['ref_no1']);
    }

    if( ! empty($ds['ref_no2']))
    {
      $this->db->like('ref_no2', $ds['ref_no2']);
    }

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! empty($ds['order_type']) && $ds['order_type'] != 'all')
    {
      $this->db->where('order_type', $ds['order_type']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['order_from_date']))
    {
      $this->db->where('order_date >=', from_date($ds['order_from_date']));
    }

    if( ! empty($ds['order_to_date']))
    {
      $this->db->where('order_date <=', to_date($ds['order_to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['order_no']))
    {
      $this->db->like('order_no', $ds['order_no']);
    }

    if( ! empty($ds['ref_no1']))
    {
      $this->db->like('ref_no1', $ds['ref_no1']);
    }

    if( ! empty($ds['ref_no2']))
    {
      $this->db->like('ref_no2', $ds['ref_no2']);
    }

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! empty($ds['order_type']) && $ds['order_type'] != 'all')
    {
      $this->db->where('order_type', $ds['order_type']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_max_code($prefix)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $prefix, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }
} //--- end class

 ?>
