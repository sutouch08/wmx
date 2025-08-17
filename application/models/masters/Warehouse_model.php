<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_model extends CI_Model
{
  private $tb = "warehouse";

  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db
    ->select('w.*, r.name AS role_name')
    ->from('warehouse AS w')
    ->join('warehouse_role AS r', 'w.role = r.id', 'left')
    ->where('w.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_id($code)
  {
    $rs = $this->db
    ->select('id')
    ->where('code', $code)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_name($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
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


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function get_all_role()
  {
    $rs = $this->db->get('warehouse_role');

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
      $this->db->like('code', $ds['code'])->or_like('name', $ds['code']);
    }

    if( ! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if( isset($ds['sell']) && $ds['sell'] != 'all')
    {
      $this->db->where('sell', $ds['sell']);
    }

    if( isset($ds['prepare']) && $ds['prepare'] != 'all')
    {
      $this->db->where('prepare', $ds['prepare']);
    }

    if( isset($ds['lend']) && $ds['lend'] != 'all')
    {
      $this->db->where('lend', $ds['lend']);
    }

    if( isset($ds['auz']) && $ds['auz'] != 'all')
    {
      $this->db->where('auz', $ds['auz']);
    }

    if( isset($ds['is_pos']) && $ds['is_pos'] != 'all')
    {
      $this->db->where('warehouse.is_pos', $ds['is_pos']);
    }

    if(isset($ds['is_consignment']) && $ds['is_consignment'] != 'all')
    {
      if($ds['is_consignment'] == 1)
      {
        $this->db->where('is_consignment', $ds['is_consignment']);
      }
      else
      {
        $this->db
        ->group_start()
        ->where('is_consignment', 0)
        ->or_where('is_consignment IS NULL', NULL, FALSE)
        ->group_end();
      }
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('w.*, r.name AS role_name')
    ->from('warehouse AS w')
    ->join('warehouse_role AS r', 'w.role = r.id');

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('w.code', $ds['code'])
      ->or_like('w.name', $ds['code'])
      ->group_end();
    }

    if(! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('w.role', $ds['role']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('w.active', $ds['active']);
    }

    if( isset($ds['sell']) && $ds['sell'] != 'all')
    {
      $this->db->where('w.sell', $ds['sell']);
    }

    if( isset($ds['prepare']) && $ds['prepare'] != 'all')
    {
      $this->db->where('w.prepare', $ds['prepare']);
    }

    if( isset($ds['lend']) && $ds['lend'] != 'all')
    {
      $this->db->where('w.lend', $ds['lend']);
    }

    if( isset($ds['auz']) && $ds['auz'] != 'all')
    {
      $this->db->where('w.auz', $ds['auz']);
    }

    if( isset($ds['is_pos']) && $ds['is_pos'] != 'all')
    {
      $this->db->where('w.is_pos', $ds['is_pos']);
    }

    if(isset($ds['is_consignment']) && $ds['is_consignment'] != 'all')
    {
      if($ds['is_consignment'] == 1)
      {
        $this->db->where('w.is_consignment', $ds['is_consignment']);
      }
      else
      {
        $this->db
        ->group_start()
        ->where('w.is_consignment', 0)
        ->or_where('w.is_consignment IS NULL', NULL, FALSE)
        ->group_end();
      }
    }

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all($active = NULL)
  {
    if( ! empty($active))
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->order_by('position', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sell_warehouse_list()
  {
    $rs = $this->db
    ->where_in('role', [1, 2, 3])
    ->where('active', 1)
    ->where('sell', 1)
    ->order_by('position', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- เอาเฉพาะคลังที่สามารถยืมสินค้าได้
  public function get_lend_warehouse_list()
  {
    $rs = $this->db
    ->where('active', 1)
    ->where('lend', 1)
    ->order_by('position', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_warehouse_list()
  {
    $rs = $this->db
    ->where('active', 1)
    ->order_by('code', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  ///---- คลังฝากขายทั้งหมด
  public function get_consign_warehouse_list()
  {
    $rs = $this->db->where('role', 2)->where('active', 1)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_transform_warehouse_list()
  {
    $rs = $this->db
    ->where('role', 7)
    ->where('active', 1)
    ->order_by('position', 'ASC')
    ->get('warehouse');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- เอาเฉพาะคลังฝากขายแท้
  public function get_consign_list()
  {
    $rs = $this->db
    ->where('role', 2)
    ->group_start()
    ->where('is_consignment IS NULL', NULL, FALSE)
    ->or_where('is_consignment', 0)
    ->group_end()
    ->where('active', 1)
    ->order_by('code', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- เอาเฉพาะคลังฝากขายเทียม
  public function get_consignment_list()
  {
    $rs = $this->db
    ->where('role', 2)
    ->where('is_consignment', 1)
    ->where('active', 1)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_common_list()
	{
		$rs = $this->db
		->where_in('role', array(1, 3, 4, 5))
		->where('active', 1)
		->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_lend_list()
	{
		$rs = $this->db
		->where('role', 8)
		->where('active', 1)
		->get('warehouse');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function count_zone($code)
  {
    return $this->db->where('warehouse_code', $code)->count_all_results('zone');
  }


  public function get_role_name($id)
  {
    $rs = $this->db->select('name')->where('id', $id)->get('warehouse_role');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function has_zone($code)
  {
    $count = $this->db->where('warehouse_code', $code)->count_all_results('zone');

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists($code)
  {
    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $code = NULL)
  {
    if( ! empty($code))
    {
      $this->db->where('code !=', $code);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_auz($code)
  {
    $count = $this->db->select('auz')->where('code', $code)->where('auz', 1)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


	public function is_consignment($code)
	{
		$count = $this->db->select('code')->where('code', $code)->where('is_consignment', 1)->count_all_results($this->tb);

		return $count > 0 ? TRUE : FALSE;
	}


  public function is_exists_zone($code)
  {
    $count = $this->db->where('warehouse_code', $code)->count_all_results('zone');

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_limit_amount($whsCode)
  {
    $rs = $this->db->select('limit_amount')->where('code', $whsCode)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return get_zero($rs->row()->limit_amount);
    }

    return 0.00;
  }

}
 ?>
