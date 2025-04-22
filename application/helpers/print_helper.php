<?php
function doc_type($role)
{
	switch($role)
	{
		case 'S' :
			$content	= "order";
			$title 		= "Packing List";
		break;

		case 'C' :
			$content = "consign";
			$title = "ใบส่งของ / ใบแจ้งหนี้  สินค้าฝากขาย";
		break;

		case 'N' :
			$content = "consign";
			$title = "ใบส่งของ / ใบแจ้งหนี้  สินค้าฝากขาย";
		break;

		case 'U' :
			$content = "support";
			$title = "ใบส่งของ / รายการเบิกอภินันทนาการ";
		break;

		case 'P' :
			$content = "sponsor";
			$title = "ใบส่งของ / รายการสปอนเซอร์สโมสร";
		break;

		case 'T' :
			$content = "transform";
			$title = "ใบส่งของ / ใบเบิกสินค้าเพื่อแปรรูป";
		break;

		case 'L' :
			$content = "lend";
			$title = "ใบส่งของ / ใบยืมสินค้า";
		break;

		case 'R' :
			$content 	= "requisition";
			$title 		= "ใบส่งของ / ใบเบิกสินค้า";
		break;

		default :
			$content = "order";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
		break;
	}

	return array("content"=>$content, "title"=>$title);
}


function get_header($order)
{
	$CI =& get_instance();

	//---	เบิกสปอนเซอร์
	if( $order->role == 'P')
	{
		$header	= array(
			"ผู้รับ" => $order->customer_name,
			"วันที่" => thai_date($order->date_add, FALSE, '/'),
			"ผู้เบิก" => $CI->user_model->get_name($order->user),
			"เลขที่" => $order->code,
			"ผู้ทำรายการ" =>  $CI->user_model->get_name($order->user)
		);
	}
	else if($order->role == 'L' )
	{
		$header		= array(
			"เลขที่"	=> $order->code,
			"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
			"ผู้ยืม"	=> $order->customer_name,
			"ผู้ทำรายการ" => $CI->user_model->get_name($order->user)
		);
	}
	else if( $order->role == 'R' || $order->role == 'T' )
	{
		$header		= array(
			"ลูกค้า"	=> $order->customer_name,
			"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
			"ผู้เบิก"	=> $CI->user_model->get_name($order->user),
			"เลขที่"	=> $order->code
		);
	}
	else if( $order->role == 'U')
	{
		$header	= array(
			"ผู้เบิก"	=> $order->customer_name,
			"วันที่"	=> thai_date($order->date_add, FALSE, '/'),
			"ผู้ทำรายการ"	=> $CI->user_model->get_name($order->user),
			"เลขที่"	=> $order->code
		);
	}
	else if( $order->role == 'C' OR $order->role == 'N')
	{
		$header	= array(
			"ลูกค้า"	 => $order->customer_name,
			"วันที่"		=> thai_date($order->date_add, FALSE, '/'),
			"พนักงาน" => $CI->user_model->get_name($order->user),
			"เลขที่" => $order->code
		);
	}
	else
	{
		$ref = !empty($order->reference) ? '['.$order->reference.']' : '';
		$header	= array(
			"ลูกค้า"	=> $order->customer_name,
			"วันที่"		=> thai_date($order->date_add, FALSE, '/'),
			"พนักงาน" => $CI->user_model->get_name($order->user),
			"เลขที่" => $order->code.$ref
		);
	}

	return $header;
}

function barcodeImage($barcode)
{
	return '<img src="'.base_url().'assets/barcode/barcode.php?text='.$barcode.'" style="height:8mm;" />';
}


function inputRow($text, $style='')
{
  return '<input type="text" class="print-row" value="'.$text.'" style="'.$style.'" disabled/>';
}


define('BAHT_TEXT_NUMBERS', array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'));
define('BAHT_TEXT_UNITS', array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'));
define('BAHT_TEXT_ONE_IN_TENTH', 'เอ็ด');
define('BAHT_TEXT_TWENTY', 'ยี่');
define('BAHT_TEXT_INTEGER', 'ถ้วน');
define('BAHT_TEXT_BAHT', 'บาท');
define('BAHT_TEXT_SATANG', 'สตางค์');
define('BAHT_TEXT_POINT', 'จุด');

function baht_text($number, $include_unit = true, $display_zero = true)
{
	if (!is_numeric($number)) {
		return null;
	}

	$log = floor(log($number, 10));

	if ($log > 5) {
		$millions = floor($log / 6);
		$million_value = pow(1000000, $millions);
		$normalised_million = floor($number / $million_value);
		$rest = $number - ($normalised_million * $million_value);
		$millions_text = '';
		for ($i = 0; $i < $millions; $i++) {
			$millions_text .= BAHT_TEXT_UNITS[6];
		}
		return baht_text($normalised_million, false) . $millions_text . baht_text($rest, true, false);
	}

	$number_str = (string)floor($number);
	$text = '';
	$unit = 0;

	if ($display_zero && $number_str == '0') {
		$text = BAHT_TEXT_NUMBERS[0];
	} else for ($i = strlen($number_str) - 1; $i > -1; $i--) {
		$current_number = (int)$number_str[$i];

		$unit_text = '';
		if ($unit == 0 && $i > 0) {
			$previous_number = isset($number_str[$i - 1]) ? (int)$number_str[$i - 1] : 0;
			if ($current_number == 1 && $previous_number > 0) {
				$unit_text .= BAHT_TEXT_ONE_IN_TENTH;
			} else if ($current_number > 0) {
				$unit_text .= BAHT_TEXT_NUMBERS[$current_number];
			}
		} else if ($unit == 1 && $current_number == 2) {
			$unit_text .= BAHT_TEXT_TWENTY;
		} else if ($current_number > 0 && ($unit != 1 || $current_number != 1)) {
			$unit_text .= BAHT_TEXT_NUMBERS[$current_number];
		}

		if ($current_number > 0) {
			$unit_text .= BAHT_TEXT_UNITS[$unit];
		}

		$text = $unit_text . $text;
		$unit++;
	}

	if ($include_unit) {
		$text .= BAHT_TEXT_BAHT;

		$satang = explode('.', number_format($number, 2, '.', ''))[1];
		$text .= $satang == 0
		? BAHT_TEXT_INTEGER
		: baht_text($satang, false) . BAHT_TEXT_SATANG;
	} else {
		$exploded = explode('.', $number);
		if (isset($exploded[1])) {
			$text .= BAHT_TEXT_POINT;
			$decimal = (string)$exploded[1];
			for ($i = 0; $i < strlen($decimal); $i++) {
				$text .= BAHT_TEXT_NUMBERS[$decimal[$i]];
			}
		}
	}

	return $text;
}
 ?>
