<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-sm-12 col-xs-12 padding-5 padding-top-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" value="" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center e" name="customer_code" id="customer_code" autofocus/>
	</div>
	<div class="col-lg-7-harf col-md-7 col-sm-7 col-xs-6 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm e" name="customer_name" id="customer_name" value="" readonly/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>รหัสโซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_code" id="zone_code" />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
		<label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm e" name="zone_name" id="zone_name" readonly/>
	</div>

	<div class="col-lg-5 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm e" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
