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
      return $this->db->insert($this->tb, $ds);
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


  public function get($code)
  {
    $rs = $this->db
    ->select('m.*')
    ->select('fw.name AS from_warehouse_name, tw.name AS to_warehouse_name')
    ->select('u.uname, u.name AS display_name')
    ->from('move AS m')
    ->join('warehouse AS fw', 'm.from_warehouse = fw.code', 'left')
    ->join('warehouse AS tw', 'm.to_warehouse = tw.code', 'left')
    ->join('user AS u', 'm.accept_by = u.uname', 'left')
    ->where('m.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_details($code)
  {
    $rs = $this->db
    ->select('md.*, pd.barcode, fz.name AS from_zone_name, tz.name AS to_zone_name')
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

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db
    ->select('md.*, pd.barcode, fz.name AS from_zone_name, tz.name AS to_zone_name')
    ->from('move_detail AS md')
    ->join('products AS pd', 'md.product_code = pd.code', 'left')
    ->join('zone AS fz', 'md.from_zone = fz.code', 'left')
    ->join('zone AS tz', 'md.to_zone = tz.code', 'left')
    ->where('md.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }
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


  public function get_accept_list($code)
  {
    $rs = $this->db
    ->select('m.is_accept, m.accept_by, m.accept_on')
    ->select('u.uname, u.name AS display_name')
    ->from('move_detail AS m')
    ->join('zone AS z', 'm.to_zone = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('m.move_code', $code)
    ->where('m.must_accept', 1)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->group_by('z.user_id')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_accept_all($code)
  {
    $count = $this->db
    ->where('move_code', $code)
    ->where('must_accept', 1)
    ->where('is_accept', 0)
    ->count_all_results($this->td);

    if($count == 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function accept_all($code, $uname)
  {
    $arr = array(
      'is_accept' => 1,
      'accept_by' => $uname,
      'accept_on' => now()
    );

    $this->db
    ->where('move_code', $code)
    ->where('must_accept', 1)
    ->where('is_accept', 0);

    return $this->db->update($this->td, $arr);
  }


  public function is_owner_zone($code, $user_id)
  {
    $count = $this->db
    ->from('move_detail AS md')
    ->join('zone AS zn', 'md.to_zone = zn.code', 'left')
    ->where('md.move_code', $code)
    ->where('md.must_accept', 1)
    ->where('zn.user_id', $this->_user->id)
    ->count_all_results();

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function accept_zone($code, $zone_code, $uname)
  {
    $arr = array(
      'is_accept' => 1,
      'accept_by' => $uname,
      'accept_on' => now()
    );

    return $this->db->where('move_code', $code)->where('to_zone', $zone_code)->update($this->td, $arr);
  }


  public function get_my_zone($code, $user_id)
  {
    $rs = $this->db
    ->select('md.to_zone')
    ->from('move_detail AS md')
    ->join('zone AS zn', 'md.to_zone = zn.code', 'left')
    ->where('move_code', $code)
    ->where('md.must_accept', 1)
    ->where('zn.user_id', $user_id)
    ->group_by('md.to_zone')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->to_zone;
    }

    return NULL;
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

    return FALSE;
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


  public function add_temp(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tm, $ds);
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
    ->select('move_temp.*, products.barcode')
    ->from($this->tm)
    ->join('products', 'products.code = move_temp.product_code', 'left')
    ->where('move_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
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

    return FALSE;
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
    return $this->db->where('move_code', $code)->delete($this->td);
  }


  public function drop_temp($id)
  {
    return $this->db->where('id', $id)->delete($this->tm);
  }


  public function drop_all_detail($code)
  {
    return $this->db->where('move_code', $code)->delete($this->td);
  }


  public function drop_detail($id)
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

    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_detail($code)
  {
    $rs = $this->db->select('id')->where('move_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_temp($code)
  {
    $rs = $this->db->select('id')->where('move_code', $code)->get($this->tm);
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
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
      $this->db->where('from_warehouse', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      if( $ds['status'] == 5)
      {
        $this->db->where('is_expire', 1);
      }
      else
      {
        $this->db->where('is_expire', 0)->where('status', $ds['status']);
      }
    }

    if(isset($ds['must_accept']) && $ds['must_accept'] != 'all')
    {
      $this->db->where('must_accept', $ds['must_accept']);
    }

    if($ds['is_export'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_export']);
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
    $this->db
    ->select('m.*')
    ->select('w.name AS warehouse_name')
    ->select('u.name AS display_name')
    ->from('move AS m')
    ->join('warehouse AS w', 'm.from_warehouse = w.code', 'left')
    ->join('user AS u', 'm.user = u.uname', 'left');

    if( ! empty($ds['code']))
    {
      $this->db->like('m.code', $ds['code']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('m.from_warehouse', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('m.user', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      if( $ds['status'] == 5)
      {
        $this->db->where('m.is_expire', 1);
      }
      else
      {
        $this->db->where('m.is_expire', 0)->where('m.status', $ds['status']);
      }
    }

    if(isset($ds['must_accept']) && $ds['must_accept'] != 'all')
    {
      $this->db->where('m.must_accept', $ds['must_accept']);
    }

    if($ds['is_export'] != 'all')
    {
      $this->db->where('m.is_exported', $ds['is_export']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('m.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('m.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('m.code', 'DESC')->limit($perpage, $offset)->get();

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


  public function exported($code)
  {
    return $this->db->set('is_exported', 1)->where('code', $code)->update($this->tb);
  }


  public function get_un_export_list($from_date, $to_date, $limit)
  {
    $rs = $this->db
    ->select('code')
    ->where('date_add >=', $from_date)
    ->where('date_add <=', $to_date)
    ->where('status', 1)
    ->where('is_exported', 0)
    ->order_by('date_add', 'ASC')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update($this->tb);
  }


  public function must_accept($code)
  {
    $rs = $this->db
    ->from('move_detail AS md')
    ->join('zone AS z', 'md.to_zone = z.code', 'left')
    ->where('md.move_code', $code)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->where('z.user_id >', 0)
    ->count_all_results();

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

}
 ?>
