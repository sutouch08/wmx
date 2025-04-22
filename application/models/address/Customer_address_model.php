<?php
class Customer_address_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_customer_bill_to_address($customer_code)
  {
    $rs = $this->db->where('customer_code', $customer_code)->get('address_bill_to');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_customer_ship_to_address($id)
  {
    $rs = $this->db->where('id', $id)->get('address_ship_to');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_ship_to(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('address_ship_to', $ds);
    }

    return FALSE;
  }


  public function update_ship_to_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update('address_ship_to', $ds);
    }

    return FALSE;
  }


  public function get_ship_to_address($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('address_ship_to');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function add_bill_to(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('address_bill_to', $ds);
    }

    return FALSE;
  }


  public function update_bill_to($customer_code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('customer_code', $customer_code)->update('address_bill_to', $ds);
    }

    return FALSE;
  }


  public function update_bill_to_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update('address_bill_to', $ds);
    }

    return FALSE;
  }


  public function delete_ship_to($id)
  {
    return $this->db->where('id', $id)->delete('address_ship_to');
  }


  public function get_max_code($code)
  {
    $qr = "SELECT MAX(address_code) AS code FROM address_ship_to WHERE code = '{$code}' ORDER BY address_code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



} //--- end class

 ?>
