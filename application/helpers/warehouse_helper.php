<?php
function select_warehouse($id = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/warehouse_model');
  $options = $ci->warehouse_model->get_list();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" data-code="'.$rs->code.'" '.is_selected($id, $rs->id).'>'.$rs->code." : ".$rs->name.'</option>';
    }
  }

  return $sc;
}


function warehouse_name($id)
{
  $ci =& get_instance();
  $ci->load->model('masters/warehouse_model');
  return $ci->warehouse_model->get_name_by_id($id);
}

function warehouse_code($id)
{
  $ci =& get_instance();
  $ci->load->model('masters/warehouse_model');
  return $ci->warehouse_model->get_code($id);
}

 ?>
