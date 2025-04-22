<?php
class Buffer_model extends CI_Model
{
  private $tb = "buffer";

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


  public function get_data(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('buffer.*')
    ->select('zone.name AS zone_name')
    ->select('order_state.name AS state_name')
    ->from($this->tb)
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if( ! empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if( ! empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('buffer.warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    $this->db->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->from($this->tb)
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if( ! empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if( ! empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('buffer.warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_sum_buffer_product($order_code, $product_code, $detail_id = NULL)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_details($order_code, $product_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_all_details($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sum_stock($code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $code)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }

  ///--- เอาเฉพาะสินค้าและโซน
  public function get_buffer_zone($zone_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->get($this->tb);

    return $rs->row()->qty;
  }

  //---- เอาเฉพาะสินค้าและโซน ใช้ตอน QC
  public function get_prepared_from_zone($order_code, $product_code)
  {
    $this->db
    ->select('buffer.*, zone.name')
    ->from($this->tb)
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code);

    $rs = $this->db->get();

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
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    $this->db
    ->set("qty", "qty + {$qty}", FALSE)
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->update($this->tb);
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function delete_all($code)
  {
    return $this->db->where('order_code', $code)->delete($this->tb);
  }


	public function drop_buffer($order_code)
	{
		return $this->db->where('order_code', $order_code)->delete($this->tb);
	}


  public function is_exists($order_code, $product_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $count = $this->db->count_all_results($this->tb);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function remove_zero_buffer($code)
  {
    return $this->db->where('order_code', $code)->where('qty', 0)->delete($this->tb);
  }


  public function remove_buffer($order_code, $item_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('order_detail_id', $detail_id);

    return $this->db->delete($this->tb);
  }


  public function get_buffer_by_order_and_product($order_code, $item_code, $detail_id)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('order_detail_id', $detail_id)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_product_buffer_zone($zone_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty > 0 ? $rs->row()->qty : 0;
    }

    return 0;
  }
}
 ?>
