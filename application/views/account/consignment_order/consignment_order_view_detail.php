<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h4 class="title margin-top-5">
			<span class="goBack" onclick="goBack()"><i class="fa fa-angle-left fa-lg"></i></span>
			<?php echo $this->title; ?></h4>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
			<?php if($doc->status != 0 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-xs btn-white btn-danger top-btn btn-80" onclick="rollback()"><i class="fa fa-refresh"></i> Rollback</button>
			<?php endif; ?>
			<?php if($doc->status == 1) : ?>
				<?php if($this->pm->can_delete) : ?>
					<button type="button" class="btn btn-xs btn-white btn-danger top-btn btn-80" onclick="cancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> Cancel</button>
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-white btn-success top-btn btn-80" onclick="sendToErp('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i>  Send To ERP</button>
				<button type="button" class="btn btn-xs btn-info btn-white hidden-xs top-btn btn-80" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php endif; ?>

			<?php if($doc->status == 0) : ?>
				<button type="button" class="btn btn-xs btn-white btn-warning top-btn btn-80" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> Edit</button>
			<?php endif; ?>
		</div>
	</div><!-- End Row -->
<hr class=""/>
<?php if($doc->status == 2) : ?>
<?php 	$this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<?php $statusLabel = ['0' => 'Draft', '1' => 'Closed', '2' => 'Canceled']; ?>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-3-harf col-md-6-harf col-sm-6-harf col-xs-8 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->customer_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-6-harf col-sm-6-harf col-xs-8 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->ref_code; ?>" disabled>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $statusLabel[$doc->status]; ?>" disabled>
	</div>
	<div class="col-lg-9-harf col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled>
	</div>
</div>
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" >
<hr class="margin-top-15">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:900px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-250">สินค้า</th>
					<th class="fix-width-100 text-right">ราคา</th>
					<th class="fix-width-100 text-right">ส่วนลด</th>
					<th class="fix-width-80 text-right">จำนวน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php if(!empty($details)) : ?>
					<?php  $no = 1; ?>
					<?php  $totalQty = 0; ?>
					<?php  $totalAmount = 0; ?>
					<?php  foreach($details as $rs) : ?>
						<tr class="font-size-11">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->price,2); ?></td>
							<td class="middle text-right"><?php echo $rs->discount; ?></td>
							<td class="middle text-right"><?php echo number($rs->qty); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
						</tr>
						<?php  $no++; ?>
						<?php  $totalQty += $rs->qty; ?>
						<?php  $totalAmount += $rs->amount; ?>
					<?php endforeach; ?>
					<tr>
						<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
						<td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
						<td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
					</tr>
				<?php else : ?>
					<tr>
						<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
						<td id="total-qty" class="middle text-right">0</td>
						<td id="total-amount" class="middle text-right">0</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancel_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
