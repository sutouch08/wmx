<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้ยืม</label>
		<select class="width-100 e" id="empID" onchange="zoneInit()">
			<option value="">เลือกพนักงาน</option>
			<?php echo select_active_employee(); ?>
		</select>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้รับ[คนสั่งงาน]</label>
		<input type="text" class="form-control input-sm e" name="user_ref" id="user_ref" value="" />
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>พื้นที่จัดเก็บ[คลังยืม]</label>
		<input type="text" class="form-control input-sm e" name="zone" id="zone" value="" />
	</div>


	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="width-100 e" name="warehouse" id="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_lend_warehouse(); ?>
		</select>
	</div>

	<div class="col-lg-8 col-md-7-harf col-sm-7-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="">
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">Submit</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="zone_code" id="zone_code" value="" />


<script>
	$('#empID').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/lend/lend.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/lend/lend_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
