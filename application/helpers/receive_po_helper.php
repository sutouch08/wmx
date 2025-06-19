<?php
function receive_status_color($status = 'P')
{
  $default = '#FFFFFF';

  $colors = array(
    'P' => '#FFFFFF',
    'O' => '#fbe4ff',
    'R' => '#f1e9ff',
    'C' => '#f4ffe7',
    'D' => '#d3d3d3'
  );

  return ! empty($colors[$status]) ? $colors[$status] : $default;
}

function receive_status_text($status = 'P')
{
  $default = 'Draft';

  $text = array(
    'P' => 'Draft',
    'O' => 'Open',
    'R' => 'Receiving',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return ! empty($text[$status]) ? $text[$status] : $default;
}

 ?>
