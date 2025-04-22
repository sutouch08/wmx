<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-12">
			<input type="text" name="code" id="code" class="form-control input-sm input-medium e" maxlength="15" value="" onkeyup="validCode(this)" autofocus  />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<input type="text" name="name" id="name" class="width-100 e" maxlength="100" value="" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เลขประจำตัว/Tax ID</label>
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="Tax_id" id="tax-id" class="width-100" value="" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มลูกค้า</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="group" id="group" class="form-control e">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ประเภทลูกค้า</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="kind" id="kind" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_kind(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชนิดลูกค้า</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="type" id="type" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_type(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เกรดลูกค้า</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="class" id="class" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_class(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">พื้นที่ขาย</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="area" id="area" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_area(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">พนักงานขาย</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select name="sale" id="sale" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_saleman(); ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Active</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="padding-top:5px;">
				<input class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" checked />
				<span class="lbl"></span>
			</label>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<p class="pull-right">
				<button type="button" class="btn btn-sm btn-success btn-100" onclick="add()">Add</button>
			</p>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline">
			&nbsp;
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
