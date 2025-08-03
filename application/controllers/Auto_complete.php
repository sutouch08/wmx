<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_complete extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_sponsor()
  {
    $ds = array();
    $txt = trim($_REQUEST['term']);

    $this->db
    ->select('cs.code, cs.name')
    ->from('sponsor AS sp')
    ->join('customers AS cs', 'sp.customer_code = cs.code', 'left')
    ->where('sp.active', 1);

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('cs.code', $txt)
      ->or_like('cs.name', $txt)
      ->group_end();
    }

    $sp = $this->db->order_by('cs.name', 'DESC')->limit(50)->get();

    if($sp->num_rows() > 0)
    {
      foreach($sp->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'notfound';
    }

    echo json_encode($sc);
  }


  public function get_vender_code_and_name()
  {
    $sc = array();
    $this->db->select('code, name')->where('status', 1);

    if(trim($_REQUEST['term']) != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $_REQUEST['term'])->or_like('name', $_REQUEST['term']);
      $this->db->group_end();
    }

    $vender = $this->db->limit(20)->get('vender');

    if($vender->num_rows() > 0)
    {
      foreach($vender->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }


  public function get_po_code($vendor = NULL)
  {
    $sc = array();
    $txt = trim($_REQUEST['term']);

    //---- receive product if over due date or not
    $receive_due = getConfig('RECEIVE_OVER_DUE'); //--- 1 = receive , 0 = not receive

    $this->db->select('code, vender_code, vender_name')->where_in('status', ['O', 'P']);

    if( ! empty($vendor))
    {
      $this->db->where('vender_code', $vendor);
    }

    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }

    $po = $this->db->get('po');

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $rs)
      {
        $sc[] = $rs->code ." | ".$rs->vender_name;
      }
    }
		else
		{
			$sc[] = "not found";
		}

    echo json_encode($sc);
  }


  public function get_invoice_code($customer_code = NULL)
	{
		$txt = $_REQUEST['term'];
		$ds = array();

		$this->db
		->select('code, customer_code, customer_name')
		->where('state', 8)
		->where_in('role', array('S','P', 'U'));

    if( ! empty($customer_code))
    {
      $this->db->where('customer_code', $customer_code);
    }

    $qs = $this->db->like('code', $txt)->get('orders');

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$ds[] = $rs->code ." | ".$rs->customer_code." | ".$rs->customer_name;
			}
		}
		else
		{
			$ds[] = 'Not found';
		}

		echo json_encode($ds);
	}


	public function get_wx_code()
	{
		$txt = trim($_REQUEST['term']);
		$sc = array();

		$this->db->select('code');
		if($txt != '*')
		{
			$this->db->like('code', $txt);
		}

		$rs = $this->db->order_by('code', 'DESC')->limit(20)->get('consign_check');

		if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code;
      }
    }
		else
		{
			$sc[] = "not found";
		}

    echo json_encode($sc);
	}


  public function get_sender()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $rs = $this->db
    ->select('id, name')
    ->like('name', $txt)
    ->limit(20)
    ->get('address_sender');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->id.' | '.$rd->name;
      }
    }

    echo json_encode($sc);
  }


  public function get_carplate()
  {
    $txt = trim($_REQUEST['term']);

    $sc = [];

    if($txt != '*')
    {
      $this->db->like('plate_no', $txt)->or_like('province', $txt);
    }

    $rs = $this->db
    ->order_by('id', 'DESC')
    ->limit(50)
    ->get('dispatch_cars');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->plate_no.' | '.$rd->province;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }


  public function get_driver_name()
  {
    $txt = trim($_REQUEST['term']);
    $sc = [];

    if($txt != '*')
    {
      $this->db->like('name', $txt);
    }

    $rs = $this->db->order_by('name', 'ASC')->limit(50)->get('dispatch_driver');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->name;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }


  public function plate_province()
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
        $sc[] = str_replace('จังหวัด', '', $rs->province);
      }
    }
    else
    {
      $sc[] = 'not found';
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


  public function get_customer_code_name_id()
  {
    $ds = [];

    $txt = $_REQUEST['term'];

    $rs = $this->db
    ->select('id, code, name')
		->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->group_end()
    ->limit(50)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name.' | '.$rd->id;
      }
    }

    echo json_encode($ds);
  }


  public function get_model_code()
  {
    $sc = array();
    $this->db
    ->select('code, name')
    ->group_start()
    ->like('code', $_REQUEST['term'])
    ->or_like('name', $_REQUEST['term'])
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(50);

    $qs = $this->db->get('product_model');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code .' | '.$rs->name;
      }
    }

    echo json_encode($sc);
  }


  //--- use in discount_rule
  public function get_model_name()
  {
    $ds = [];

    $txt = trim($_REQUEST['term']);

    $this->db->select('id, code, name');

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $this->db->order_by('code', 'ASC')->limit(50);
    $rs = $this->db->get('product_model');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code." | ".$rd->name." | ".$rd->id;
      }
    }
    else
    {
      $ds[] = "not found";
    }

    echo json_encode($ds);
  }



  public function get_model_code_and_name()
  {
    $sc = array();

    $txt = trim($_REQUEST['term']);

    $this->db->select('code, name');

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

    $qs = $this->db->get('product_model');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_prepare_style_code()
  {
    $sc = array();
    $this->db
    ->select('code, old_code')
    ->where('active', 1)
    ->where('can_sell', 1)
    ->where('is_deleted', 0)
    ->group_start()
    ->like('code', $_REQUEST['term'])
    ->or_like('old_code', $_REQUEST['term'])
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(20);
    $qs = $this->db->get('product_model');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      $sc[] = $rs->code .' | '.$rs->old_code;
    }

    echo json_encode($sc);
  }


  public function get_prepare_item_code()
  {
    $sc = array();
    $this->db
    ->select('code, old_code')
    ->where('active', 1)
    ->where('can_sell', 1)
    ->where('is_deleted', 0)
    ->group_start()
    ->like('code', $_REQUEST['term'])
    ->or_like('old_code', $_REQUEST['term'])
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(50);
    $qs = $this->db->get('products');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      $sc[] = $rs->code .' | '.$rs->old_code;
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


  //---- ค้นหาใบเบิกสินค้าแปรสภาพ
  //---- $all : TRUE => ทุกสถานะ
  //---- $all : FALSE => เฉพาะที่ยังไม่ปิด
  public function get_transform_code($all = FALSE)
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    if($all === FALSE)
    {
      $this->db->where('is_closed', 0);
    }

    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    $this->db->limit(20);
    $code = $this->db->get('order_transform');
    if($code->num_rows() > 0)
    {
      foreach($code->result() as $rs)
      {
        $sc[] = $rs->order_code;
      }
    }
    else
    {
      $sc[] = 'ไม่พบข้อมูล';
    }

    echo json_encode($sc);
  }


  public function get_request_receive_po_code($vendor = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];

    $this->db
    ->select('code, po_code')
    ->where('status', 1)
    ->where('valid', 0);

    if( ! empty($vendor))
    {
      $this->db->where('vendor_code', $vendor);
    }

    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }

    $rq = $this->db->get('receive_product_request');


    if($rq->num_rows() > 0)
    {
      foreach($rq->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->po_code;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }


  public function get_valid_lend_code($empID = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('order_code');
    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    if( ! empty($empID))
    {
      $this->db->where('empID', $empID);
    }

    $this->db->where('valid' , 0)->group_by('order_code')->limit(20);
    $rs = $this->db->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ds)
      {
        $sc[] = $ds->order_code;
      }
    }

    echo json_encode($sc);
  }


  public function get_zone_code_and_name($warehouse = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code, name')->where('active', 1);

    if( ! empty($warehouse))
    {
      $warehouse = urldecode($warehouse);
      $arr = explode('|', $warehouse);
      $this->db->where_in('warehouse_code', $arr);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('old_code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $this->db
    ->order_by('warehouse_code', 'ASC')
    ->order_by('code', 'ASC')
    ->limit(20);

    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = $zone->code.' | '.$zone->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_common_zone_code_and_name($warehouse = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
    ->select('zone.code AS code, zone.name AS name')
    ->from('zone')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->where_in('warehouse.role', array(1, 3, 4, 5))
    ->where('zone.active', 1)
    ->where('warehouse.active', 1);

    if( ! empty($warehouse))
    {
      $warehouse = urldecode($warehouse);
      $arr = explode('|', $warehouse);
      $this->db->where_in('zone.warehouse_code', $arr);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('zone.code', $txt)
      ->or_like('zone.old_code', $txt)
      ->or_like('zone.name', $txt)
      ->group_end();
    }

    $this->db
    ->order_by('zone.warehouse_code', 'ASC')
    ->order_by('zone.code', 'ASC')
    ->limit(20);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = $zone->code.' | '.$zone->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_zone_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code, name')->where('active', 1);
    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $txt)->or_like('old_code', $txt)->or_like('name', $txt);
      $this->db->group_end();
    }

    $rs = $this->db->limit(20)->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $cs)
      {
        $sc[] = $cs->code.' | '.$cs->name;
      }
    }

    echo json_encode($sc);
  }


  public function get_transform_zone()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
    ->select('zone.code AS code, zone.name AS name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('zone.active', 1)
    ->where('warehouse.role', 7); //--- 7 =  คลังระหว่างทำ ดู table warehouse_role

    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('zone.code', $txt);
      $this->db->or_like('zone.name', $txt);
      $this->db->group_end();
    }

    $this->db->limit(20);

    $zone = $this->db->get();

    if($zone->num_rows() > 0)
    {
      foreach($zone->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_lend_zone($empID)
  {
    $sc = array();
    $txt = $_REQUEST['term'];

    if( ! empty($empID))
    {
      $this->db
      ->select('zone.code AS code, zone.name AS name')
      ->from('zone')
      ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
      ->join('zone_employee', 'zone_employee.zone_code = zone.code')
      ->where('zone.active', 1)
      ->where('warehouse.role', 8) //--- 8 =  คลังยืมสินค้า ดู table warehouse_role
      ->where('zone_employee.empID', $empID);

      if($txt != '*')
      {
        $this->db->like('zone.code', $txt);
        $this->db->or_like('zone.name', $txt);
      }

      $this->db->limit(20);

      $zone = $this->db->get();

      if($zone->num_rows() > 0)
      {
        foreach($zone->result() as $rs)
        {
          $sc[] = $rs->code.' | '.$rs->name;
        }
      }
      else
      {
        $sc[] = "not found";
      }
    }
    else
    {
      $sc[] = "กรุณาระบุผู้ยืม";
    }

    echo json_encode($sc);
  }


  public function get_user()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('uname, name');
    if($txt != '*')
    {
      $this->db->like('uname', $txt)->or_like('name', $txt);
    }
    $this->db->limit(20);

    $sponsor = $this->db->get('user');

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->uname.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_active_user_by_uname()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('id, uname, name')->where('active', 1);

    if($txt != '*')
    {
      $this->db->like('uname', $txt);
    }

    $rs = $this->db->limit(20)->get('user');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ds)
      {
        $arr = array(
          'label' => $ds->uname,
          'id' => $ds->id,
          'uname' => $ds->uname,
          'dname' => $ds->name
        );

        array_push($sc, $arr);
      }
    }

    echo json_encode($sc);
  }


  public function get_consign_zone($customer_code = '')
  {
    if($customer_code == '')
    {
      echo json_encode(array('เลือกลูกค้าก่อน'));
    }
    else
    {
      $this->db
      ->select('zone.code, zone.name')
      ->from('zone_customer')
      ->join('zone', 'zone.code = zone_customer.zone_code', 'left')
      ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
      ->where('warehouse.role', 2) //--- 2 = คลังฝากขาย
      ->where('zone_customer.customer_code', $customer_code)
      ->where('zone.active', 1);

      if($_REQUEST['term'] != '*')
      {
        $this->db->group_start();
        $this->db->like('zone.code', $_REQUEST['term']);
        $this->db->or_like('zone.name', $_REQUEST['term']);
        $this->db->group_end();
      }

      $this->db->limit(20);
      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        $ds = array();
        foreach($rs->result() as $rd)
        {
          $ds[] = $rd->code.' | '.$rd->name;
        }

        echo json_encode($ds);
      }
      else
      {
        echo json_encode(array('ไม่พบโซน'));
      }
    }
  }


  public function getConsignmentZone($warehouse_code = NULL)
  {
    $this->db
    ->select('zone.code, zone.name')
    ->from('zone')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->where('warehouse.role', 2)
    ->where('warehouse.is_consignment', 1)
    ->where('zone.active', 1)
    ->limit(20);

    if($_REQUEST['term'] != '*')
    {
      $this->db->group_start();
      $this->db->like('zone.code', $_REQUEST['term']);
      $this->db->or_like('zone.name', $_REQUEST['term']);
      $this->db->group_end();
    }

    if( ! empty($warehouse_code))
    {
      $this->db->where('zone.warehouse_code', $warehouse_code);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      $ds = array();
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }

      echo json_encode($ds);
    }
    else
    {
      echo json_encode(array('ไม่พบโซน'));
    }

  }


  public function get_consignment_zone($customer_code = NULL)
  {
    if(empty($customer_code))
    {
      echo json_encode(array('เลือกลูกค้าก่อน'));
    }
    else
    {
      $this->db
      ->select('zone.code, zone.name')
      ->from('zone_customer')
      ->join('zone', 'zone.code = zone_customer.zone_code', 'left')
      ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
      ->where('warehouse.role', 2) //--- 2 = คลังฝากขาย
      ->where('is_consignment', 1)
      ->where('zone.active', 1)
      ->where('zone_customer.customer_code', $customer_code);

      if($_REQUEST['term'] != '*')
      {
        $this->db->group_start();
        $this->db->like('zone.code', $_REQUEST['term']);
        $this->db->or_like('zone.name', $_REQUEST['term']);
        $this->db->group_end();
      }

      $this->db->limit(20);

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        $ds = array();
        foreach($rs->result() as $rd)
        {
          $ds[] = $rd->code.' | '.$rd->name;
        }

        echo json_encode($ds);
      }
      else
      {
        echo json_encode(array('ไม่พบโซน'));
      }
    }
  }


  public function get_product_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code')
    ->where('active', 1)
    ->like('code', $txt)
    ->limit(50)
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


  public function get_product_code_and_name()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, name')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(50)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code." | ".$pd->name;
      }
    }
    else
    {
      $sc[] = "notfound";
    }

    echo json_encode($sc);
  }


  public function get_item_code_and_name()
  {
    $sc = array();
  	$txt = trim($_REQUEST['term']);

  	$this->db->select('code, name');

  	if($txt !== '*')
  	{
  		$this->db->like('code', $txt);
  		$this->db->or_like('name', $txt);
  	}

  	$this->db->order_by('code', 'ASC');
  	$this->db->limit(50);

    $qs = $this->db->get('products');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_item_code_name_id()
  {
    $ds = [];
  	$txt = trim($_REQUEST['term']);

  	$this->db->select('id, code, name');

  	if($txt !== '*')
  	{
  		$this->db->like('code', $txt)->or_like('name', $txt);
  	}

  	$this->db->order_by('code', 'ASC');

  	$this->db->limit(50);

    $qs = $this->db->get('products');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name.' | '.$rs->id;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_item_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, name')
    ->where('active', 1)
    ->like('code', $txt)
    ->limit(100)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code .' | '.$pd->name;
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


  public function get_color_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt);
      $this->db->or_like('name', $txt);
    }
    $rs = $this->db->order_by('code', 'ASC')->limit(20)->get('product_color');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_fount";
    }

    echo json_encode($sc);
  }


  public function get_size_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt, 'after');
      $this->db->or_like('name', $txt, 'after');
    }
    $rs = $this->db->order_by('position', 'ASC')->limit(20)->get('product_size');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_found";
    }

    echo json_encode($sc);
  }


  public function get_warehouse_by_role($role = 1)
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    $rs = $this->db
    ->select('code, name')
    ->where('role', $role)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(20)
    ->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $sc[] = $row->code .' | '. $row->name;
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
