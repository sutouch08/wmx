<?php
function setToken($token)
{
	$ci =& get_instance();
	$cookie = array(
		'name' => 'file_download_token',
		'value' => $token,
		'expire' => 3600,
		'path' => '/'
	);

	return $ci->input->set_cookie($cookie);
}


function parsePhoneNumber($phone, $length = 10)
{
	$find = [" ", "-", "+"];
  $rep = ["", "", ""];
	$length = $length * -1;

  if($phone != "")
  {
    $phone = trim($phone);
    $phone = str_replace($find, $rep, $phone);
    $phone = substr($phone, $length);

    return $phone;
  }

  return NULL;
}


function parseSubDistrict($ad, $province)
{
	if(! empty($ad))
	{
		if(isBangkok($province))
		{
			$find = [' ', 'แขวง'];
			$rep = ['', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'แขวง', 0, 0);
		}
		else
		{
			$find = [' ', 'ต.', 'ตำบล'];
			$rep = ['', '', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'ตำบล', 0, 0);
		}

	}

	return NULL;
}


function parseDistrict($ad, $province)
{
	if(! empty($ad))
	{
		if(isBangkok($province))
		{
			$find = [' ', 'เขต'];
			$rep = ['', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'เขต', 0, 0);
		}
		else
		{
			$find = [' ', 'อ.', 'อำเภอ'];
			$rep = ['', '', ''];
			$ad = str_replace($find, $rep, $ad);
			return substr_replace($ad, 'อำเภอ', 0, 0);
		}
	}

	return NULL;
}


function parseProvince($ad)
{
	if(! empty($ad))
	{
		$find = [' ', 'จ.', 'จังหวัด', '.'];
		$rep = ['', '', '', ''];
		$ad = str_replace($find, $rep, $ad);
		$ad = substr_replace($ad, 'จังหวัด', 0, 0);

		if(isBangkok($ad))
		{
			$ad = 'จังหวัดกรุงเทพมหานคร';
		}

		return $ad;
	}

	return NULL;
}


function isBangkok($province)
{
	$list = array(
		'จังหวัดกรุงเทพมหานคร',
		'จังหวัดกรุงเทพ',
		'จังหวัดกรุงเทพฯ',
		'จ.กรุงเทพมหานคร',
		'จ.กรุงเทพ',
		'จ.กรุงเทพฯ',
		'กรุงเทพ',
		'กรุงเทพฯ',
		'กรุงเทพมหานคร',
		'กทม',
		'กทม.',
		'ก.ท.ม.'
	);

	if( ! empty($province))
	{
		foreach($list as $val)
		{
			if($province == $val)
			{
				return TRUE;
			}
		}
	}

	return FALSE;
}


//---	ตัดข้อความแล้วเติม ... ข้างหลัง
function limitText($str, $length)
{
	$txt = '...';
	if( strlen($str) >= $length)
	{
		return mb_substr($str, 0, $length).$txt;
	}
	else
	{
		return $str;
	}
}


function is_selected($val, $select)
{
  return $val === $select ? 'selected' : '';
}


function is_checked($val1, $val2)
{
  return $val1 == $val2 ? 'checked' : '';
}


function is_active($val, $showX = TRUE)
{
	if($val == 1 OR $val == '1' OR $val == 'Y' OR $val == 'y' OR $val === TRUE)
	{
		return '<i class="fa fa-check green"></i>';
	}
	else
	{
		return $showX ? '<i class="fa fa-times red"></i>' : NULL;
	}
}


function get_filter($postName, $cookieName, $defaultValue = "")
{
  $ci =& get_instance();
  $sc = '';

  if($ci->input->post($postName) !== NULL)
  {
    $sc = trim($ci->input->post($postName));
    $ci->input->set_cookie(array('name' => $cookieName, 'value' => $sc, 'expire' => 3600 , 'path' => '/'));
  }
  else if($ci->input->cookie($cookieName) !== NULL)
  {
    $sc = $ci->input->cookie($cookieName);
  }
  else
  {
    $sc = $defaultValue;
  }

	return $sc;
}


