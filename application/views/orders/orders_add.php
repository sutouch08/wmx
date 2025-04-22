<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" id="customer_code" name="customer_code" value="" />
	</div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <label>ชื่อลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า[ออนไลน์]</label>
		<input type="text" class="form-control input-sm" id="customer_ref" name="cust_ref" value="" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" id="channels" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>การชำระเงิน</label>
		<select class="form-control input-sm" name="payment" id="payment" required>
			<option value="">ทั้งหมด</option>
			<?php echo select_payment_method(); ?>
		</select>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิงออเดอร์</label>
		<input type="text" class="form-control input-sm" name="reference" id="reference" value="" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>แปรสภาพ</label>
		<select class="form-control input-sm" name="transformed" id="transformed">
			<option value="0">No</option>
			<option value="1">Yes</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>Pre Order</label>
		<select class="form-control input-sm" name="is_pre_order" id="is_pre_order">
			<option value="0">No</option>
			<option value="1">Yes</option>
		</select>
  </div>

	<div class="col-lg-3 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="width-100" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

  <div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
	<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
