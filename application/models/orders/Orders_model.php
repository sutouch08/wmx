<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Orders_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_details";

  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('orders.*, payment_method.role AS payment_role')
    ->from('orders')
    ->join('payment_method', 'orders.payment_code = payment_method.code', 'left')
    ->where('orders.code', $code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


	public function get_cancel_reason($code)
	{
		$rs = $this->db->where('code', $code)->get('order_cancel_reason');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
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


	public function add_cancel_reason(array $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->db->insert('order_cancel_reason', $ds);
		}

		return FALSE;
	}


  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update($this->td, $ds);
  }


  public function update_details($code, array $ds = array())
  {
    return $this->db->where('order_code', $code)->update($this->td, $ds);
  }


  public function remove_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function remove_all_details($order_code)
  {
    return $this->db->where('order_code', $order_code)->delete($this->td);
  }


  public function remove_details_by_ids(array $ids = array())
  {
    if( ! empty($ids))
    {
      return $this->db->where_in('id', $ids)->delete($this->td);
    }

    return FALSE;
  }


	public function log_delete(array $ds = array())
	{
		return $this->db->insert('order_delete_logs', $ds);
	}


  public function is_exists_detail($order_code, $item_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get($this->td);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_order($code, $old_code = NULL)
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


  public function get_detail_id($order_code, $item_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- get exclude detail_ids to delete
  public function get_exclude_details_ids($order_code, array $ids = array())
  {
    if( ! empty($ids))
    {
      $rs = $this->db
      ->select('id')
      ->where('order_code', $order_code)
      ->where_not_in('id', $ids)
      ->get($this->td);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

  public function get_exists_detail($order_code, $item_code, $price)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('price', $price)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_exists_free_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('is_free', 1)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_unvalid_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('valid', 0)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_unvalid_qc_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('valid_qc', 0)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- use in prepare to check item is exists in order or not and get sum of qty
  public function get_sum_item_qty($order_code, $item_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_by('product_code')
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


	public function get_order_uncount_details($order_code)
	{
		$rs = $this->db
		->where('order_code', $order_code)
		->where('is_count', 0)
		->get($this->td);

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

    return FALSE;
  }


  public function get_detail_by_product($order_code, $product_code)
  {
    $rs = $this->db->where('order_code', $order_code)->where('product_code', $product_code)->order_by('id', 'ASC')->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail_by_product_and_line_id($order_code, $product_code, $line_id)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('line_id', $line_id)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


	public function get_only_count_stock_details($order_code)
	{
		$rs = $this->db
		->select('od.*, pd.unit_code')
		->from('order_details AS od')
		->join('products AS pd', 'od.product_code = pd.code', 'left')
		->where('od.order_code', $order_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_order_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get($this->td);
    // $rs = $this->db
    // ->select('order_details.*, products.unit_code')
    // ->from('order_details')
    // ->join('products', 'order_details.product_code = products.code', 'left')
    // ->where('order_code', $code)
    // ->order_by('products.model_code', 'ASC')
    // ->order_by('products.color_code', 'ASC')
    // ->order_by('products.size_code', 'ASC')
    // ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_unvalid_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->where('valid', 0)
    ->where('is_count', 1)
    ->order_by('product_code', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_valid_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->group_start()
    ->where('valid', 1)
    ->or_where('is_count', 0)
    ->group_end()
    ->order_by('product_code', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_valid_item($id_order_detail)
  {
    $rs = $this->db
    ->where('id', $id_order_detail)
    ->where('valid', 1)
    ->get($this->td);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_invalid_item($id_order_detail)
  {
    $rs = $this->db
    ->where('id', $id_order_detail)
    ->where('valid', 0)
    ->get($this->td);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return FALSE;
  }


  //---- get order not cancel by reference เพื่อใช้กับ api ยกเลิกออเดอร์ จาก platform
  public function get_order_by_reference($reference)
  {
    $rs = $this->db->where('reference', $reference)->order_by('id', 'DESC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  //---- for dispatch TikTok
  public function get_order_by_tracking($tracking_number)
  {
    $rs = $this->db
    ->where('shipping_code IS NOT NULL', NULL, FALSE)
    ->where('shipping_code', $tracking_number)
    ->order_by('id', 'DESC')
    ->limit(1)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function get_order_by_oracle_id($oracle_id)
  {
    $rs = $this->db
    ->where('oracle_id IS NOT NULL', NULL, FALSE)
    ->where('oracle_id', $oracle_id)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->where('state !=', 9)->where('status !=', 2)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  //--- เช็คว่า reference นี้มีการเพิ่มเข้า order แล้ว และไม่ได้ยกเลิก เพื่อเพิ่มออเดอร์ใหม่โดยใช้ reference ได้
  public function is_active_order_reference($reference)
  {
    $count = $this->db
    ->where('reference IS NOT NULL', NULL, FALSE)
    ->where('reference', $reference)
    ->where('is_cancled', 0)
    ->where_in('state', [4, 5, 6, 7, 8])
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  //--- เช็คว่า reference นี้มีการเพิ่มเข้า order แล้ว และไม่ได้ยกเลิก เพื่อเพิ่มออเดอร์ใหม่โดยใช้ reference ได้
  public function is_active_order_so($so_no)
  {
    $count = $this->db
    ->where('so_no IS NOT NULL', NULL, FALSE)
    ->where('so_no', $so_no)
    ->where('is_cancled', 0)
    ->where_in('state', [4, 5, 6, 7, 8])
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  //--- เช็คว่า oracle_id นี้มีการเพิ่มเข้า order แล้ว และไม่ได้ยกเลิก เพื่อเพิ่มออเดอร์ใหม่โดยใช้ oracle_id ได้
  public function is_active_order_fulfillment($fulfillment_code)
  {
    $count = $this->db
    ->where('fulfillment_code IS NOT NULL', NULL, FALSE)
    ->where('fulfillment_code', $fulfillment_code)
    ->where('is_cancled', 0)
    ->where_in('state', [4, 5, 6, 7, 8])
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_active_order_oracle_id($oracle_id)
  {
    $count = $this->db
    ->where('oracle_id IS NOT NULL', NULL, FALSE)
    ->where('oracle_id', $oracle_id)
    ->where('is_cancled', 0)
    ->where_in('state', [4, 5, 6, 7, 8])
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_active_order_by_reference($reference)
  {
    $rs = $this->db->where('reference', $reference)->where('state <=', 7)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_order_by_so($so_no)
  {
    $rs = $this->db->where('so_no', $so_no)->where('state <=', 7)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_order_by_oracle_id($oracle_id)
  {
    $rs = $this->db
    ->where('oracle_id IS NOT NULL', NULL, FALSE)
    ->where('oracle_id', $oracle_id)
    ->where('state <=', 7)
    ->order_by('state', 'ASC')
    ->limit(1)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_order_by_fulfillment_code($fulfillment_code)
  {
    $rs = $this->db
    ->where('fulfillment_code IS NOT NULL', NULL, FALSE)
    ->where('fulfillment_code', $fulfillment_code)
    ->where('state <=', 7)
    ->order_by('state', 'ASC')
    ->limit(1)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update($this->td);
  }


  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update($this->td);
  }


  public function valid_all_details($code)
  {
    return $this->db->set('valid', 1)->where('order_code', $code)->update($this->td);
  }


  public function valid_qc($id)
  {
    return $this->db->set('valid_qc', 1)->where('id', $id)->update($this->td);
  }


  public function unvalid_qc($id)
  {
    return $this->db->set('valid_qc', 0)->where('id', $id)->update($this->td);
  }


  public function valid_all_qc_details($code)
  {
    return $this->db->set('valid_qc', 1)->where('order_code', $code)->update($this->td);
  }


  public function change_state($code, $state)
  {
    $arr = array(
      'state' => $state,
      'update_user' => get_cookie('uname')
    );

    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function update_shipping_code($code, $ship_code)
  {
    return $this->db->set('shipping_code', $ship_code)->where('code', $code)->update($this->tb);
  }


  public function set_never_expire($code, $option)
  {
    return $this->db->set('never_expire', $option)->where('code', $code)->update($this->tb);
  }


  public function un_expired($code)
  {
    $this->db->trans_start();
    $this->db->set('is_expired', 0)->where('code', $code)->update($this->tb);
    $this->db->set('is_expired', 0)->where('order_code', $code)->update($this->td);
    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }


  //---- เปิดบิลใน SAP เรียบร้อยแล้ว
  public function set_complete($code)
  {
    return $this->db->set('is_complete', 1)->where('order_code', $code)->update($this->td);
  }


  public function un_complete($code)
  {
    return $this->db->set('is_complete', 0)->where('order_code', $code)->update($this->td);
  }


  public function paid($code, $paid)
  {
    $paid = $paid === TRUE ? 1 : 0;
    return $this->db->set('is_paid', $paid)->where('code', $code)->update($this->tb);
  }


  public function count_rows(array $ds = array())
  {
    if( isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['so_no']))
    {
      $this->db->like('so_no', $ds['so_no']);
    }

    if( ! empty($ds['fulfillment_code']))
    {
      $this->db->like('fulfillment_code', $ds['fulfillment_code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference'], 'after');
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('shipping_code', $ds['ship_code']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if(isset($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if( ! empty($ds['zone_code']))
    {
      $zone = $this->zone_in($ds['zone_code']);

      if( ! empty($zone))
      {
        $this->db->where_in('zone_code', $zone);
      }
      else
      {
        $this->db->where('zone_code', 'NULL');
      }
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['state_list']))
    {
      $this->db->where_in('state', $ds['state_list']);
    }

    if(isset($ds['is_backorder']) && $ds['is_backorder'] != 'all')
    {
      $this->db->where('is_backorder', $ds['is_backorder']);
    }

    if(isset($ds['is_cancled']) && $ds['is_cancled'] != 'all')
    {
      $this->db->where('is_cancled', $ds['is_cancled']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('id, code, role, so_no, fulfillment_code, reference, customer_code, customer_name, customer_ref')
    ->select('channels_code, payment_code, state, status, warehouse_code, zone_code, date_add, is_expired, doc_total')
    ->select('is_backorder, is_cancled, budget_id, budget_code');

    if( isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['so_no']))
    {
      $this->db->like('so_no', $ds['so_no']);
    }

    if( ! empty($ds['fulfillment_code']))
    {
      $this->db->like('fulfillment_code', $ds['fulfillment_code']);
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference'], 'after');
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('shipping_code', $ds['ship_code']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if(isset($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if( ! empty($ds['zone_code']))
    {
      $zone = $this->zone_in($ds['zone_code']);

      if( ! empty($zone))
      {
        $this->db->where_in('zone_code', $zone);
      }
      else
      {
        $this->db->where('zone_code', 'NULL');
      }
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['state_list']))
    {
      $this->db->where_in('state', $ds['state_list']);
    }

	  if(isset($ds['is_backorder']) && $ds['is_backorder'] != 'all')
    {
      $this->db->where('is_backorder', $ds['is_backorder']);
    }

    if(isset($ds['is_cancled']) && $ds['is_cancled'] != 'all')
    {
      $this->db->where('is_cancled', $ds['is_cancled']);
    }

    $rs = $this->db
    ->order_by('id', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function zone_in($zone)
  {
    $ds = array();

    $qr = "SELECT code FROM zone WHERE code LIKE '%{$zone}%' OR name LIKE '%{$zone}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->code;
      }
    }

    return $ds;
  }


  private function customer_in($customer)
  {
    $ds = array();
    $customer = $this->db->escape_str($customer);

    $qr = "SELECT code FROM customers WHERE code LIKE '%{$customer}%' OR name LIKE '%{$customer}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->code;
      }
    }

    return $ds;
  }


  private function user_in($user)
  {
    $ds = array();

    $user = $this->db->escape_str($user);

    $qr = "SELECT uname FROM user WHERE uname LIKE '%{$user}%' OR name LIKE '%{$user}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->uname;
      }
    }

    return $ds;
  }


  private function getOrderStateChangeIn($state, $fromDate, $toDate, $startTime, $endTime)
  {
    $qr  = "SELECT order_code FROM order_state_change ";
    $qr .= "WHERE state = {$state} ";
    $qr .= "AND date_upd >= '{$fromDate}' ";
    $qr .= "AND date_upd <= '{$toDate}' ";
    $qr .= "AND time_upd >= '{$startTime}' ";
    $qr .= "AND time_upd <= '{$endTime}' ";

    $rs = $this->db->query($qr);

  	$sc = array();

  	if($rs->num_rows() > 0)
  	{
  		foreach($rs->result() as $row)
  		{
  			$sc[] = $row->order_code;
  		}

      return $sc;
  	}

  	return 'xx';
  }


  public function get_un_approve_list($role = 'C', $perpage = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name, empName')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', $role)
    ->where('orders.status', 1)
    ->where('orders.state <', 3)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $this->db->limit($perpage);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_un_approve_rows($role = 'C')
  {
    $this->db
    ->where('role', $role)
    ->where('status', 1)
    ->where('state <', 3)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 0);

    return $this->db->count_all_results($this->tb);
  }


  public function get_un_received_list($perpage = '', $offset = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', 'N')
    ->where('orders.status', 1)
    ->where('orders.state', 8)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 1)
    ->where('orders.is_valid', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_un_receive_rows()
  {
    $this->db
    ->where('role', 'N')
    ->where('status', 1)
    ->where('state', 8)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 1)
    ->where('is_valid', 0);

    return $this->db->count_all_results($this->tb);
  }


  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }


  public function get_order_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->where('order_code', $code)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->total_amount;
    }

    return 0;
  }


  public function get_bill_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->where('reference', $code)
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->amount;
    }

    return 0;
  }


  public function get_order_total_qty($code)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->where('order_code', $code)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function count_order_sku($code)
  {
    $count = $this->db->where('order_code', $code)->where('is_count', 1)->count_all_results('order_details');

    return $count;
  }


  public function get_reserv_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('orders.is_pre_order', 0)
    ->where('order_details.product_code', $item_code)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where('orders.warehouse_code', $warehouse);
    }

    if($zone !== NULL)
    {
      $this->db->where('orders.zone_code', $zone);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_reserv_stock_exclude($item_code, $warehouse, $order_detail_id)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('orders.is_pre_order', 0)
    ->where('order_details.product_code', $item_code)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1)
    ->where('orders.warehouse_code', $warehouse)
    ->where('order_details.id !=', $order_detail_id);

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_reserv_stock_by_style($style_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('orders.is_pre_order', 0)
    ->where('order_details.style_code', $style_code)
    ->where('order_details.is_cancle', 0)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where('warehouse_code', $warehouse);
    }
    $rs = $this->db->get();
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update($this->tb);
  }


  public function set_report_status($code, $status)
  {
    //--- NULL = not sent, 1 = sent, 3 = error;
    return $this->db->set('is_report', $status)->where('code', $code)->update($this->tb);
  }


  public function update_approver($code, $user)
  {
    return $this->db
    ->set('approver', $user)
    ->set('approve_date', now())
    ->set('is_approved', 1)
    ->where('code', $code)
    ->update($this->tb);
  }


  public function un_approver($code, $user)
  {
    return $this->db
    ->set('approver', NULL)
    ->set('approve_date', now())
    ->set('is_approved', 0)
    ->where('code', $code)
    ->update($this->tb);
  }


  public function clear_order_detail($code)
  {
    return $this->db->where('order_code', $code)->delete($this->td);
  }


	public function cancle_order_detail($code)
	{
		return $this->db->set('is_cancle', 1)->where('order_code', $code)->update($this->td);
	}


  //--- Set is_valid = 1 when transfer draft is confirmed (use in Controller inventory/transfer->confirm_receipted)
  public function valid_transfer_draft($code)
  {
    return $this->db->set('is_valid', 1)->where('code', $code)->update($this->tb);
  }


  public function set_exported($code, $status, $error)
  {
    return $this->db->set('is_exported', $status)->set('export_error', $error)->where('code', $code)->update($this->tb);
  }


  public function get_expire_list($date, array $role = array('S'))
  {
    $rs = $this->db
    ->select('code')
    ->where('date_add <', $date)
    ->where_in('role', $role)
    ->where_in('state', array(1,2))
    ->where('is_paid', 0)
    ->where('never_expire', 0)
    ->where('is_pre_order', 0)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function set_expire_order($code)
  {
    if( ! empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('code', $code)
      ->update($this->tb);
    }

    return FALSE;
  }


  public function set_expire_order_details($code)
  {
    if( ! empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('order_code', $code)
      ->update($this->td);
    }

    return FALSE;
  }


  public function get_order_tracking($code)
  {
    $rs = $this->db
    ->select('tracking_no, carton_code, courier_code, courier_name')
    ->select_sum('qty')
    ->where('order_code', $code)
    ->group_by('tracking_no')
    ->group_by('carton_code')
    ->get('order_tracking_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_tracking_list($code)
  {
    return $this->db->where('order_code', $code)->delete('order_tracking_details');
  }


  public function add_tracking(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('order_tracking_details', $ds);
    }

    return FALSE;
  }


  public function add_backlogs_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('order_backlog_details', $ds);
    }

    return FALSE;
  }


  public function drop_backlogs_list($code)
  {
    return $this->db->where('order_code', $code)->delete('order_backlog_details');
  }


  public function get_backlogs_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_backlog_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add_cancel_request(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('order_cancel_request', $ds);
    }

    return FALSE;
  }


  public function is_cancel_request($code)
  {
    $count = $this->db->where('reference', $code)->or_where('order_code', $code)->count_all_results('order_cancel_request');

    return $count > 0 ? TRUE : FALSE;
  }


  public function getUnsendTrackingList($id_sender, $limit = 100)
  {
    $rs = $this->db
    ->select('code, reference')
    ->where('role', 'S')
    ->where('channels_code', 'WRX12')
    ->where('id_sender', $id_sender)
    ->where('send_tracking IS NULL', NULL, FALSE)
    ->where('state', 8)
    ->where('reference IS NOT NULL')
    ->where('date_add >=', '2024-04-01 00:00:00')
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function has_zero_price($code)
  {
    $count = $this->db
    ->where('order_code', $code)
    ->where('price', 0)
    ->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }
} //--- End class


 ?>
