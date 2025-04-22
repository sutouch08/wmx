<?php

function get_cancle_qty_by_product_and_zone($item_code, $zone_code)
{
  $ci =& get_instance();

  $ci->load->model('inventory/cancle_model');

  $cQty = $ci->cancle_model->get_product_cancle_zone($zone_code, $item_code);

  return $cQty > 0 ? $cQty : 0;
}

?>