function clear_filter($cookies)
{
  if(is_array($cookies))
  {
    foreach($cookies as $cookie)
    {
      delete_cookie($cookie);
    }
  }
  else
  {
    delete_cookie($cookies);
  }
}


function set_rows($value = 20)
{
  $value = $value > 300 ? 300 : $value;

  $arr = array(
    'name' => 'rows',
    'value' => $value,
    'expire' => 259200,
    'path' => '/'
  );

  return set_cookie($arr);
}


function get_rows()
{
  $rows = get_cookie('rows');

	return $rows <= 0 ? 20 : ($rows > 300 ? 300 : $rows);
}


function number($val, $digit = 0)
{
  return number_format($val, $digit);
}


function ac_format($val, $digit = 0)
{
	return $val == 0 ? '-' : number_format($val, $digit);
}


function getConfig($code)
{
  $ci =& get_instance();
  $rs = $ci->db->select('value')->where('code', $code)->get('config');
  if($rs->num_rows() == 1)
  {
    return $rs->row()->value;
  }

	return NULL;
}


function get_vat_amount($amount, $vat = NULL, $type = 'I')
{
	//--- type I = include, E = exclude
	$re_vat = 0;

	if($amount > 0)
	{
		if($vat === NULL)
		{
			$vat = getConfig('SALE_VAT_RATE');
		}

		if($type == 'E')
		{
			$re_vat = $amount * ($vat * 0.01);
		}
		else
		{
			$re_vat = ($amount * $vat) / (100+$vat);
		}
	}


	return round($re_vat,6);
}


function remove_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat	= ($vat + 100) / 100;
		return round($amount/$re_vat, 6);
	}

	return round($amount, 6);
}


//---- remove discount percent return price after discount
function get_price_after_discount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$price = $price - ($price *($disc * 0.01));
	}

	return $price;
}


//--- return discount amount calculate from price and discount percentage
function get_discount_amount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$amount = $price * ($disc * 0.01);
	}
	else
	{
		$amount = 0;
	}

	return $amount;
}


function add_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat = $vat * 0.01;
		return round(($amount * $re_vat) + $amount, 6);
	}

	return round($amount, 6);
}


function set_error($key, $name = "data")
{
	$error = array(
		'insert' => "Insert {$name} failed.",
		'update' => "Update {$name} failed.",
		'delete' => "Delete {$name} failed.",
		'permission' => "You don't have permission to perform this operation.",
		'required' => "Missing required parameter.",
		'exists' => "{$name} already exists.",
		'status' => "Invalid document status",
		'notfound' => "Data or Document number not found",
		'transection' => "Unable to delete {$name} because transections exists or link to other module."
	);

	$ci =& get_instance();

	$ci->error = (!empty($error[$key]) ? $error[$key] : "Unknow error.");
}


function set_message($message)
{
  $ci =& get_instance();
  $ci->session->set_flashdata('success', $message);
}


//--- return null if blank value
function get_null($value)
{
	return $value === '' ? NULL : $value;
}


//--- return TRUE if value ==  1 else return FALSE;
function is_true($value)
{
	if($value === 1 OR $value === '1' OR $value === 'Y' OR $value === TRUE OR $value === 'TRUE' OR $value === 'true')
	{
		return TRUE;
	}

	return FALSE;
}


//---- check permission for add edit delete
function can_do($pm)
{
	if( ! empty($pm))
	{
		return ($pm->can_add OR $pm->can_edit OR $pm->can_delete OR $pm->can_approve) ? TRUE : FALSE;
	}

	return FALSE;
}


function get_zero($value)
{
	return $value === NULL ? 0 : $value;
}


