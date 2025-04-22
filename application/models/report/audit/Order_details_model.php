<?php
class Order_details_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function count_rows(array $ds = array())
  {
    if(! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('is_expired', $ds['is_expired']);
      }

      if($ds['is_preorder'] != 'all')
      {
        $this->db->where('is_pre_order', $ds['is_preorder']);
      }

      if($ds['all_role'] == 0 && ! empty($ds['role']))
      {
        $this->db->where_in('role', $ds['role']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('state', $ds['state']);
      }

      if($ds['all_channels'] == 0 && ! empty($ds['channels']))
      {
        $this->db->where_in('channels_code', $ds['channels']);
      }

      if($ds['all_payment'] == 0 && ! empty($ds['payment']))
      {
        $this->db->where_in('payment_code', $ds['payment']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse']);
      }

      return $this->db->count_all_results('orders');
    }

    return 0;
  }



  public function get_data(array $ds = array(), $limit = NULL)
  {
    if(! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('is_expired', $ds['is_expired']);
      }

      if($ds['is_preorder'] != 'all')
      {
        $this->db->where('is_pre_order', $ds['is_preorder']);
      }

      if($ds['all_role'] == 0 && ! empty($ds['role']))
      {
        $this->db->where_in('role', $ds['role']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('state', $ds['state']);
      }

      if($ds['all_channels'] == 0 && ! empty($ds['channels']))
      {
        $this->db->where_in('channels_code', $ds['channels']);
      }

      if($ds['all_payment'] == 0 && ! empty($ds['payment']))
      {
        $this->db->where_in('payment_code', $ds['payment']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse']);
      }

      $this->db->order_by('date_add', 'ASC')->order_by('code', 'ASC');

      if( ! empty($limit))
      {
        $this->db->limit($limit);
      }

      $rs = $this->db->get('orders');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

  public function get_doc_total($code)
  {
    $rs = $this->db->select_sum('total_amount')->where('order_code', $code)->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->total_amount;
    }

    return 0.00;
  }

} //-- end class

 ?>
