<?php
class Zone_sync_model extends CI_Model
{
  private $ix = null;
  private $tb = 'zone';

  public function __construct()
  {
    parent::__construct();
    $this->ix = $this->load->database('ix', TRUE);
  }

  public function count_zone()
  {
    return $this->ix->count_all_results($this->tb);
  }

  public function get_zones($limit = 1000, $offset = 0)
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