function pagination_config( $base_url, $total_rows = 0, $perpage = 20, $segment = 3)
{
    $rows = get_rows();
    $input_rows  = '<p class="pull-right pagination">';
    $input_rows .= 'ทั้งหมด '.number($total_rows).' รายการ';
    $input_rows .= '<input type="number" name="set_rows" id="set_rows" class="input-mini text-center margin-left-15 margin-right-10" value="'.$rows.'" />';
    $input_rows .= 'ต่อหน้า ';
    $input_rows .= '<buton class="btn btn-success btn-xs" type="button" onClick="set_rows()">แสดง</button>';
    $input_rows .= '</p>';

		$config['full_tag_open'] 		= '<nav><ul class="pagination">';
		$config['full_tag_close'] 		= '</ul>'.$input_rows.'</nav><hr class="hidden-xs">';
		$config['first_link'] 				= 'First';
		$config['first_tag_open'] 		= '<li>';
		$config['first_tag_close'] 		= '</li>';
		$config['next_link'] 				= 'Next';
		$config['next_tag_open'] 		= '<li>';
		$config['next_tag_close'] 	= '</li>';
		$config['prev_link'] 			= 'prev';
		$config['prev_tag_open'] 	= '<li>';
		$config['prev_tag_close'] 	= '</li>';
		$config['last_link'] 				= 'Last';
		$config['last_tag_open'] 		= '<li>';
		$config['last_tag_close'] 		= '</li>';
		$config['cur_tag_open'] 		= '<li class="active"><a href="#">';
		$config['cur_tag_close'] 		= '</a></li>';
		$config['num_tag_open'] 		= '<li>';
		$config['num_tag_close'] 		= '</li>';
		$config['uri_segment'] 		= $segment;
		$config['per_page']			= $perpage;
		$config['total_rows']			= $total_rows != false ? $total_rows : 0 ;
		$config['base_url']				= $base_url;
		return $config;
}


function genUid($lenght = 13)
{
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes"))
		{
        $bytes = random_bytes(ceil($lenght / 2));
    }
		elseif (function_exists("openssl_random_pseudo_bytes"))
		{
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    }
		else
		{
        $bytes = uniqid('', TRUE);
    }

    return substr(bin2hex($bytes), 0, $lenght);
}

function action_logs_label($action = NULL)
{
	$arr = array(
		'create' => 'สร้างโดย',
		'update' => 'แก้ไขโดย',
		'approve' => 'อนุมัติโดย',
		'reject' => 'ปฏิเสธโดย',
		'cancel' => 'ยกเลิกโดย'
	);

	return empty($arr[$action]) ? 'Unknow' : $arr[$action];
}


function status_text($status = NULL)
{
	$arr = array(
		'-1' => 'Draft',
		'0' => 'Pending',
		'1' => 'Success',
		'2' => 'Canceled',
		'9' => 'Closed',
		'O' => 'Open',
		'P' => 'Pending',
		'R' => 'In progress',
		'C' => 'Completed',
		'D' => 'Canceled'
	);

	return empty($arr[$status]) ? 'Unknow' : $arr[$status];
}

function status_label($status = NULL)
{
	$label = '<span class="grey">Unknow</span>';

	switch($status)
	{
		case '-1' :
			$label = '<span class="purple">Draft</span>';
			break;
		case '0' :
			$label = '<span class="orange">Pending</span>';
			break;
		case '1' :
			$label = '<span class="green">Success</span>';
			break;
		case '2' :
			$label = '<span class="red">Canceled</span>';
			break;
		case 'P' :
			$label = '<span class="orange">Pending</span>';
			break;
		case 'R' :
			$label = '<span class="purple">Pending</span>';
			break;
		case 'C' :
			$label = '<span class="green">Completed</span>';
			break;
		case 'D' :
			$label = '<span class="red">Canceled</span>';
			break;
	}

	return $label;
}


function approval_text($status = 'P')
{
	$arr = array(
		'A' => 'Approved',
		'P' => 'Pending',
		'R' => 'Rejected'
	);

	return empty($arr[$status]) ? 'Pending' : $arr[$status];
}

function approval_label($status = NULL)
{
	$label = '<span class="orange">Pending</span>';

	switch($status)
	{
		case 'A' :
			$label = '<span class="green">Approved</span>';
			break;
		case 'R' :
			$label = '<span class="red">Rejected</span>';
			break;
	}

	return $label;
}

 ?>
