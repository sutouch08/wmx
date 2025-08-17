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
				<input type="text" id="uuid" class="width-100" maxlength="150" value="<?php echo $ds->uuid; ?>" placeholder="No value" readonly  />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Code</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" name="code" id="code" class="width-100 e" maxlength="15" value="<?php echo $ds->code; ?>" placeholder="No value" readonly  />
				<input type="hidden" id="id" value="<?php echo $ds->id; ?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Name</label>
			<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
				<input type="text" name="name" id="name" class="width-100 e" maxlength="100" value="<?php echo $ds->name; ?>"  placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Tax ID</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" name="Tax_id" id="tax-id" class="width-100" value="<?php echo $ds->Tax_Id; ?>"  placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Group</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo parse_value_label($ds->group_code, $ds->group_name); ?>" placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Category</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo parse_value_label($ds->kind_code, $ds->kind_name); ?>" placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Grade</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo parse_value_label($ds->class_code, $ds->class_name); ?>" placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Type</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo parse_value_label($ds->type_code, $ds->type_name); ?>" placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Area</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo parse_value_label($ds->area_code, $ds->area_name); ?>" placeholder="No value" readonly />
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Status</label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<input type="text" class="width-100" value="<?php echo $ds->active == 1 ? 'Active' : 'Inactive'; ?>" readonly />
			</div>
		</div>

		<div class="divider-hidden"></div>
	<?php if($this->pm->can_edit && ! $view) : ?>
		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
<script src="<?php echo base_url(); ?>scripts/masters/customer_address.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
