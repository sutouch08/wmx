<?php
function select_product_main_group($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_main_group_model');

  $list = $ci->product_main_group_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_group($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_group_model');

  $list = $ci->product_group_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_segment($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_segment_model');

  $list = $ci->product_segment_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_class($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_class_model');

  $list = $ci->product_class_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_family($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_family_model');

  $list = $ci->product_family_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_type($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_type_model');

  $list = $ci->product_type_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_kind($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_kind_model');

  $list = $ci->product_kind_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_gender($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_gender_model');

  $list = $ci->product_gender_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_collection($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_collection_model');

  $list = $ci->product_collection_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_sport_type($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_sport_type_model');

  $list = $ci->product_sport_type_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_product_brand($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/product_brand_model');

  $list = $ci->product_brand_model->get_all();

  $ds = "";

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-name="'.$rs->name.'">'.$rs->name.'</option>';
    }
  }

  return $ds;
}
 ?>
