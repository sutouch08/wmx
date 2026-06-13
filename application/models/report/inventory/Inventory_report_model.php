<?php
class Inventory_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function getStock($option, $limit = 100, $offset = 0)
  {
    $this->db
    ->select('p.code AS product_code, p.name AS product_name, p.cost')
    ->select('z.code AS warehouse_code, z.code AS zone_code, z.name AS zone_name')
    ->select_sum('s.qty')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('s.qty !=', 0);

    if($option->allProduct == 0 && ! empty($option->pdFrom) && ! empty($option->pdTo))
    {
      $this->db->where('p.model_code >=', $option->pdFrom)->where('p.model_code <=', $option->pdTo);
    }

    if ($option->allWhouse == 0 && ! empty($option->whsList))
    {
      $this->db->where_in('z.warehouse_code', $option->whsList);
    }

    $rs = $this->db
    ->group_by('s.product_code')
    ->order_by('s.product_code', 'ASC')
    ->limit($limit, $offset)
    ->get();    

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->db
    ->select('p.code AS product_code, p.name AS product_name, p.cost, p.barcode')
    ->select_sum('s.qty', 'qty')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')    
    ->where('s.qty !=', 0);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->db->where('p.model_code >=', $pdFrom)->where('p.model_code <=', $pdTo);
    }

    if($allWhouse == 0 && !empty($warehouse))
    {
      $this->db->where_in('z.warehouse_code', $warehouse);
    }

    $this->db
    ->group_by('s.product_code')
    ->order_by('s.product_code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;    
  }  


  public function get_reserv_stock($item_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where_in('orders.warehouse_code', $warehouse);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return empty($rs->row()->qty) ? 0 : $rs->row()->qty;
    }

    return 0;
  }

  public function countStockItems($option)
  {
    $qr = "SELECT COUNT(DISTINCT s.product_code) AS numrows FROM stock AS s ";
    $qr .= "LEFT JOIN zone AS z ON s.zone_code = z.code ";
    $qr .= "WHERE s.qty != 0 ";

    if($option->allProduct == 0 && ! empty($option->pdFrom) && ! empty($option->pdTo))
    {
      $qr .= "AND s.product_code >= '{$option->pdFrom}' ";
      $qr .= "AND s.product_code <= '{$option->pdTo}' ";
    }

    if($option->allWhouse == 0 && ! empty($option->whsList))
    {
      $whsCode = "";

      $i = 1;

      foreach ($option->whsList as $whs)
      {
        $whsCode .= $i == 1 ? "'{$whs}'" : ", '{$whs}'";
        $i++;
      }

      $qr .= "AND z.warehouse_code IN({$whsCode}) ";
    }

    $qs = $this->db->query($qr);

    if($qs->num_rows() == 1)
    {
      return $qs->row()->numrows;
    }

    return 0;
  }
}
 ?>
