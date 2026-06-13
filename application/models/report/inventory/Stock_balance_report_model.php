<?php
class Stock_balance_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_stock_balance_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    ini_set('memory_limit','512M'); 
    $this->db
    ->select('p.code AS product_code, p.name AS product_name, p.cost AS price')
    ->select('w.code AS warehouse_code, z.code AS zone_code, z.name AS zone_name')
    ->select('s.qty')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
    ->where('s.qty !=', 0);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->db->where('p.model_code >=', $pdFrom)->where('p.model_code <=', $pdTo);
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && !empty($warehouse))
      {
        $this->db->where_in('z.warehouse_code', $warehouse);
      }
    }

    if($allZone == 0 && !empty($zoneCode))
    {
      $this->db->where('s.zone_code', $zoneCode);
    }

    $this->db->order_by('p.code', 'ASC');
    $this->db->order_by('w.code', 'ASC');
    $this->db->order_by('z.code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }  

}
 ?>
