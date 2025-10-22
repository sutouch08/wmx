<?php
function status_text($status = NULL)
{
  $ds = array(
    'P' => 'Draft',
    'A' => 'Approval',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return empty($ds[$status]) ? 'Unknow' : $ds[$status];
}

function status_color($status = 'P')
{
  $statusColor = [
    'P' => 'draft',
    'A' => 'approval',
    'C' => 'closed',
    'D' => 'canceled'
  ];

  return empty($statusColor[$status]) ? 'draft' : $statusColor[$status];
}
 ?>
