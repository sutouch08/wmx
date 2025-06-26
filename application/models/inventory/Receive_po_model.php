<?php
class Receive_po_model extends CI_Model
{
  private $tb = "receive_product";
  private $td = "receive_product_detail";

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


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('re.*, po.reference')
    ->from('receive_product AS re')
    ->join('po', 're.po_code = po.code', 'left')
    ->where('re.code', $code)
    ->get();
    // $rs = $this->db->where('code', $code)->get($this->tb);

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
    $rs = $this->db->where('receive_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_in_complete_list($code)
  {
    $rs = $this->db->where('receive_code', $code)->where('valid', 0)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_complete_list($code)
  {
    $rs = $this->db->where('receive_code', $code)->where('valid', 1)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db->where('receive_code', $code)->where('product_code', $product_code)->get($this->td);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function update_detail($id, $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->db->where('id', $id)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function update_receive_qty($id, $qty)
  {
    return $this->db->set("receive_qty", "receive_qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function update_details($code, $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->db->where('receive_code', $code)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete($this->td);
  }


	public function drop_not_valid_details($code)
	{
		return $this->db->where('receive_code', $code)->where('valid', 0)->delete($this->td);
	}


  public function cancle_details($code)
  {
    return $this->db->set('line_status', 'D')->where('receive_code', $code)->update($this->td);
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get($this->td);

    return intval($rs->row()->qty);
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get($this->td);
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }



  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update($this->tb);
  }


	public function set_cancle_reason($code, $reason)
	{
		return $this->db->set('cancle_reason', $reason)->where('code', $code)->update($this->tb);
	}


  public function count_rows(array $ds = array())
  {
    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if( ! empty($ds['po']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    //--- vender
    if( ! empty($ds['vender']))
    {
      $this->db
      ->group_start()
      ->like('vender_code', $ds['vender'])
      ->or_like('vender_name', $ds['vender'])
      ->group_end();
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['is_mobile'] === TRUE)
    {
      $this->db->where_in('status', ['O','R']);
    }
    else
    {
      if($ds['status'] !== 'all')
      {
        $this->db->where('status', $ds['statusx']);
      }
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if( ! empty($ds['po']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    //--- vender
    if( ! empty($ds['vender']))
    {
      $this->db
      ->group_start()
      ->like('vender_code', $ds['vender'])
      ->or_like('vender_name', $ds['vender'])
      ->group_end();
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['is_mobile'] === TRUE)
    {
      $this->db->where_in('status', ['O','R']);
    }
    else
    {
      if($ds['status'] !== 'all')
      {
        $this->db->where('status', $ds['statusx']);
      }
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    $this->db->order_by('date_add', 'DESC');
    $this->db->order_by('code', 'DESC');

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_on_order_qty($product_code, $po_code, $po_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('po_code', $po_code)
    ->where('po_detail_id', $po_detail_id)
    ->where('product_code', $product_code)
    ->where('line_status', 'O')
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty > 0 ? $rs->row()->qty : 0;
    }

    return 0;
  }

}

 ?>
