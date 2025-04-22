<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$row_span = 3;
$config 		= array(
	"row" => 14,
	"total_row" => $total_row,
	"font_size" => 10
);


$this->xprinter->config($config);

$page  = '';
$page .= $this->xprinter->doc_header();

$this->xprinter->add_title($title);

$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>".getConfig('COMPANY_FULL_NAME')."</span>",
	'address1' => getConfig('COMPANY_ADDRESS1'),
	'address2' => getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE'),
	'phone' => 'โทร: '. getConfig('COMPANY_PHONE'),
	'taxid' => 'Tax ID: ' . getConfig('COMPANY_TAX_ID')
);

if(!empty($vender))
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; color:#910DDE;'>Vendor</span>",
		"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>{$vender->name}</span>",
		"address1" => "{$vender->address}",
		"phone" => "โทร: {$vender->phone}",
		"taxid" => "Tax ID: {$vender->tax_id}"
	);
}
else
{
	$header['left']['B'] = array(
		"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; color:orange;'>Vendor</span>",
		"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>{$po->vender_name}</span>",
		"taxid" => "Tax ID: -"
	);
}


//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $po->code),
	array('label' => 'วันที่', 'value' => thai_date($po->date_add, FALSE, '/')),
	array('label' => 'ครบกำหนด', 'value' => thai_date($po->due_date, FALSE, '/')),
	array('label' => 'ผู้สั่ง', 'value' => display_name($po->user))
);


$this->xprinter->add_header($header);


$row = $this->xprinter->row;
$total_page  = $this->xprinter->total_page;
$total_qty = 0; //--  จำนวนรวม
$total_amount= 0;  //--- มูลค่ารวม(หลังหักส่วนลด ไมีรวมภาษี

//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
	array("#", "width:5%; text-align:center;"),
	array("รายละเอียด", "width:50%; text-align:center;"),
	array("จำนวน", "width:20%; text-align:right;"),
	array("ราคา/หน่วย", "width:10%; text-align:right;"),
	array("มูลค่า", "width:15%; text-align:right;")
);

$this->xprinter->add_subheader($thead);

//***************************** กำหนด css ของ td *****************************//
$pattern = array(
	"text-align:center;",
	"text-align:left;",
	"text-align:right;",
	"text-align:right;",
	"text-align:right;"
);

$this->xprinter->set_pattern($pattern);

//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
	array("ผู้อนุมัติ", "","วันที่"),
	array("ผู้ตรวจสอบ", "","วันที่"),
  array("ผู้จัดทำ", "","วันที่")
);

$this->xprinter->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
	$page .= $this->xprinter->page_start();
	$page .= $this->xprinter->top_page();
	$page .= $this->xprinter->content_start();
	$page .= $this->xprinter->table_start();
	if($po->status == 'D')
	{
		$page .= '
		<div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
		<span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
		</div>';
	}

	$i = 0;

	while($i < $row)
	{
		$rs = isset($details[$index]) ? $details[$index] : array();

		if(!empty($rs))
		{
			$detail = $rs->product_code .' : '.$rs->product_name;
			$data = array(
				$n,
				inputRow($detail),
				number($rs->qty).(empty($rs->unit_name) ? '' : ' '.$rs->unit_name),
				number($rs->price, 2),
				number($rs->line_total, 2)
			);

			$total_qty += $rs->qty;
			$total_amount += $rs->line_total;
		}
		else
		{
			$data = array("", "", "", "","");
		}

		$page .= $this->xprinter->print_row($data);
		$n++;
		$i++;
		$index++;
	}

	$page .= $this->xprinter->table_end();

	if($this->xprinter->current_page == $this->xprinter->total_page)
	{
		$qty = number($total_qty, 2);
		$net_amount = number($total_amount, 2);
		$remark = $po->remark;
	}
	else
	{
		$qty  = "";
		$net_amount = "";
		$remark = "";
	}

	$subTotal = array();

	if($this->xprinter->current_page == $this->xprinter->total_page)
	{
		//--- จำนวนรวม   ตัว
		$sub_qty  = '<td class="width-60 text-center" style="border:0;">';
		$sub_qty .= '<span class=""></span>';
		$sub_qty .= '</td>';
		$sub_qty .= '<td class="width-20" style="border:0;">';
		$sub_qty .= '</td>';
		$sub_qty .= '<td class="width-20 text-right" style="border:0;"></td>';

		array_push($subTotal, array($sub_qty));
	}

	$sub_price  = '<td rowspan="2" class="width-60 subtotal-first-row middle text-center"></td>';
	$sub_price .= '<td class="width-20 subtotal subtotal-first-row">';
	$sub_price .=  '<strong class="'.$this->xprinter->text_color.'">จำนวนรวม</strong>';
	$sub_price .= '</td>';
	$sub_price .= '<td class="width-20 subtotal subtotal-first-row text-right">';
	$sub_price .=  $qty;
	$sub_price .= '</td>';
	array_push($subTotal, array($sub_price));

	//--- ยอดสุทธิ
	$sub_net  = "";

	$sub_net .= '<td class="subtotal subtotal-last-row">';
	$sub_net .=  '<strong class="'.$this->xprinter->text_color.'">จำนวนเงินรวมทั้งสิ้น</strong>';
	$sub_net .= '</td>';
	$sub_net .= '<td class="subtotal subtotal-last-row text-right">';
	$sub_net .=  $net_amount;
	$sub_net .= '</td>';

	array_push($subTotal, array($sub_net));

	if($this->xprinter->current_page == $this->xprinter->total_page)
	{
		//--- หมายเหตุ
		$sub_remark  = '<td colspan="3" class="no-border" style="white-space:normal;"><span class="'.$this->xprinter->text_color.'"><b>หมายเหตุ : </b></span>'.$remark.'</td>';
		array_push($subTotal, array($sub_remark));
	}


	$page .= $this->xprinter->print_sub_total($subTotal);
	$page .= $this->xprinter->content_end();
	$page .= $this->xprinter->footer;
	$page .= $this->xprinter->page_end();
	$total_page --;
	$this->xprinter->current_page++;
}

$page .= $this->xprinter->doc_footer();

echo $page;
 ?>
