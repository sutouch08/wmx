<?php
function dispatch_status($status = NULL)
{
  $ds = array(
    'P' => 'Pending',
    'R' => 'Released',
    'S' => 'Shipped',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return empty($ds[$status]) ? 'Unknow' : $ds[$status];
}


function status_text($status = NULL)
{
  $ds = array(
    'P' => 'Pending',
    'R' => 'Released',
    'S' => 'Shipped',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return empty($ds[$status]) ? 'Unknow' : $ds[$status];
}

function status_color($status = 'P')
{
  $statusColor = [
    'P' => 'draft',
    'R' => 'release',
    'S' => 'shipped',
    'C' => 'closed',
    'D' => 'canceled'
  ];

  return empty($statusColor[$status]) ? 'draft' : $statusColor[$status];
}
 ?>
