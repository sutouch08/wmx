<?php
class Zone_model extends CI_Model
{
  private  $tb = "zone";

  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  //--- add new customer to zone
  public function add_customer(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('zone_customer', $ds);
    }

    return FALSE;
  }


  //--- add new customer to zone
  public function add_employee(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('zone_employee', $ds);
    }

    return FALSE;
  }


  //--- remove customer from connected zone
  public function delete_customer($id)
  {
    return $this->db->where('id', $id)->delete('zone_customer');
  }


  //--- remove customer from connected zone
  public function delete_employee($id)
  {
    return $this->db->where('id', $id)->delete('zone_employee');
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function is_exists($code)
  {
    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $code = NULL)
  {
    if( ! empty($code))
    {
      $this->db->where('code !=', $code);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  //--- check customer exists in zone or not
  public function is_exists_customer($zone_code, $customer_code)
  {
    $count = $this->db
    ->where('zone_code', $zone_code)
    ->where('customer_code', $customer_code)
    ->count_all_results('zone_customer');

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_employee($zone_code, $empID)
  {
    $count = $this->db
    ->where('zone_code', $zone_code)
    ->where('empID', $empID)
    ->count_all_results('zone_employee');

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['customer']))
    {
      return $this->count_rows_customer($ds);
    }

    $this->db
    ->from('zone AS z')
    ->join('user AS u', 'z.user_id = u.id', 'left');

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('z.code', $ds['code'])
      ->or_like('z.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['uname']))
    {
      $this->db
      ->group_start()
      ->like('u.uname', $ds['uname'])
      ->or_like('u.name', $ds['uname'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']))
    {
      $this->db->where('z.warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] != 'all')
    {
      $this->db->where('z.is_pos_api', $ds['is_pos_api']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('z.active', $ds['active']);
    }

    return $this->db->count_all_results();
  }


  private function count_rows_customer(array $ds = array())
  {
    $this->db
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->join('user', 'zone.user_id = user.id','left')
    ->like('customers.code', $ds['customer'])
    ->or_like('customers.name', $ds['customer']);

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('zone.code', $ds['code'])
      ->or_like('zone.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['uname']))
    {
      $this->db
      ->group_start()
      ->like('user.uname', $ds['uname'])
      ->or_like('user.name', $ds['uname'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] != 'all')
    {
      $this->db->where('zone.is_pos_api', $ds['is_pos_api']);
    }

    if(isset($ds['is_pickface']) && $ds['is_pickface'] != 'all')
    {
      $this->db->where('zone.is_pickface', $ds['is_pickface']);
    }


    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('zone.active', $ds['active']);
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    //--- if search for customer
    if(! empty($ds['customer']))
    {
      return $this->get_list_customer($ds, $perpage, $offset);
    }

    $this->db
    ->select('zone.id, zone.code AS code, zone.name AS name, zone.warehouse_code, zone.is_pos_api')
    ->select('zone.old_code, zone.active, zone.is_pickface')
    ->select('warehouse.name AS warehouse_name, user.uname, user.name AS display_name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->join('user', 'zone.user_id = user.id', 'left');

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('zone.code', $ds['code'])
      ->or_like('zone.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['uname']))
    {
      $this->db
      ->group_start()
      ->like('user.uname', $ds['uname'])
      ->or_like('user.name', $ds['uname'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] != 'all')
    {
      $this->db->where('zone.is_pos_api', $ds['is_pos_api']);
    }

    if(isset($ds['is_pickface']) && $ds['is_pickface'] != 'all')
    {
      $this->db->where('zone.is_pickface', $ds['is_pickface']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('zone.active', $ds['active']);
    }

    $this->db->order_by('zone.date_upd', 'DESC');
    $this->db->order_by('zone.code', 'ASC');

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function get_list_customer(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('zone.id, zone.code AS code, zone.name AS name, warehouse.name AS warehouse_name, zone.old_code, zone.active, zone.is_pos_api')
    ->select('customers.code AS customer_code, customers.name AS customer_name')
    ->select('user.uname, user.name AS display_name')
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->join('user', 'zone.user_id = user.id', 'left');

    if(! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customers.code', $ds['customer'])
      ->or_like('customers.name', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('zone.code', $ds['code'])
      ->or_like('zone.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['uname']))
    {
      $this->db
      ->group_start()
      ->like('user.uname', $ds['uname'])
      ->or_like('user.name', $ds['uname'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] != 'all')
    {
      $this->db->where('zone.is_pos_api', $ds['is_pos_api']);
    }

    $this->db->group_by('zone.code');

    if( ! empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_customer($code)
  {
    return $this->db->where('zone_code', $code)->count_all_results('zone_customer');
  }


  public function get_customers($zone_code)
  {

    $rs = $this->db->where('zone_code', $zone_code)->get('zone_customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_employee($zone_code)
  {

    $rs = $this->db->where('zone_code', $zone_code)->get('zone_employee');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('zone.id, zone.code, zone.name, zone.warehouse_code, zone.active, zone.user_id, zone.is_pos_api, zone.is_pickface')
    ->select('warehouse.name AS warehouse_name, warehouse.role, warehouse_role.name AS role_name, warehouse.is_consignment')
    ->select('user.uname, user.name AS display_name')
    ->from('zone')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->join('warehouse_role', 'warehouse.role = warehouse_role.id', 'left')
    ->join('user', 'zone.user_id = user.id', 'left')
    ->where('zone.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_warehouse_code($zone_code)
  {
    $rs = $this->db->select('warehouse_code')->where('code', $zone_code)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->warehouse_code;
    }

    return NULL;
  }


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_zone_detail_in_warehouse($code, $warehouse)
  {
    $rs = $this->db
    ->where('warehouse_code', $warehouse)
    ->group_start()
    ->where('code', $code)
    ->or_where('old_code', $code)
    ->group_end()
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_zone($code, $warehouse_code = NULL)
  {
    if( ! empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    $rs = $this->db
    ->where('warehouse_code IS NOT NULL', NULL, FALSE)
    ->group_start()
    ->where('code', $code)
    ->or_where('old_code', $code)
    ->group_end()
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function search($txt, $warehouse_code = NULL)
  {
    $limit = 50;
    if( ! empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $txt)->or_like('name', $txt);
      $this->db->group_end();
    }

    $this->db->order_by('code', 'ASC');
    $rs = $this->db->limit($limit)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- ใช้จัดสินค้า
  public function get_zone_code($barcode)
  {
    $rs = $this->db->select('code')->where('old_code', $barcode)->or_where('code', $barcode)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_pickface_zone()
  {
    $rs = $this->db->where('is_pickface', 1)->where('active', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function has_customer($code)
  {
    $count = $this->db->where('zone_code', $code)->count_all_results('zone_customer');

    return $count > 0 ? TRUE : FALSE;
  }


  public function has_employee($code)
  {
    $count = $this->db->where('zone_code', $code)->count_all_results('zone_employee');

    return $count > 0 ? TRUE : FALSE;
  }


  public function has_stock($code)
  {
    $count = $this->db->where('zone_code', $code)->where('qty >', 0)->count_all_results('stock');

    return $count > 0 ? TRUE : FALSE;
  }


  public function has_transection($code)
  {
    $count = $this->db->where('zone_code', $code)->count_all_results('stock_movement');

    return $count > 0 ? TRUE : FALSE;
  }
} //--- end class

 ?>
