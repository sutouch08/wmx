<?php
class Channels_model extends CI_Model
{
  private $tb = "channels";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
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
      $this->db->where('id', $id);
      return $this->db->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if(isset($ds['is_online']) && $ds['is_online'] != 'all')
    {
      $this->db->where('is_online', $ds['is_online']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if(isset($ds['is_online']) && $ds['is_online'] != 'all')
    {
      $this->db->where('is_online', $ds['is_online']);
    }

    $this->db->order_by('position', 'ASC')->order_by('id', 'ASC');

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_channels($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() == 1 )
    {
      return $rs->row();
    }

    return array();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() == 1 )
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_default()
  {
    $rs = $this->db->where('is_default', 1)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_online_list()
  {
    $rs = $this->db->where('is_online', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_code($name)
  {
    $rs = $this->db->select('code')->where('name', $name)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function is_exists($code, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


	public function get_channels_array()
	{
		$rs = $this->db->get($this->tb);

		if($rs->num_rows() > 0)
		{
			$arr = array();
			foreach($rs->result() as $ds)
			{
				$arr[$ds->code] = $ds->name;
			}

			return $arr;
		}

		return NULL;
	}


  public function get_all()
  {
    $rs = $this->db->order_by('position', 'ASC')->order_by('code', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function has_transection($code)
  {
    $count = $this->db->where('channels_code', $code)->count_all_results('orders');

    return $count > 0 ? TRUE : FALSE;
  }

}
?>
