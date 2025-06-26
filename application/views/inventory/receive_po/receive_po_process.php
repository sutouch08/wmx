<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<style>
	.table tr>td {
		padding:3px !important;
	}
</style>

<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-white btn-success top-btn" onclick="validateReceive()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัสผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm text-center e" value="<?php echo $doc->vender_code; ?>" disabled />
  </div>
  <div class="col-lg-4-harf col-md-8 col-sm-8 col-xs-12 padding-5">
  	<label>ผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vender_name; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ใบสั่งซื้อ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลัง</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->warehouse_code . ' | '.$doc->warehouse_name; ?>" disabled />
	</div>
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-lg-5-harf col-md-7 col-sm-7 col-xs-8 padding-5">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled/>
	</div>
  <div class="col-lg-10-harf col-md-9 col-sm-8 col-xs-4 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo receive_status_text($doc->status); ?>" disabled />
	</div>
</div>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 hidden-xs padding-5">
		<input type="number" class="form-control input-sm text-center" id="qty" value="1.00" placeholder="จำนวน"/>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 hidden-xs padding-5">
		<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off" autofocus />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 hidden-xs padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="checkBarcode()"><i class="fa fa-check"></i> ตกลง</button>
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo $allow_over_po; ?>">
</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered border-1" style="margin-bottom:0px; min-width:700px;">
			<thead>
				<tr class="font-size-12">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">จำนวน[ส่ง]</th>
					<th class="fix-width-100 text-center">จำนวน[รับ]</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php $totalQty = 0; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<?php $uid = $rs->id."-".$rs->po_detail_id; ?>
			<tr class="font-size-11" id="row-<?php echo $uid; ?>">
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle text-center">
					<input type="text" class="form-control input-sm text-center text-label" id="qty-<?php echo $uid; ?>" value="<?php echo number($rs->qty); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-center text-label receive-qty"
						id="receive-qty-<?php echo $uid; ?>"
						data-id="<?php echo $rs->id; ?>"
						data-uid="<?php echo $uid; ?>"
						data-limit="<?php echo $rs->qty; ?>"
						data-basecode="<?php echo $rs->po_code; ?>"
						data-baseline="<?php echo $rs->po_detail_id; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-name="<?php echo $rs->product_name; ?>"
						data-unit="<?php echo $rs->unit; ?>"
						value="" onchange="sumReceive()"/>

						<input type="hidden"
						class="<?php echo $rs->barcode; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-limit="<?php echo $rs->qty; ?>"
						value="<?php echo $uid; ?>"
						/>
				</td>
			</tr>
			<?php $no++; ?>
			<?php $totalQty += $rs->qty; ?>
		<?php endforeach; ?>
			<tr>
				<td colspan="3" class="text-right">รวม</td>
				<td class=""><input type="text" class="form-control input-sm text-center text-label" id="total-qty" value="<?php echo number($totalQty); ?>" readonly/></td>
				<td class=""><input type="text" class="form-control input-sm text-center text-label" id="total-receive" value="0" readonly/></td>
			</tr>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
