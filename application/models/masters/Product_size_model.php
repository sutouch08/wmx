<?php
class Product_size_model extends CI_Model
{
  private $tb = "product_size";

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

    $this->db->order_by('position', 'ASC')->order_by('code', 'ASC');
    $this->db->limit($perpage, $offset);
    $rs = $this->db->get($this->tb);

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

    return FALSE;
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



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }




  public function get_data($code = '', $name = '', $perpage = '', $offset = '')
  {
    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }

    $this->db->order_by('position', 'ASC');
    $this->db->order_by('code', 'ASC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get($this->tb);

    return $rs->result();
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



  public function count_members($code)
  {
    $this->db->select('active')->where('size_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }


  public function is_sap_exists($code)
  {
    $rs = $this->mc->select('Code')->where('Code', $code)->get('SIZE');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add_sap_size(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('SIZE', $ds);
    }

    return FALSE;
  }



  public function update_sap_size($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->where('Code', $code)->update('SIZE', $ds);
    }

    return FALSE;
  }

}
?>
