<?php
function po_status_color($status = 'P')
{
  $default = '#FFFFFF';

  $colors = array(
    'P' => '#FFFFFF',
    'O' => '#bae3ff',
    'C' => '#f4ffe7',
    'D' => '#d3d3d3'
  );

  return ! empty($colors[$status]) ? $colors[$status] : $default;
}

function po_status_text($status = 'P')
{
  $default = 'Draft';

  $text = array(
    'P' => 'Draft',
    'O' => 'Open',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return ! empty($text[$status]) ? $text[$status] : $default;
}

 ?>
