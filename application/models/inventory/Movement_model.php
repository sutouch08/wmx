<?php
class Movement_model extends CI_Model
{
  private $tb = "stock_movement";

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


  public function drop_movement($code)
  {
    return $this->db->where('reference', $code)->delete($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['product_code']))
    {
      $this->db->like('product_code', $ds['product_code']);
    }

    if( ! empty($ds['warehouse_code']))
    {
      $this->db->like('warehouse_code', $ds['warehouse_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->like('zone_code', $ds['zone_code']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('date_upd >=', from_date($ds['from_date']))
      ->where('date_upd <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('date_upd', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['product_code']))
    {
      $this->db->like('product_code', $ds['product_code']);
    }

    if( ! empty($ds['warehouse_code']))
    {
      $this->db->like('warehouse_code', $ds['warehouse_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->like('zone_code', $ds['zone_code']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('date_upd >=', from_date($ds['from_date']))
      ->where('date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


} //--- end class

?>
