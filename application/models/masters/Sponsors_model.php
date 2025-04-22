<?php
class Sponsors_model extends CI_Model
{
  private $tb = "sponsor";
  private $td = "sponsor_budget";

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


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function is_exists($code)
  {
    $count = $this->db->where('customer_code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get($id)
  {
    $rs = $this->db
    ->select('sp.*, cs.name AS customer_name')
    ->from('sponsor AS sp')
    ->join('customers AS cs', 'sp.customer_code = cs.code', 'left')
    ->where('sp.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_customer_code($code)
  {
    $rs = $this->db
    ->select('sp.*, cs.name AS customer_name')
    ->from('sponsor AS sp')
    ->join('customers AS cs', 'sp.customer_code = cs.code', 'left')
    ->where('sp.customer_code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('sp.*, c.name AS customer_name')
    ->from('sponsor AS sp')
    ->join('customers AS c', 'sp.customer_code = c.code', 'left');

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('sp.customer_code', $ds['code'])
      ->or_like('c.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('sp.budget_code', $ds['reference']);
    }

    if( isset($ds['year']) && $ds['year'] != 'all')
    {
      $this->db->where('sp.year', $ds['year']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('sp.active', $ds['active']);
    }

    $rs = $this->db
    ->order_by('sp.customer_code', 'ASC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('sponsor AS sp')
    ->join('customers AS c', 'sp.customer_code = c.code', 'left');

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('sp.customer_code', $ds['code'])
      ->or_like('c.name', $ds['code'])
      ->group_end();
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('sp.budget_code', $ds['reference']);
    }

    if( isset($ds['year']) && $ds['year'] != 'all')
    {
      $this->db->where('sp.year', $ds['year']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('sp.active', $ds['active']);
    }

    return $this->db->count_all_results();
  }

} //--- end class
  ?>
