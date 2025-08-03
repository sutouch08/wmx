<?php
function adjust_status_label($status)
{
  $label = array(
    'P' => 'Draft',
    'A' => 'Approval',
    'R' => 'Rejected',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return isset($label[$status]) ? $label[$status] : 'Unknow';
}

 ?>
