<?php
function select_customer_group($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/customer_group_model');
  $options = $ci->customer_group_model->get_all();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;

}


function select_customer_kind($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/customer_kind_model');
  $options = $ci->customer_kind_model->get_all();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_type($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/customer_type_model');
  $options = $ci->customer_type_model->get_all();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_class($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/customer_class_model');
  $options = $ci->customer_class_model->get_all();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_area($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/customer_area_model');
  $options = $ci->customer_area_model->get_all();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}


function customer_in($txt)
{
  $sc = array('0');
  $ci =& get_instance();
  $ci->load->model('masters/customers_model');
  $rs = $ci->customers_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc[] = $cs->code;
    }
  }

  return $sc;
}



 ?>
