<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class stock_model extends CI_Model
{
  private $tb = "stock";

  public function __construct()
  {
    parent::__construct();
  }

  public function update_stock_zone($zone_code, $product_code, $qty)
  {
    if($qty != 0)
    {
      if( ! $this->is_exists($zone_code, $product_code))
      {
        $arr = array(
          'product_code' => $product_code,
          'zone_code' => $zone_code,
          'qty' => $qty
        );

        return $this->db->insert($this->tb, $arr);
      }
      else
      {
        return $this->db
        ->set("qty", "qty + {$qty}", FALSE)
        ->where('zone_code', $zone_code)
        ->where('product_code', $product_code)
        ->update($this->tb);
      }
    }

    return TRUE;
  }


  public function is_exists($zone_code, $product_code)
  {
    $count = $this->db
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_items_zone($zone_code)
  {
    $this->db->where('zone_code', $zone_code)->where('qty >', 0, FALSE);
    return $this->db->count_all_results($this->tb);
  }


  public function count_items_consignment_zone($zone_code)
  {
    $this->db->where('zone_code', $zone_code)->where('qty >', 0, FALSE);
    return $this->db->count_all_results($this->tb);
  }


  public function get_stock_zone($zone_code, $pd_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('product_code', $pd_code)
    ->where('zone_code', $zone_code)
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return get_zero($rs->row()->qty);
    }

    return 0;
  }


  public function get_consign_stock_zone($zone_code, $pd_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('product_code', $pd_code)
    ->where('zone_code', $zone_code)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }


  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_stock($item, $warehouse = NULL, $zone = NULL)
  {
    $this->db
    ->select_sum('s.qty')
    ->from('stock AS s')
    ->join('zone AS z', 's.zone_code = z.code', 'left');

    if(empty($warehouse))
    {
      $this->db
      ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
      ->where_in('w.role', [1, 3, 5]);
    }
    else
    {
      $this->db->where('z.warehouse_code', $warehouse);
    }

    if( ! empty($zone))
    {
      $this->db->where('s.zone_code', $zone);
    }

    $rs = $this->db->where('s.product_code', $item)->get();

    $stock = intval($rs->row()->qty);
    $buffer = $this->get_buffer_stock($item, $warehouse, $zone);
    $cancel = $this->get_cancel_stock($item, $warehouse, $zone);

    return $stock + $buffer + $cancel;
  }

  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_items_stock(array $items = array(), $warehouse = NULL, $zone = NULL)
  {
    $this->db
    ->select('s.product_code')
    ->select_sum('s.qty')
    ->from('stock AS s')
    ->join('zone AS z', 's.zone_code = z.code', 'left');

    if(empty($warehouse))
    {
      $this->db
      ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
      ->where_in('w.role', [1, 3, 5]);
    }
    else
    {
      $this->db->where('z.warehouse_code', $warehouse);
    }

    if( ! empty($zone))
    {
      $this->db->where('s.zone_code', $zone);
    }

    $rs = $this->db
    ->where_in('s.product_code', $items)
    ->group_by('s.product_code')
    ->get();

    if($rs->num_rows() > 0)
    {
      $stockList = [];

      foreach($rs->result() as $ro)
      {
        $stock = intval($ro->qty);
        $buffer = $this->get_buffer_stock($ro->product_code, $warehouse, $zone);
        $cancel = $this->get_cancel_stock($ro->product_code, $warehouse, $zone);

        $stockList[$ro->product_code] = $stock + $buffer + $cancel;
      }

      return $stockList;
    }

    return NULL;
  }


  public function get_buffer_stock($sku, $warehouse_code = NULL, $zone_code = NULL)
  {
    $this->db->select_sum('qty')->where('product_code', $sku);

    if( ! empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    if( ! empty($zone_code))
    {
      $this->db->where('zone_code', $zone_code);
    }

    $rs = $this->db->get('buffer');

    return intval($rs->row()->qty);
  }


  public function get_cancel_stock($sku, $warehouse_code = NULL, $zone_code = NULL)
  {
    $this->db->select_sum('qty')->where('product_code', $sku);

    if( ! empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    if( ! empty($zone_code))
    {
      $this->db->where('zone_code', $zone_code);
    }

    $rs = $this->db->get('cancle');

    return intval($rs->row()->qty);
  }


  //--- ยอดรวมสินค้าทั้งหมดทุกคลัง (รวมฝากขาย)
  public function get_stock($item)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $item)->get($this->tb);

    return intval($rs->row()->qty);
  }


	//--- ยอดรวมสินค้าทั้งหมดในคลังฝากขายเทียมเท่านั้น
  public function get_consignment_stock($item)
  {
    $rs = $this->db
    ->select_sum('s.qty')
    ->from('stock AS s')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
    ->where('w.role', 2)
    ->where('w.is_consignment', 1)
    ->where('s.product_code', $item)
    ->get();

    return intval($rs->row()->qty);
  }


  //---- ยอดสินค้าคงเหลือในแต่ละโซน
  public function get_stock_in_zone($item, $warehouse = NULL)
  {
    $this->db
    ->select('s.zone_code, s.qty, z.name')
    ->from('stock AS s')
    ->join('zone AS z', 's.zone_code = z.code', 'left');

    if( ! empty($warehouse))
    {
      $this->db->where('z.warehouse_code', $warehouse);
    }
    else
    {
      $this->db
      ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
      ->where_in('w.role', [1,3,5]);
    }

    $rs = $this->db->where('s.product_code', $item)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_all_stock_in_zone($zone_code, $item_code = NULL)
  {
    $this->db
    ->select('s.product_code, s.qty')
    ->select('p.name AS product_name, p.barcode, p.cost, p.price')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->where('s.zone_code', $zone_code)
    ->where('s.qty !=', 0);

    if( ! empty($item_code))
    {
      $this->db
      ->group_start()
      ->like('s.product_code', $item_code)
      ->or_like('p.barcode', $item_code)
      ->group_end();
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน POS API
  public function getAllStockInZone($zone_code, $limit = 10000, $offset = 0)
  {
    $rs = $this->db
    ->select('product_code, qty')
    ->where('zone_code', $zone_code)
    ->where('qty !=', 0)
    ->order_by('product_code', 'ASC')
    ->limit($limit, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน POS API
  public function getAllStockInConsignmentZone($zone_code, $limit = 10000, $offset = 0)
  {
    $rs = $this->db
    ->select('product_code, qty')
    ->where('zone_code', $zone_code)
    ->where('qty !=', 0)
    ->order_by('product_code', 'ASC')
    ->limit($limit, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_stock_consignment_zone($zone_code, $item_code = NULL)
  {
    $this->db
    ->select('s.product_code, s.qty')
    ->select('p.name AS product_name, p.barcode, p.cost, p.price')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->where('s.zone_code', $zone_code)
    ->where('s.qty !=', 0);

    if( ! empty($item_code))
    {
      $this->db
      ->group_start()
      ->like('s.product_code', $item_code)
      ->or_like('p.barcode', $item_code)
      ->group_end();
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

	//--- for compare stock
	public function get_items_stock($warehouse_code)
	{
    $rs = $this->db
    ->select('s.product_code, s.qty')
    ->select('p.code, p.name, p.barcode, p.unit_code, p.count_stock')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('z.warehouse_code', $warehouse_code)
    ->where('p.count_stock', 1)
    ->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      $this->db
      ->select('product_code, zone_code, qty')
      ->select('qty, product_code, zone_code');
      // ->where('qty !=', 0);

      if( ! empty($ds['item_code']))
      {
        $this->db->like('product_code', $ds['item_code']);
      }

      if( ! empty($ds['zone_code']))
      {
        $this->db->like('zone_code', $ds['zone_code']);
      }

      $rs = $this->db
      ->order_by('product_code', 'ASC')
      ->order_by('zone_code', 'ASC')
      ->limit($perpage, $offset)->get($this->tb);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_export_list(array $ds = array())
  {
    if( ! empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      $this->db
      ->select('product_code, zone_code, qty')
      ->select('qty, product_code, zone_code');
      // ->where('qty !=', 0);

      if( ! empty($ds['item_code']))
      {
        $this->db->like('product_code', $ds['item_code']);
      }

      if( ! empty($ds['zone_code']))
      {
        $this->db->like('zone_code', $ds['zone_code']);
      }

      $rs = $this->db
      ->order_by('product_code', 'ASC')
      ->order_by('zone_code', 'ASC')
      ->get($this->tb);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      $itemCode = $ds['item_code'];
      $zoneCode = $ds['zone_code'];

      $this->db->where('qty !=', 0);

      if( ! empty($ds['item_code']))
      {
        $this->db->like('product_code', $ds['item_code']);
      }

      if( ! empty($ds['zone_code']))
      {
        $this->db->like('zone_code', $ds['zone_code']);
      }

      return $this->db->count_all_results($this->tb);
    }

    return 0;
  }

}//--- end class
