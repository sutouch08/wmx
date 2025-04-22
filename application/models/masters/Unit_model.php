<?php
class Unit_model extends CI_Model
{
  private $tb = "unit";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_data()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //--- end class

 ?>
