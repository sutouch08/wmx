<?php
function select_product_group($code = '')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_group_model');
  $result = $CI->product_group_model->get_all();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


 ?>
