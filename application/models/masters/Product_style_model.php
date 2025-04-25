<?php
class Product_style_model extends CI_Model
{
  private $tb = "product_style";

  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return  $this->db->insert($this->tb, $ds);
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


  public function delete($code)
  {
    return  $this->db->where('code', $code)->delete($this->tb);
  }


  public function get_all()
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

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

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
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

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
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


  public function count_members($code)
  {
    $this->db->select('active')->where('style_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }
}
?>
