<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-white btn-purple" onclick="save(3)"><i class="fa fa-save"></i> บันทึกรอรับ</button>
			<button type="button" class="btn btn-white btn-success" onclick="save(1)"><i class="fa fa-save"></i> บันทึกรับทันที</button>
		<?php endif; ?>		
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
  	<label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่บิล</label>
		<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center edit" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
		<label>ชื่อโซน]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-9 padding-5">
  	<label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">save</label>
<?php 	if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
					<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">แก้ไข</button>
					<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">Update</button>
<?php	endif; ?>
	</div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="invoice_code" id="invoice_code" value="<?php echo $doc->invoice; ?>" />

<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-danger btn-100" onclick="deleteChecked()"><i class="fa fa-trash"></i>&nbsp; ลบ</button>
	</div>
</div>

<hr class=""/>
<form id="detailsForm" method="post" action="<?php echo $this->home.'/add_details/'.$doc->code; ?>">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered border-1 padding-3" style="margin-bottom:0px; min-width:940px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">
						<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
						<span class="lbl"></span>
					</th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-200">สินค้า</th>
					<th class="fix-width-120">เลขที่บิล</th>
					<th class="fix-width-80 text-right">ราคา</th>
					<th class="fix-width-100 text-right">ส่วนลด</th>
					<th class="fix-width-80 text-right">จำนวน</th>
					<th class="fix-width-80 text-right">คืน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  foreach($details as $rs) : ?>
		<tr class="font-size-11" id="row_<?php echo $no; ?>">
			<td class="middle text-center">
				<input type="checkbox" class="chk ace" data-id="<?php echo $rs->id; ?>" value="<?php echo $no; ?>">
				<span class="lbl"></span>
			</td>
			<td class="middle text-center no"><?php echo $no; ?></td>
			<td class="middle <?php echo $no; ?>"><?php echo $rs->product_code; ?></td>
			<td class="middle"><input type="text" class="form-control input-sm text-label" style="font-size:11px !important;" value="<?php echo $rs->product_name; ?>" readonly/></td>
			<td class="middle"><?php echo $rs->order_code; ?></td>
			<td class="middle text-right"><?php echo $rs->price; ?></td>
			<td class="middle text-right"><?php echo $rs->discount_percent.' %'; ?></td>
			<td class="middle text-right inv_qty"><?php echo round($rs->qty); ?></td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-right input-qty"
					id="qty-<?php echo $no; ?>"
					data-no="<?php echo $no; ?>"
					data-id="<?php echo $rs->id; ?>"
					data-pdcode="<?php echo $rs->product_code; ?>"
					data-pdname="<?php echo $rs->product_name; ?>"
					data-invoice="<?php echo $rs->order_code; ?>"
					data-order="<?php echo $rs->order_code; ?>"
					data-sold="<?php echo round($rs->qty); ?>"
					data-price="<?php echo $rs->price; ?>"
					data-sell="<?php echo $rs->sell_price; ?>"
					data-discount="<?php echo $rs->discount_percent; ?>"
					value="<?php echo $rs->qty; ?>"
					onchange="recalRow(<?php echo $no; ?>)"	/>
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right amount-label text-label" id="amount-<?php echo $no; ?>" value="<?php echo number($rs->amount, 2); ?>" readonly/>
			</td>
		</tr>
<?php
		$no++;
		$total_qty += $rs->qty;
		$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
<?php endif; ?>
				<tr>
					<td colspan="8" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

</form>


<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
