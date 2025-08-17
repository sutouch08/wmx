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

<div class="row">
	<div class="form-horizontal">
		<div class="form-group margin-top-30">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">UUID</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" id="uuid" class="width-100" maxlength="150" value="<?php echo $ds->uuid; ?>" readonly  />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Code</label>
			<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-12">
				<input type="text" name="code" id="code" class="form-control input-sm input-medium e" maxlength="15" value="<?php echo $ds->code; ?>" readonly  />
				<input type="hidden" id="id" value="<?php echo $ds->id; ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Name</label>
			<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
				<input type="text" name="name" id="name" class="width-100 e" maxlength="100" value="<?php echo $ds->name; ?>"   />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Tax ID</label>
			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
				<input type="text" name="Tax_id" id="tax-id" class="width-100" value="<?php echo $ds->Tax_Id; ?>"  />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Group</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<select name="group" id="group" class="width-100"  >
					<option value="">เลือกรายการ</option>
					<?php echo select_customer_group($ds->group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Category</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<select name="kind" id="kind" class="width-100" >
					<option value="">เลือกรายการ</option>
					<?php echo select_customer_kind($ds->kind_code); ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Grade</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<select name="class" id="class" class="width-100" >
					<option value="">เลือกรายการ</option>
					<?php echo select_customer_class($ds->class_code); ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Type</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<select name="type" id="type" class="width-100" >
					<option value="">เลือกรายการ</option>
					<?php echo select_customer_type($ds->type_code); ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Area</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<select name="area" id="area" class="width-100" >
					<option value="">เลือกรายการ</option>
					<?php echo select_customer_area($ds->area_code); ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Active</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<label style="padding-top:5px;">
					<input class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($ds->active, '1'); ?> />
					<span class="lbl"></span>
				</label>
			</div>
		</div>

		<div class="divider-hidden"></div>

	<?php if($this->_SuperAdmin) : ?>
		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<p class="pull-right">
					<button type="button" class="btn btn-sm btn-success btn-100" onclick="update()">Update</button>
				</p>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline">
				&nbsp;
			</div>
		</div>
	<?php endif; ?>
	</div>

</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/customers.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
