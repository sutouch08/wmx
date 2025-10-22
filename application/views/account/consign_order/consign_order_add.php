<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center r" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Posting date</label>
    <input type="text" class="form-control input-sm text-center r" id="posting-date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center r" id="customer-code" autofocus/>
	</div>
  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-8 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm r" id="customer-name" readonly />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>GP (%)</label>
		<input type="number" class="form-control input-sm text-center r" id="gp" />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-9 padding-5">
    <label>คลังฝากขายแท้</label>
		<select class="width-100 r" id="warehouse" onchange="updateCustomer()">
			<option value="">เลือกคลัง</option>
			<?php echo select_consign_warehouse(); ?>
		</select>
  </div>
  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">Submit</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
</form>
<hr class="margin-top-15">

<script id="warehouse-template" type="text/x-handlebarsTemplate">
	<option value="">เลือก</option>
	{{#each this}}
		<option value="{{code}}" {{selected}}>{{code}} | {{name}}</option>
	{{/each}}
</script>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('YmdH'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
