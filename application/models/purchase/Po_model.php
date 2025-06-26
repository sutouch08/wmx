<?php
class Po_model extends CI_Model
{
  private $tb = "po";
  private $td = "po_details";

  public function __construct()
  {
    parent::__construct();
  }

  //--- get document data
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


  public function get_by_reference($reference, $option = 'NOT_CANCEL')
  {
    //--- option : NOT_CANCEL, ALL

    $this->db->where('reference', $reference);

    if($option === 'NOT_CANCEL')
    {
      $this->db->where('status !=', 'D');
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  //--- get po detail in document
  public function get_details($code)
  {
    $rs = $this->db->where('po_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
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


  public function get_detail_by_product($po_code, $product_code)
  {
    $rs = $this->db
    ->where('po_code', $po_code)
    ->where('product_code', $product_code)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail_by_product_and_line_num($po_code, $product_code, $line_num)
  {
    $rs = $this->db
    ->where('po_code', $po_code)
    ->where('product_code', $product_code)
    ->where('line_num', $line_num)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details_exclude_ids($po_code, array $ids = array())
  {
    if( ! empty($ids))
    {
      $rs = $this->db->where('po_code', $po_code)->where_not_in('id', $ids)->get($this->td);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

  //--- add new document
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


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
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


  public function update_detail($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('po_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function un_close_details($code)
  {
    return $this->db
    ->set('line_status', 'O')
    ->set('update_user', $this->_user->uname)
    ->where('po_code', $code)
    ->where('open_qty >', 0)
    ->update($this->td);
  }


  public function update_open_qty($id, $qty)
  {
    return $this->db->set('open_qty', "open_qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_all_details($code)
  {
    return $this->db->where('po_code', $code)->delete($this->td);
  }


  //--- delete rows by id array
  public function delete_rows_id(array $ids = array())
  {
    return $this->db->where_in('id', $ids)->delete($this->td);
  }


  public function is_all_done($code)
  {
    $openQty = $this->db
    ->where('po_code', $code)
    ->where_in('line_status', ['O', 'P'])
    ->where('open_qty >', 0)
    ->count_all_results($this->td);

    return $openQty > 0 ? FALSE : TRUE;
  }


  public function is_all_open($code)
  {
    $count = $this->db->where('po_code', $code)->where('line_status !=', 'O')->count_all_results($this->td);

    return $count > 0 ? FALSE : TRUE;
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

    if( ! empty($ds['vender']))
    {
      $this->db
      ->group_start()
      ->like('vender_code', $ds['vender'])
      ->or_like('vender_name', $ds['vender'])
      ->group_end();
    }

    if( isset($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('id', 'DESC')->limit($perpage, $offset)->get($this->tb);

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

    if( ! empty($ds['vender']))
    {
      $this->db
      ->group_start()
      ->like('vender_code', $ds['vender'])
      ->or_like('vender_name', $ds['vender'])
      ->group_end();
    }

    if( isset($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_exists_detail($po_code, $product_code)
  {
    $count = $this->db
    ->where('po_code', $po_code)
    ->where('product_code', $product_code)
    ->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_received($code)
  {
    $count = $this->db->where('po_code', $code)->where("open_qty <", "qty", FALSE)->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function recal_total($code)
  {
    $sum = $this->get_total_summary($code);

    if( ! empty($sum))
    {
      return $this->db
      ->set('total_qty', get_zero($sum->qty))
      ->set('total_open_qty', get_zero($sum->open_qty))
      ->where('code', $code)
      ->update($this->tb);
    }

    return FALSE;
  }


  private function get_total_summary($code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->select_sum('open_qty')
    ->where('po_code', $code)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
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

    return $rs->row()->code;
  }


} //---- end class
 ?>
