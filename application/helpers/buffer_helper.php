<?php
function get_buffer_qty_by_product_and_zone($item_code, $zone_code)
{
  $ci =& get_instance();

  $ci->load->model('inventory/buffer_model');

  $bQty = $ci->buffer_model->get_product_buffer_zone($zone_code, $item_code);

  return $bQty > 0 ? $bQty : 0;
}

 ?>
