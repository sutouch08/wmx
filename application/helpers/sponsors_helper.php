<?php
function select_budget($id = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('masters/sponsor_budget_model');

  $bud = $ci->sponsor_budget_model->get_all();

  if( ! empty($bud))
  {
    foreach($bud as $bd)
    {
      $ds .= "<option value=\"{$bd->id}\"
        data-code=\"{$bd->code}\"
        data-reference=\"{$bd->reference}\"
        data-from=\"".thai_date($bd->from_date)."\"
        data-to=\"".thai_date($bd->to_date)."\"
        data-amount=\"{$bd->amount}\"
        data-used=\"{$bd->used}\"
        data-balance=\"{$bd->balance}\"
        data-active=\"{$bd->active}\"
        data-year=\"{$bd->budget_year}\" "
        .is_selected($id, $bd->id).">
        {$bd->code} [{$bd->reference}] มูลค่า ".number($bd->amount, 2)
        ."</option>";
    }
  }

  return $ds;
}

 ?>
