<?php
class Address_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_shipping_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('address_ship_to');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_shipping_address_by_code($code)
  {
    return $this->get_ship_to_address($code);
  }


	public function get_shipping_address_id_by_code($code)
  {
    return $this->get_ship_to_address($code);
  }



  public function get_default_address($code)
  {
    return $this->get_ship_to_address($code);
  }



  public function get_shipping_address($code)
  {
    return $this->get_ship_to_address($code);
  }



	public function get_ship_to_address($code)
  {
    $rs = $this->db->where('order_code', $code)->get('address_ship_to');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function add_shipping_address(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert('address_ship_to', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function update_shipping_address($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('address_ship_to', $ds);
  }



  public function delete_shipping_address($id)
  {
    return $this->db->where('id', $id)->delete('address_ship_to');
  }


  public function count_address($code)
  {
    return $this->db->where('customer_code', $code)->count_all_results('address_ship_to');
  }


  public function is_valid_sub_district($sub_district)
  {
    $count = $this->db->where('tumbon', $sub_district)->count_all_results('address_info');

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_valid_district($district)
  {
    $count = $this->db->where('amphur', $district)->count_all_results('address_info');

    return $count > 0 ? TRUE : FALSE;
  }

  public function is_valid_province($province)
  {
    $count = $this->db->where('province', $province)->count_all_results('address_info');

    return $count > 0 ? TRUE : FALSE;
  }

  public function is_valid_postcode($postcode)
  {
    $count = $this->db->where('zipcode', $postcode)->count_all_results('address_info');

    return $count > 0 ? TRUE : FALSE;
  }

  public function is_valid_full_address($sub_district, $district, $province, $postcode)
  {
    $count = $this->db
    ->where('tumbon', $sub_district)
    ->where('amphur', $district)
    ->where('province', $province)
    ->where('zipcode', $postcode)
    ->count_all_results('address_info');

    return $count > 0 ? TRUE : FALSE;
  }
} //--- end class

 ?>
