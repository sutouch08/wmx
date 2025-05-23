<?php
class Discount_policy_model extends CI_Model
{
  private $tb = "discount_policy";
  
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    return $this->db->insert($this->tb, $ds);
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    $result = new stdClass();
    $result->status = TRUE;

    $this->db->trans_start();

    //---- remove rule from policy before delete
    $this->db->set('id_policy', NULL)->where('id_policy', $id)->update('discount_rule');

    //--- delete policy
    $this->db->where('id', $id)->delete($this->tb);

    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
       $result->status = FALSE;
       $result->message = $this->db->error();
    }

    return $result;
  }





  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_code($id)
  {
    $rs = $this->db->select('code')
    ->where('id', $id)
    ->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('name')
    ->where('id', $id)
    ->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( ! empty($ds['start_date']))
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if( ! empty($ds['end_date']))
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( ! empty($ds['start_date']))
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if( ! empty($ds['end_date']))
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_data($code, $name, $active, $start, $end, $perpage = '', $offset = '')
  {
    $qr = "SELECT * FROM discount_policy WHERE code != '' ";

    if($code != "")
    {
      $qr .= "AND code LIKE '%".$code."%' ";
    }

    if($name != "")
    {
      $qr .= "AND name LIKE '%".$name."%' ";
    }

    if($active != 2)
    {
      $qr .= "AND active = ".$active." ";
    }

    if($start != "" && $end != "")
    {
      $qr .= "AND (start_date >= '".db_date($start)."' OR end_date <= '".db_date($end)."') ";

    }

    $qr .= "ORDER BY code DESC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }





  public function get_policy_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return array();
  }




  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM discount_policy WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



  public function search($txt)
  {
    $rs = $this->db->select('id')
    ->like('code', $txt)
    ->like('name', $txt)
    ->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }

} //--- end class

 ?>
