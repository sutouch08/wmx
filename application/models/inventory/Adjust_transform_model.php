<?php
class Adjust_transform_model extends CI_Model
{
  private $tb = "adjust_transform";
  private $td = "adjust_transform_detail";

  public function __construct()
  {
    parent::__construct();
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

    return NULL;
  }


	public function get_sum_qty($code)
	{
		if( ! empty($code))
		{
			$rs = $this->db->select_sum('qty')->where('adjust_code', $code)->get($this->td);

			if($rs->num_rows() === 1)
			{
				return get_zero($rs->row()->qty);
			}
		}

		return 0;
	}


	public function get_sum_issued_qty($transform_code, $product_code)
	{
		$rs = $this->db
		->select_sum('qty')
		->from('adjust_transform_detail AS atd')
		->join('adjust_transform AS at', 'atd.adjust_code = at.code', 'left')
		->where('at.reference', $transform_code)
		->where('atd.product_code', $product_code)
		->where('atd.is_cancle', 0)
		->get();

		if($rs->num_rows() == 1)
		{
			return get_zero($rs->row()->qty);
		}

		return 0;
	}


  public function get_details($code)
  {
    if( ! empty($code))
    {
      $rs = $this->db
      ->select('adjust_transform_detail.*')
      ->select('products.name AS product_name, products.cost, products.price, products.unit_code')
      ->from('adjust_transform_detail')
      ->join('products', 'adjust_transform_detail.product_code = products.code')
      ->where('adjust_transform_detail.adjust_code', $code)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
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


  public function update_detail_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where("id", $id)->update($this->td);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('adjust_code', $code)->delete($this->td);
  }


  public function valid_detail($id)
  {
    return $this->db->set('valid', '1')->where('id', $id)->update($this->td);
  }


  public function unvalid_details($code)
  {
    return $this->db->set('valid', '0')->where('adjust_code', $code)->update($this->td);
  }


  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('adjust_code', $code)->update($this->td);
  }


  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->set('update_user', get_cookie('uname'))->where('code', $code)->update($this->tb);
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

    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['remark']))
    {
      $this->db->like('remark', $ds['remark']);
    }

    if($ds['status'] !== 'all')
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

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['remark']))
    {
      $this->db->like('remark', $ds['remark']);
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $this->db->order_by('code', 'DESC');

    $this->db->limit($perpage, $offset);

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

    return $rs->row()->code;
  }


  public function is_exists_code($code)
  {
    $rs = $this->db->where('code', $code)->count_all_results($this->tb);

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

} //--- End Model
 ?>
