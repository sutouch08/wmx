<?php $this->load->view('include/header_mobile'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center">New Document</div>
</div>
<div class="row padding-top-20">
	<div class="col-xs-6 fi">
		<label>เลขที่</label>
		<input type="text" class="form-control text-center r" id="code" value="" disabled />
	</div>
	<div class="col-xs-6 fi">
		<label>วันที่</label>
		<input type="text" class="form-control text-center r" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>

	<div class="col-xs-12 fi">
		<label>คลัง</label>
		<select class="form-control" id="warehouse">
			<option value="">เลือก</option>
			<?php echo select_common_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>

	<div class="col-xs-12 fi">
		<label>อ้างอิง</label>
		<input type="text" class="form-control text-center r" id="reference"  value="" />
	</div>

	<div class="col-xs-12 fi">
		<label>หมายเหตุ</label>
		<textarea class="form-control" id="remark"></textarea>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-xs-12">
		<button type="button" class="btn btn-primary btn-block" onclick="add()"><i class="fa fa-plus"></i>&nbsp; &nbsp; เพิ่ม</button>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/mobile/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
