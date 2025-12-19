<?php $this->load->view('include/header_mobile'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center">New Document</div>
</div>
<div class="row padding-top-20">
	<div class="col-xs-6 fi">
		<label>วันที่</label>
		<input type="text" class="form-control text-center r" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-xs-6 fi">
		<label>Posting date</label>
		<input type="text" class="form-control text-center r" id="posting-date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-xs-6 fi">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control text-center r" id="customer-code" value="" />
	</div>
	<div class="col-xs-6 fi">
		<label>GP (%)</label>
		<input type="number" class="form-control text-center r" id="gp" value="" />
	</div>
	<div class="col-xs-12 fi">
		<label>ลูกค้า</label>
		<input type="text" class="form-control r" id="customer-name"  value="" readonly />
	</div>

	<div class="col-xs-12 fi">
		<label>คลัง</label>
		<select class="form-control" id="warehouse"  onchange="updateCustomer()">
			<option value="">เลือก</option>
			<?php echo select_consign_warehouse(); ?>
		</select>
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

<script id="warehouse-template" type="text/x-handlebarsTemplate">
	<option value="">เลือก</option>
	{{#each this}}
		<option value="{{code}}" {{selected}}>{{code}} | {{name}}</option>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/mobile/consign_order/consign_order.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/consign_order/consign_order_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
