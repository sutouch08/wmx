<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm" value="" disabled />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center e" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly/>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>รหัสผู้รับ</label>
			<input type="text" class="form-control input-sm text-center e" id="customer-code" value="" />
		</div>
		<div class="col-lg-4 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
			<label>ชื่อผู้รับ[สโมสร/ผู้รับการสนับสนุน]</label>
			<input type="text" class="form-control input-sm e" name="customer" id="customer-name" value="" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
			<label>งบคงเหลือ</label>
			<input type="text" class="form-control input-sm text-center e" id="budget-amount" data-amount="" value="" disabled />
		</div>

		<div class="col-lg-2 col-md-4 col-sm-3-harf col-xs-6 padding-5">
			<label>ผู้เบิก[พนักงาน/คนสั่ง]</label>
			<input type="text" class="form-control input-sm e" id="customer-ref" value="" />
		</div>

		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 padding-5">
			<label>คลัง</label>
			<select class="width-100 e" name="warehouse" id="warehouse">
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse(); ?>
			</select>
		</div>
		<div class="col-lg-7 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm e" name="remark" id="remark" value="">
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">Submit</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		</div>
	</div>
	<input type="hidden" id="budget-id" value="" />
	<input type="hidden" id="budget-code" value="" />
</form>
<hr class="margin-top-15 padding-5">

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
