<?php
class Transfer_model extends CI_Model
{
  private $tb = "transfer";
  private $td = "transfer_detail";
  private $tm = "transfer_temp";

  public function __construct()
  {
    parent::__construct();
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
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('transfer_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db
		->select('td.*, pd.barcode, pd.unit_code')
		->from('transfer_detail AS td')
		->join('products AS pd', 'td.product_code = pd.code', 'left')
		->where('td.id', $id)
		->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

		return NULL;
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
      return $this->db->where('transfer_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


	public function get_detail_by_product($code, $product_code)
  {
    $rs = $this->db
		->select('td.*, pd.barcode, pd.unit_code')
		->from('transfer_detail AS td')
		->join('products AS pd', 'td.product_code = pd.code', 'left')
		->where('td.transfer_code', $code)
		->where('td.product_code', $product_code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

		return NULL;
  }


  public function get_detail_row($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->where('from_zone', $zone_code)
    ->get($this->td);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

		return NULL;
  }


  public function get_id($transfer_code, $product_code, $from_zone, $to_zone)
  {
    $rs = $this->db
    ->select('id')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('to_zone', $to_zone)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function update_wms_qty($id, $wms_qty)
  {
    return $this->db->set("wms_qty", "wms_qty + {$wms_qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function update_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      $id = $this->get_temp_id($ds['transfer_code'], $ds['product_code'], $ds['zone_code']);

      if(!empty($id))
      {
        return $this->update_temp_qty($id, $ds['qty']);
      }
      else
      {
        return $this->add_temp($ds);
      }
    }
    return FALSE;
  }


  public function get_temp_id($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_temp_row($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tm, $ds);
    }

    return FALSE;
  }


  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->tm);
  }


  public function get_transfer_temp($code)
  {
    $rs = $this->db
    ->select('transfer_temp.*, products.barcode')
    ->from($this->tm)
    ->join('products', 'products.code = transfer_temp.product_code', 'left')
    ->where('transfer_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_temp_product($code, $product_code)
  {
    $rs = $this->db
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->get($this->tm);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_qty($transfer_code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_temp_stock($product_code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $product_code)->get($this->tm);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_transfer_qty($transfer_code, $product_code, $from_zone)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('valid', 0)
    ->get($this->td);

    return intval($rs->row()->qty);
  }


  public function delete_temp($id)
  {
    return $this->db->where('id', $id)->delete($this->tm);
  }


  public function drop_zero_temp()
  {
    return $this->db->where('qty <', 1)->delete($this->tm);
  }


  public function drop_all_temp($code)
  {
    return $this->db->where('transfer_code', $code)->delete($this->tm);
  }



  public function drop_all_detail($code)
  {
    return $this->db->where('transfer_code', $code)->delete($this->td);
  }


  public function drop_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_rows(array $ds = array())
  {
    return $this->db->where_in('id', $ds)->delete($this->td);
  }


  public function is_exists($code, $old_code = NULL)
  {
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_detail($code)
  {
    $rs = $this->db->select('id')->where('transfer_code', $code)->get($this->td);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_temp($code)
  {
    $rs = $this->db->select('id')->where('transfer_code', $code)->get($this->tm);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update($this->tb);
  }



  public function valid_all_detail($code, $valid)
  {
    return $this->db->set('valid', $valid)->where('transfer_code', $code)->update($this->td);
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['to_warehouse']) && $ds['to_warehouse'] != 'all')
    {
      $this->db->where('to_warehouse', $ds['to_warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('is_export', $ds['is_export']);
    }

    if( ! empty($ds['doc_num']))
    {
      $this->db->where('DocNum IS NOT NULL', NULL, FALSE)->like('DocNum', $ds['doc_num']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['to_warehouse']) && $ds['to_warehouse'] != 'all')
    {
      $this->db->where('to_warehouse', $ds['to_warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }
  
    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('is_export', $ds['is_export']);
    }

    if( ! empty($ds['doc_num']))
    {
      $this->db->where('DocNum IS NOT NULL', NULL, FALSE)->like('DocNum', $ds['doc_num']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

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
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }

}
 ?>
