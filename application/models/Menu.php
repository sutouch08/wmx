<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Model{

  public function __construct()
  {
    parent::__construct();
  }

  public function get_active_menu_groups($type = 'side')
  {
    $rs = $this->db
    ->where('type', $type)
    ->where('active', 1)
    ->order_by('position', 'ASC')
    ->get('menu_group');
    return $rs->result();
  }


  public function get_menu_groups()
  {
    $this->db->order_by('position', 'ASC');
    $rs = $this->db->get('menu_group');
    return $rs->result();
  }

  public function get_sub_groups($group_code = NULL)
  {
    if($group_code !== NULL)
    {
      $this->db->where('group_code', $group_code);
    }

    $rs = $this->db->order_by('group_code', 'ASC')->order_by('position', 'ASC')->get('menu_sub_group');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }  

  public function get_menus_sub_group($group_code, $all = FALSE)
  {
    $this->db->where('group_code', $group_code);

    if(! $all)
    {
      $this->db->where('active', 1);
    }

    $this->db->order_by('position', 'ASC');

    $rs = $this->db->get('menu_sub_group');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_menus_by_sub_group(string $sub_group_code, string $group_code, $all = FALSE)
  {
    $this->db
    ->where('group_code', $group_code)
    ->where('sub_group', $sub_group_code);
    if(! $all)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->where('url IS NOT NULL')
    ->order_by('position', 'ASC')
    ->get('menu');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_menus_non_sub_group($group_code, $all = FALSE)
  {
    $this->db
    ->where('group_code', $group_code)
    ->where('sub_group IS NULL', NULL, FALSE);

    if(! $all)
    {
      $this->db->where('active', 1);
    }

    $this->db->order_by('position', 'ASC');
    $rs = $this->db->get('menu');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_menus_by_group($group_code, $all = TRUE)
  {
    $this->db
    ->where('group_code', $group_code)
    ->where('active', 1);

    if($all === FALSE)
    {
      $this->db->where('sub_group IS NULL', NULL, FALSE)
      ->where('url IS NOT NULL');
    }

    $this->db->order_by('position', 'ASC');
    $rs = $this->db->get('menu');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_valid_menus_by_group($group_code, $all = TRUE)
  {
    $this->db
    ->where('group_code', $group_code)
    ->where('active', 1);

    if($all === FALSE)
    {
      $this->db->where('sub_group IS NULL', NULL, FALSE)
      ->where('url IS NOT NULL');
    }

    $this->db->order_by('position', 'ASC');
    $rs = $this->db->where('valid', 1)->get('menu');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    return FALSE;

  }


  public function count_menu($group_code)
  {
    return $this->db->where('group_code', $group_code)->count_all_results('menu');
  }

  public function is_active($menu_code)
  {
    $rs = $this->db->where('code', $menu_code)->where('active', 1)->get('menu');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


}
?>
