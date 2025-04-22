<?php
class Sponsor_budget_model extends CI_Model
{
  private $tb = "sponsor_budget";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_used($id, $amount)
  {
    if($this->db->set("used", "used + {$amount}", FALSE)->where('id', $id)->update($this->tb))
    {
      return $this->recal_balance($id);
    }

    return FALSE;
  }


  public function rollback_used($id, $amount)
  {
    if($this->db->set("used", "used - {$amount}", FALSE)->where('id', $id)->update($this->tb))
    {
      return $this->recal_balance($id);
    }
  }


  public function recal_balance($id)
  {
    return $this->db->set("balance", "amount - used", FALSE)->where('id', $id)->update($this->tb);
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_valid_budget($id)
  {
    $rs = $this->db
    ->where('id', $id)
    ->where('active', 1)
    ->where('from_date <=', now())
    ->where('to_date >=', now())
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('from_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('to_date <=', to_date($ds['to_date']));
    }

    if(isset($ds['year']) && $ds['year'] != 'all')
    {
      $this->db->where('budget_year', $ds['year']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db
    ->order_by('code', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('from_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('to_date <=', to_date($ds['to_date']));
    }

    if(isset($ds['year']) && $ds['year'] != 'all')
    {
      $this->db->where('budget_year', $ds['year']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function has_transection($id)
  {
    $order = $this->db->where('budget_id', $id)->count_all_results('orders');
    $sponsor = $this->db->where('budget_id', $id)->count_all_results('sponsor');

    return ($order > 0 OR $sponsor > 0 ) ? TRUE : FALSE;
  }


  public function count_members($id)
  {
    return $this->db->where('budget_id', $id)->count_all_results('sponsor');
  }


  public function get_members($id)
  {
    $rs = $this->db
    ->select('sp.*, cs.name AS customer_name')
    ->from('sponsor AS sp')
    ->join('customers AS cs', 'sp.customer_code = cs.code', 'left')
    ->where('sp.budget_id', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_commit_amount($id, $order_code = NULL)
  {
    $amount = 0.00;

    $this->db
    ->select_sum('od.total_amount')
    ->from('order_details AS od')
    ->join('orders AS or', 'od.order_code = or.code', 'left')
    ->where('or.role', 'P')
    ->where('or.state <', 8)
    ->where('or.status !=', 2)
    ->where('or.is_expired', 0)
    ->where('or.budget_id', $id)
    ->where('od.is_expired', 0)
    ->where('od.is_cancle', 0)
    ->where('od.is_complete', 0);

    if( ! empty($order_code))
    {
      $this->db->where('or.code !=', $order_code);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      $amount = $rs->row()->total_amount;
    }

    return $amount;
  }
} //--- end class
  ?>
