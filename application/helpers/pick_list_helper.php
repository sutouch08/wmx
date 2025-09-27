<?php
function status_color($status = 'P')
{
  $statusColor = [
    'C' => 'closed',
    'D' => 'canceled',
    'P' => 'draft',
    'R' => 'release',
    'Y' => 'picking'
  ];

  return empty($statusColor[$status]) ? 'draft' : $statusColor[$status];
}


function status_text($status = 'P')
{
  $statusText = [
    'C' => 'Closed',
    'D' => 'Canceled',
    'P' => 'Draft',
    'R' => 'Released',
    'Y' => 'Picking'
  ];

  return empty($statusText[$status]) ? 'draft' : $statusText[$status];
}

  ?>
