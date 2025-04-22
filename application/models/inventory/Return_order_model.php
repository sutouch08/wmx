<?php
class Return_order_model extends CI_Model
{
  private $tb = "return_order";
  private $td = "return_order_detail";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_total_return($code)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('return_code', $code)
    ->get($this->td);

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function update_details($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('return_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('r.*')
    ->select('c.name AS customer_name')
    ->select('wh.name AS warehouse_name, z.name AS zone_name')
    ->select('u.uname, u.name AS display_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('warehouse AS wh', 'r.warehouse_code = wh.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('r.code', $code)
    ->get();

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


  public function get_details($code)
  {
    $rs = $this->db->where('return_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_count_item_details($code)
  {
    $rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 1)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_non_count_details($code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 0)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
	}


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->where('rd.product_code', $product_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_invoice_details($invoice)
  {
    $rs = $this->db
    ->select('reference AS order_code, product_code, product_name, price, sell AS sell_price, discount_label AS discount, discount_amount')
    ->select_sum('qty')
    ->where('reference', $invoice)
    ->group_by('product_code')
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_total_return_vat($code)
  {
    $rs = $this->db
    ->select_sum('vat_amount', 'amount')
    ->where('return_code', $code)
    ->get($this->td);

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function get_customer_invoice($invoice)
  {
    $rs = $this->db
    ->select('c.code AS customer_code, c.name AS customer_name')
    ->from('orders AS o')
    ->join('customers AS c', 'o.customer_code = c.code', 'left')
    ->where('o.code', $invoice)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete($this->td);
  }


  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('return_code', $code)->update($this->td);
  }


  //--- จำนวนรวมของสินค้าที่เคยคืนไปแล้ว ในใบกำกับนี้
  public function get_returned_qty($invoice, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('invoice_code', $invoice)
    ->where('product_code', $product_code)
		->where('is_cancle', 0)
    ->get($this->td);

    return get_zero($rs->row()->qty);
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('return_code', $code)
    ->get($this->td);

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')
    ->where('return_code', $code)
    ->get($this->td);

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update($this->tb);
  }


  public function approve($code)
  {
    $arr = array('is_approve' => 1, 'approver' => get_cookie('uname'));
    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function unapprove($code)
  {
    $arr = array('is_approve' => 0, 'approver' => NULL);
    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db
      ->group_start()
      ->like('r.invoice', $ds['invoice'])
      ->or_like('r.bill_code', $ds['invoice'])
      ->group_end();
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }


		if(isset($ds['api']) && $ds['api'] !== 'all')
		{
			$this->db->where('r.api', $ds['api']);
		}


    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] !== 'all')
    {
      $this->db->where('r.is_pos_api', $ds['is_pos_api']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, c.name AS customer_name, z.name AS zone_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db
      ->group_start()
      ->like('r.invoice', $ds['invoice'])
      ->or_like('r.bill_code', $ds['invoice'])
      ->group_end();
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }

		if(isset($ds['api']) && $ds['api'] !== 'all')
		{
			$this->db->where('r.api', $ds['api']);
		}

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] !== 'all')
    {
      $this->db->where('r.is_pos_api', $ds['is_pos_api']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('r.code', 'DESC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_pos_ref($pos_ref)
  {
    $count = $this->db
    ->where('pos_ref', $pos_ref)
    ->where('status !=', 2)
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function customer_in($txt)
  {
    $sc = array('0');

    $rs = $this->db->select('code')->like('code', $txt)->or_like('name', $txt)->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rs)
      {
        $sc[] = $rs->code;
      }
    }

    return $sc;
  }

}

 ?>
