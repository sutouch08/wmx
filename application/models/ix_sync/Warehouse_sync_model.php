<?php
class Warehouse_sync_model extends CI_Model
{
  private $ix = null;
  private $tb = 'warehouse';

  public function __construct()
  {
    parent::__construct();
    $this->ix = $this->load->database('ix', TRUE);
  }

  public function count_warehouses()
  {
    return $this->ix->count_all_results($this->tb);
  }

  public function get_warehouses($limit = 1000, $offset = 0)
  {
    $this->ix->limit($limit, $offset);
    $rs = $this->ix->get($this->tb);
    
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    
    return NULL;
  }
}