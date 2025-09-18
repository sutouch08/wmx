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
      $this->db->insert($this->td, $ds);
      return $this->db->insert_id();
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


  public function has_saved_imported($code, $check_code)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('ref_code', $check_code)
    ->where('status', 1)
    ->limit(1)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
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



  public function get_exists_detail($code, $product_code, $price, $discountLabel, $input_type)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('discount', $discountLabel)
    ->where('input_type', $input_type)
    ->where('status', 0)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
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


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('consign_code', $code)->get($this->td);

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
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



  public function get_item_gp($product_code, $zone_code)
  {
    $rs = $this->db
    ->select('order_sold.discount_label')
    ->from('order_sold')
    ->join('orders', 'order_sold.reference = orders.code', 'left')
    ->where_in('order_sold.role', array('C', 'N'))
    ->where('orders.zone_code', $zone_code)
    ->where('order_sold.product_code', $product_code)
    ->order_by('orders.date_add', 'DESC')
    ->limit(1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->discount_label;
    }

    return 0;
  }



  public function get_unsave_qty($code, $product_code, $price, $discount, $input_type)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price, FALSE)
    ->where('discount', $discount)
    ->where('status', 0)
    ->get($this->td);

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }



  public function change_detail_status($id, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('id', $id);
    return $this->db->update($this->td);
  }

  public function change_all_detail_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('consign_code', $code);
    return $this->db->update($this->td);
  }


  public function change_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->set('inv_code', NULL)
    ->set('update_user', get_cookie('uname'))
    ->where('code', $code);
    return $this->db->update($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    //--- document date
    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']))->where('date_add <=', to_date($ds['to_date']));
    }


    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }


    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('zone_code', $ds['zone'])
      ->or_like('zone_name', $ds['zone'])
      ->group_end();
    }

    if( isset($ds['is_api']) && $ds['is_api'] != 'all')
    {
      $this->db->where('is_api', $ds['is_api']);
    }

    if( isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == '1')
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }

      if($ds['sap'] == '0')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
    }

    if(isset($ds['tax_status']) && $ds['tax_status'] != 'all')
    {
      $this->db->where('tax_status', $ds['tax_status']);
    }

    if(isset($ds['is_etax']) && $ds['is_etax'] != 'all')
    {
      $this->db->where('is_etax', $ds['is_etax']);
    }

    $this->db->order_by('date_add', 'DESC');

    if( ! empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_rows(array $ds = array())
  {
    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    //--- document date
    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']))->where('date_add <=', to_date($ds['to_date']));
    }


    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }


    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('zone_code', $ds['zone'])
      ->or_like('zone_name', $ds['zone'])
      ->group_end();
    }

    if( isset($ds['is_api']) && $ds['is_api'] != 'all')
    {
      $this->db->where('is_api', $ds['is_api']);
    }

    if( isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == '1')
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }

      if($ds['sap'] == '0')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
    }

    if(isset($ds['tax_status']) && $ds['tax_status'] != 'all')
    {
      $this->db->where('tax_status', $ds['tax_status']);
    }

    if(isset($ds['is_etax']) && $ds['is_etax'] != 'all')
    {
      $this->db->where('is_etax', $ds['is_etax']);
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


  public function is_exists_pos_ref($pos_ref)
  {
    $count = $this->db
    ->where('pos_ref', $pos_ref)
    ->where('status !=', 2)
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }



	public function get_non_inv_code($limit = 100)
	{
		$rs = $this->db
    ->select('code')
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
	}


	public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update($this->tb);
  }

} //--- end class
?>
