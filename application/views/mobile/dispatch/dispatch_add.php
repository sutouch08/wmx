<?php $this->load->view('include/header_mobile'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center">New Dispatch</div>
</div>
<div class="row padding-top-20">
	<div class="col-xs-6 fi">
		<label>วันที่</label>
		<input type="text" class="form-control text-center r" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>

	<div class="col-xs-6 fi">
		<label>ทะเบียนรถ</label>
		<input type="text" class="form-control text-center r" id="plate-no"  value="" />
	</div>

	<div class="col-xs-6 fi">
		<label>จังหวัด</label>
    <input type="text" class="form-control text-center r" id="province" value="" />
	</div>

	<div class="col-xs-6 fi">
		<label>คนขับ</label>
		<input type="text" class="form-control r" id="driver-name"  value="" />
	</div>

	<div class="col-xs-12 fi">
		<label>ช่องทางขาย</label>
		<select class="form-control" id="channels">
			<option value="">เลือก</option>
			<?php echo select_dispatch_channels(); ?>
		</select>
	</div>

	<div class="col-xs-12 fi">
		<label>ผู้จัดส่ง</label>
		<select class="form-control r" id="sender">
			<option value="">เลือก</option>
			<?php echo select_sender(); ?>
		</select>
	</div>

	<div class="col-xs-12 fi">
		<label>หมายเหตุ</label>
		<textarea class="form-control" id="remark"></textarea>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-xs-12">
		<button type="button" class="btn btn-sm btn-primary btn-block" onclick="add()"><i class="fa fa-plus"></i>&nbsp; &nbsp; เพิ่ม</button>
	</div>
</div>

<script>
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
