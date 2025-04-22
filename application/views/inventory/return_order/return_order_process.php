<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 3 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="saveAsDraft()"><i class="fa fa-save"></i> Save As Draft</button>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="saveReturn()"><i class="fa fa-save"></i> Save</button>
		<?php endif; ?>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
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
  <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 padding-5">
  	<label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
  </div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />

<hr class="margin-top-10 margin-bottom-10"/>
<?php $this->load->view('inventory/return_order/return_order_control'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1 table-padding-3" style="margin-bottom:0px; min-width:940px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-200">สินค้า</th>
					<th class="fix-width-120">เลขที่บิล</th>
					<th class="fix-width-80 text-right">ราคา</th>
					<th class="fix-width-100 text-right">ส่วนลด</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">รับ</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php  $total_qty = 0; ?>
				<?php  $total_amount = 0; ?>
				<?php if(!empty($details)) : ?>
					<?php  $no = 1; ?>
					<?php  foreach($details as $rs) : ?>
						<?php $sell_price = $rs->price * (1 - ($rs->discount_percent * 0.01)); ?>
						<?php $rs->sell_price = $sell_price; ?>
						<tr class="font-size-11" id="row_<?php echo $rs->id; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle <?php echo $rs->id; ?>"><?php echo $rs->product_code; ?></td>
							<td class="middle"><input type="text" class="form-control input-sm text-label" style="font-size:11px !important;" value="<?php echo $rs->product_name; ?>" readonly/></td>
							<td class="middle"><?php echo $rs->order_code; ?></td>
							<td class="middle text-right"><?php echo $rs->price; ?></td>
							<td class="middle text-right"><?php echo $rs->discount_percent.' %'; ?></td>
							<td class="middle text-right">
								<input type="text"
								class="form-control input-sm text-center text-label return-qty"
								id="return-qty-<?php echo $rs->id; ?>"
								value="<?php echo number($rs->qty, 2); ?>" readonly/>
							</td>
							<td class="middle">
								<input type="number"
								class="form-control input-sm text-center input-qty text-label <?php echo $rs->barcode; ?>"
								id="qty-<?php echo $rs->id; ?>"
								data-no="<?php echo $no; ?>"
								data-id="<?php echo $rs->id; ?>"
								data-pdcode="<?php echo $rs->product_code; ?>"
								data-pdname="<?php echo $rs->product_name; ?>"
								data-invoice="<?php echo $rs->order_code; ?>"
								data-order="<?php echo $rs->order_code; ?>"
								data-sold="<?php echo round($rs->qty, 2); ?>"
								data-price="<?php echo $rs->price; ?>"
								data-sell="<?php echo $rs->sell_price; ?>"
								data-discount="<?php echo $rs->discount_percent; ?>"
								value="<?php echo $rs->receive_qty; ?>"
								onchange="recalRow(<?php echo $rs->id; ?>)"	/>
							</td>
							<td class="middle text-right">
								<input type="text" class="form-control input-sm text-right amount-label text-label" id="amount-<?php echo $rs->id; ?>" value="0.00" readonly/>
							</td>
						</tr>
						<?php		$no++;	?>
					<?php  endforeach; ?>
				<?php endif; ?>
				<tr>
					<td colspan="7" class="middle text-right" style="padding:8px !important;">รวม</td>
					<td class="middle text-center" id="total-qty" style="padding:8px !important;"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-amount" style="padding:8px !important;"><?php echo number($total_amount, 2); ?></td>
				</tr>
			</tbody>
		</table>

		<?php if( ! empty($barcode_list)) : ?>
			<?php foreach($barcode_list as $bc) : ?>
				<input type="hidden" class="bc" id="<?php echo $bc->barcode; ?>" data-sku="<?php echo $bc->product_code; ?>"  value="<?php echo $bc->product_code; ?>" />
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>



<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
