<?php
function po_status_color($status = 'P')
{
  $default = '#FFFFFF';

  $colors = array(
    'O' => '#bae3ff', //--- open
    'P' => '#fbe4ff', //--- partial
    'C' => '#f4ffe7', //--- Closed
    'D' => '#d3d3d3' //--- Canceled
  );

  return ! empty($colors[$status]) ? $colors[$status] : $default;
}

function po_status_text($status = 'P')
{
  $default = 'Draft';

  $text = array(
    'P' => 'Partial',
    'O' => 'Open',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return ! empty($text[$status]) ? $text[$status] : $default;
}

 ?>
