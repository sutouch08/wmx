<?php
class Adjust_model extends CI_Model
{
  private $tb = "adjust";
  private $td = "adjust_detail";

  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
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


  public function get_details($adjust_id)
  {
    if( ! empty($adjust_id))
    {
      $rs = $this->db
      ->select('ad.*')
      ->select('zn.name AS zone_name')
      ->from('adjust_detail AS ad')
      ->join('products AS pd', 'ad.product_id = pd.id', 'left')
      ->join('zone AS zn', 'ad.zone_id = zn.id', 'left')
      ->where('ad.adjust_id', $adjust_id)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_exists_detail($adjust_id, $product_id, $zone_id)
  {
    $rs = $this->db
    ->where('adjust_id', $adjust_id)
    ->where('product_id', $product_id)
    ->where('zone_id', $zone_id)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
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


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
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


  public function update_detail($id, $arr)
  {
    return $this->db->where('id', $id)->update($this->td, $arr);
  }


  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($adjust_id)
  {
    return $this->db->where('adjust_id', $adjust_id)->delete($this->td);
  }


  public function valid_detail($id)
  {
    return $this->db->set('valid', '1')->where('id', $id)->update($this->td);
  }


  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update($this->td);
  }


  public function unvalid_details($adjust_id)
  {
    return $this->db->set('valid', '0')->where('adjust_id', $adjust_id)->update($this->td);
  }


  public function cancel_details($adjust_id)
  {
    return $this->db->set('is_cancel', 1)->where('adjust_id', $adjust_id)->update($this->td);
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

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }
    else
    {
      if($ds['approve'] !== 'all')
      {
        $this->db->where('status !=', 2);
      }
    }

    if($ds['approve'] !== 'all')
    {
      $this->db->where('approve', $ds['approve']);
    }

    return $this->db->count_all_results($this->tb);
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

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_id', $ds['warehouse']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }
    else
    {
      if($ds['approve'] !== 'all')
      {
        $this->db->where('status !=', 2);
      }
    }

    if($ds['approve'] !== 'all')
    {
      $this->db->where('approve', $ds['approve']);
    }

    $this->db->order_by('id', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
} //--- End Model
 ?>
