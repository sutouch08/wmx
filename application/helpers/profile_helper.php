<?php
function select_profile($id = NULL)
{
  $sc = '';

  $ci =& get_instance();
  $ci->load->model('users/profile_model');
  $pf = $ci->profile_model->get_all();

  if( ! empty($pf))
  {
    foreach($pf as $rs)
    {
      if($rs->id > 0)
      {
        $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
      }
      else
      {
        if($ci->_SuperAdmin)
        {
          $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
        }
      }
    }
  }

  return $sc;
}


 ?>
