<?php
function trStatusBgColor($status = 'P')
{
  $co = "";

  switch($status)
  {
    case 'P' :
      $co = "#FFFFFF";
    break;
    case 'O' :
      $co = "#DDF0F9";
    break;
    case 'R' :
      $co = "#FBE4FF";
    break;
    case 'C' :
      $co = "#F4FFE7";
    break;
    case 'D' :
      $co = "#F7C3BF";
    break;
    default :
      $co = "#FFFFFF";
    break;
  }

  return "background-color:{$co};";
}

function trStatusText($status = 'P')
{
  $sc = array(
    'P' => 'Draft',
    'O' => 'Pending',
    'R' => 'Inprogress',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return empty($sc[$status]) ? 'Unknow' : $sc[$status];
}
 ?>
