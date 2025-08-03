<?php
class Adjust_model extends CI_Model
{
  private $tb = "adjust";
  private $td = "adjust_detail";
  private $logs = "adjust_logs";

  public function __construct()
  {
    parent::__construct();
  }

  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->logs, $ds);
    }

    return FALSE;
  }


  public function get_logs($code)
  {
    $rs = $this->db->where('code', $code)->get($this->logs);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get($code)
  {
    if( ! empty($code))
    {
      $rs = $this->db->where('code', $code)->get($this->tb);
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    if( ! empty($code))
    {
      $rs = $this->db
      ->where('adjust_code', $code)
      ->get($this->td);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_exists_detail($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('adjust_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }
  }


  public function update_detail($id, $arr)
  {
    return $this->db->where('id', $id)->update($this->td, $arr);
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('adjust_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_detail_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where("id", $id)->update("adjust_detail");
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('adjust_code', $code)->delete($this->td);
  }


  public function delete_details_by_ids(array $ids = array())
  {
    if( ! empty($ids))
    {
      return $this->db->where_in('id', $ids)->delete($this->td);
    }

    return FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));

    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['remark']))
    {
      $this->db->like('remark', $ds['remark']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));

    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['remark']))
    {
      $this->db->like('remark', $ds['remark']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $this->db->order_by('code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function do_approve($code, $user)
  {
    $arr = array(
      'is_approved' => 1,
      'approver' => $user,
      'approve_date' => now()
    );

    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function un_approve($code)
  {
    $arr = array(
      'is_approved' => 0,
      'approver' => NULL,
      'approve_date' => now()
    );

    return $this->db->where('code', $code)->update($this->tb, $arr);
  }

  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }
} //--- End Model
 ?>
