<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class stock_model extends CI_Model
{
  private $tb = "stock";

  public function __construct()
  {
    parent::__construct();
  }


  public function update_stock_zone($product_id, $product_code, $zone_id, $warehouse_id, $qty = 0)
  {
    if( ! empty($product_id) && ! empty($product_code) && ! empty($zone_id) && ! empty($warehouse_id) && $qty != 0)
    {
      $id = $this->get_id($product_id, $zone_id);

      if(empty($id))
      {
        $arr = array(
          'product_id' => $product_id,
          'product_code' => $product_code,
          'zone_id' => $zone_id,
          'warehouse_id' => $warehouse_id,
          'qty' => $qty
        );

        return $this->add($arr);
      }
      else
      {
        return $this->update($id, $qty);
      }
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($id, $qty)
  {
    $rs = $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->tb);
    return $rs;
  }


  public function remove_zero_stock()
  {
    return $this->db->where('qty', 0)->delete($this->tb);
  }


  public function get_id($product_id, $zone_id)
  {
    $rs = $this->db
    ->select('id')
    ->where('product_id', $product_id)
    ->where('zone_id', $zone_id)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function count_items_zone($zone_id)
  {
    $this->db->where('zone_id', $zone_id)->where('qty >', 0, FALSE);
    return $this->db->count_all_results($this->tb);
  }


  public function get_stock_zone($zone_id, $pd_id)
  {
    $rs = $this->db
    ->where('zone_id', $zone_id)
    ->where('product_id', $pd_id)
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_stock($pd_id, $wh_id = NULL, $zone_id = NULL)
  {
    $this->db
    ->select_sum('qty')
    ->where('product_id', $pd_id);

    if( ! empty($wh_id))
    {
      $this->db->where('warehouse_id', $wh_id);
    }

    if( ! empty($zone_id))
    {
      $this->db->where('zone_id', $zone_id);
    }

    $rs = $this->db->group_by('product_id')->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  //---- ยอดสินค้าคงเหลือในแต่ละโซน
  public function get_stock_in_zone($pd_id, $wh_id = NULL)
  {
    $this->db
    ->select('z.code, z.name, s.qty')
    ->from('stock AS s')
    ->join('zone AS z', 's.zone_id = z.id', 'left')
    ->where('s.product_id', $pd_id);

    if( ! empty($wh_id))
    {
      $this->db->where('s.warehouse_id', $wh_id);
    }


    $rs = $this->db->order_by('z.code', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}//--- end class
