<?php
function select_all_collection($code = NULL)
{
  $CI =& get_instance();
  $CI->load->model('masters/product_collection_model');
  $result = $CI->product_collection_model->get_all();
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
