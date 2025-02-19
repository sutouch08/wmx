<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_complete extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_zone_code_and_name($warehouse_id = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];

    $this->db->select('id, code, name, warehouse_id')->where('active', 1);

    if( ! empty($warehouse_id))
    {
      $this->db->where('warehouse_id', $warehouse_id);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $this->db
    ->order_by('code', 'ASC')
    ->limit(50);

    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = array(
          'label' => $zone->code.' | '.$zone->name,
          'id' => $zone->id,
          'code' => $zone->code,
          'name' => $zone->name,
          'warehouse_id' => $zone->warehouse_id
        );
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_customer_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $rs = $this->db
    ->select('code, name')
    ->where('CardType', 'C')
		->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->or_like('old_code', $txt)
    ->group_end()
    ->limit(20)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code.' | '.$rd->name;
      }
    }

    echo json_encode($sc);
  }


  public function sub_district()
  {
    $sc = array();
    $adr = $this->db->like('tumbon', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function district()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('amphur', $_REQUEST['term'])
    ->group_by('amphur')
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function province()
  {
    $sc = array();
    $adr = $this->db->select("province")
    ->like('province', $_REQUEST['term'])
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->province;
      }
    }

    echo json_encode($sc);
  }


  public function postcode()
  {
    $sc = array();
    $adr = $this->db->like('zipcode', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function get_product_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, old_code')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('old_code', $txt)
    ->group_end()
    ->limit(20)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code;
      }
    }


    echo json_encode($sc);
  }


  public function get_item_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('id, code')
    ->where('active', 1)
    ->like('code', $txt)
    ->limit(50)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = array(
          'label' => $pd->code,
          'code' => $pd->code,
          'id' => $pd->id
        );
      }
    }
    else
    {
      $sc[] = 'no item found';
    }

    echo json_encode($sc);
  }


  public function get_warehouse_code_and_name()
  {
    $txt = $_REQUEST['term'];

    $sc  = array();

    $rs = $this->db
    ->select('code, name')
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->order_by('code', 'ASC')
    ->limit(20)
    ->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $sc[] = $wh->code.' | '.$wh->name;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }
} //-- end class
?>
