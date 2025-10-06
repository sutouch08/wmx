<?php
class Move_model extends CI_Model
{
  private $tb = "move";
  private $td = "move_detail";
  private $tm = "move_temp";

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
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
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
      return $this->db->where('move_code', $code)->update($this->td, $ds);
    }

    return FALSE;
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


  public function get_details($code)
  {
    $rs = $this->db
    ->select('md.*, pd.barcode, pd.name AS product_name, fz.name AS from_zone_name, tz.name AS to_zone_name')
    ->from('move_detail AS md')
    ->join('products AS pd', 'md.product_code = pd.code', 'left')
    ->join('zone AS fz', 'md.from_zone = fz.code', 'left')
    ->join('zone AS tz', 'md.to_zone = tz.code', 'left')
    ->where('md.move_code', $code)
    ->get();

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


  public function get_id($move_code, $product_code, $from_zone, $to_zone)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('to_zone', $to_zone)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function update_temp(array $ds = array())
  {
    if( ! empty($ds))
    {
      $id = $this->get_temp_id($ds['move_code'], $ds['product_code'], $ds['zone_code']);

      if( ! empty($id))
      {
        return $this->update_temp_qty($id, $ds['qty']);
      }
      else
      {
        return $this->add_temp($ds);
      }
    }

    return FALSE;
  }


  public function get_temp_id($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_sum_temp_stock($product_code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $product_code)->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return get_zero($rs->row()->qty);
    }

    return 0;
  }


  public function get_temp_detail($move_code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp($temp_id)
  {
    $rs = $this->db->where('id', $id)->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_temp(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tm, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update($this->tm);
  }


  public function get_move_temp($code)
  {
    $rs = $this->db
    ->select('move_temp.*, products.name AS product_name, products.barcode')
    ->from($this->tm)
    ->join('products', 'products.code = move_temp.product_code', 'left')
    ->where('move_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_product($code, $product_code)
  {
    $rs = $this->db
    ->where('move_code', $code)
    ->where('product_code', $product_code)
    ->get($this->tm);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_qty($move_code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_move_qty($move_code, $product_code, $from_zone)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('move_code', $move_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('valid', 0)
    ->get($this->td);

    return intval($rs->row()->qty);
  }


  public function drop_zero_temp()
  {
    return $this->db->where('qty <', 1)->delete($this->tm);
  }


  public function drop_all_temp($code)
  {
    return $this->db->where('move_code', $code)->delete($this->tm);
  }


  public function drop_temp($id)
  {
    return $this->db->where('id', $id)->delete($this->tm);
  }


  public function delete_selected_temp(array $ids = array())
  {
    if( ! empty($ids))
    {
      return $this->db->where_in('id', $ids)->delete($this->tm);
    }

    return FALSE;
  }


  public function drop_all_detail($code)
  {
    return $this->db->where('move_code', $code)->delete($this->td);
  }


  public function drop_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function is_exists($code, $old_code = NULL)
  {
    if( ! empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_detail($code)
  {
    $count = $this->db->where('move_code', $code)->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_temp($code)
  {
    $count = $this->db->where('move_code', $code)->count_all_results($this->tm);

    return $count > 0 ? TRUE : FALSE;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update($this->tb);
  }


  public function valid_all_detail($code, $valid)
  {
    return $this->db->set('valid', $valid)->where('move_code', $code)->update($this->td);
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_warehouse_in($txt)
  {
    $rs = $this->db
    ->select('code')
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->get('warehouse');

    $arr = array('none');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $arr[] = $wh->code;
      }
    }

    return $arr;
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
}
 ?>
