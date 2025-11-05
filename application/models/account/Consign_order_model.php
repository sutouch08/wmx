<?php
class Consign_order_model extends CI_Model
{
  private $tb = "consign_order";
  private $td = "consign_order_detail";

  public function __construct()
  {
    parent::__construct();
  }


  public function add($ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function add_detail($ds = array())
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


  public function update($code, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function recal_summary($code)
  {
    $qr  = "UPDATE consign_order ";
    $qr .= "SET DocTotal = (SELECT SUM(amount) FROM consign_order_detail WHERE consign_code = '{$code}'), ";
    $qr .= "TotalQty = (SELECT SUM(qty) FROM consign_order_detail WHERE consign_code = '{$code}') ";
    $qr .= "WHERE code = '{$code}'";

    return $this->db->query($qr);
  }


  public function update_details($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('consign_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_ref_code($code, $check_code)
  {
    return $this->db->set('ref_code', $check_code)->where('code', $code)->update($this->tb);
  }


  public function drop_import_details($code, $check_code)
  {
    return $this->db->where('consign_code', $code)->where('ref_code', $check_code)->delete($this->td);
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


  public function get_consign_details($code)
  {
    $rs = $this->db->where('consign_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('consign_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
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


  public function get_exists_detail($code, $product_code, $price, $discountLabel, $input_type = 1)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('discount', $discountLabel)
    ->where('input_type', $input_type)
    ->where('status', 'O')
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function remove_rows(array $ids = array())
  {
    if( ! empty($ids))
    {
      return $this->db->where_in('id', $ids)->delete($this->td);
    }

    return FALSE;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function drop_details($code)
  {
    return $this->db->where('consign_code', $code)->delete($this->td);
  }


  public function get_sum_order_qty($code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
    }

    return 0;
  }


  public function get_unsave_qty($code, $product_code, $price, $discount)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price, FALSE)
    ->where('discount', $discount)
    ->where('status', 'O')
    ->get($this->td);

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function get_commit_qty($product_code, $warehouse_code)
  {
    $rs = $this->db
    ->select_sum('d.qty')
    ->from("{$this->td} as d")
    ->join("{$this->tb} as o", 'd.consign_code = o.code', 'left')
    ->where('d.status', 'O')
    ->where('d.product_code', $product_code)
    ->where_in('o.status', ['P', 'A'])
    ->where('o.warehouse_code', $warehouse_code)
    ->group_by('d.product_code')
    ->get();

    if($rs->num_rows() === 1)
    {
      return floatval($rs->row()->qty);
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->group_end();
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    $rs = $this->db
    ->order_by('code', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->group_end();
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    return $this->db->count_all_results($this->tb);
  }



  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM consign_order WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }


  public function is_exists($code, $old_code = NULL)
  {
    if($old_code !== NULL)
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

} //--- end class
?>
