<?php

function discount_rule_in($txt)
{
  $sc = "0";
  $CI =& get_instance();
  $CI->load->model('discount/discount_rule_model');
  $rs = $CI->discount_rule_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc .= ", ".$cs->id;
    }
  }

  return $sc;
}


function showItemDiscountLabel($item_price, $item_disc, $unit)
{
	$disc = 0.00;
	//---	ถ้าเป็นการกำหนดราคาขาย
	if($item_price > 0)
	{
		$disc = 'Price '.$item_price;
	}
	else
	{
		$symbal = $unit == 'percent' ? '%' : '';
		$disc = $item_disc.' '.$symbal;
	}

	return $disc;
}


function parse_discount_to_label(array $ds = array())
{
	$disc = 0.00;

  if( ! empty($ds))
  {
    if(isset($ds['price']) && $ds['price'] > 0)
    {
      $disc = "Price ".$ds['price'];
    }
    else
    {
      $disc_1 = empty($ds['disc_1']) ? 0 : (round($ds['disc_1'], 2) . (empty($ds['unit_1']) ? '%' : ($ds['unit_1'] == 'percent' ? '%' : '')));
      $disc_2 = empty($ds['disc_2']) ? 0 : (round($ds['disc_2'], 2) . (empty($ds['unit_2']) ? '%' : ($ds['unit_2'] == 'percent' ? '%' : '')));
      $disc_3 = empty($ds['disc_3']) ? 0 : (round($ds['disc_3'], 2) . (empty($ds['unit_3']) ? '%' : ($ds['unit_3'] == 'percent' ? '%' : '')));

      $disc  = $disc_1;
      $disc .= $disc_2 == 0 ? "" : "+".$disc_2;
      $disc .= $disc_3 == 0 ? "" : "+".$disc_3;
    }
  }

	return $disc;
}


function discount_label($type, $price, $disc1, $disc2, $disc3)
{
	$disc = 0.00;
	//---	ถ้าเป็นการกำหนดราคาขาย
	//--- N = netprice , P = percent
	if($type == 'N')
	{
		$disc = $price;
	}
	else
	{
		$disc = round($disc1, 2)."%";
		$disc .= ($disc1 > 0 && $disc2 > 0) ? "+".round($disc2)."%" : "";
		$disc .= ($disc2 > 0 && $disc3 > 0) ? "+".round($disc3)."%" : "";
		$disc .= ($disc3 > 0 && $disc4 > 0) ? "+".round($disc4)."%" : "";
		$disc .= ($disc4 > 0 && $disc5 > 0) ? "+".round($disc5)."%" : "";
	}

	return $disc;
}
?>
