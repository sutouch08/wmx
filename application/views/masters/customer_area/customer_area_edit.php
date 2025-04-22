<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
			<input type="text" name="code" id="code" class="width-100 e" maxlength="15" value="<?php echo $ds->code; ?>" onkeyup="validCode(this)" disabled />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="name" id="name" class="width-100 e" maxlength="100" value="<?php echo $ds->name; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="divider-hidden"></div>

	<?php if($this->pm->can_edit) : ?>
		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">&nbsp;</label>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-right">
				<button type="button" class="btn btn-sm btn-success btn-100" onclick="update()">Update</button>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline">&nbsp;</div>
		</div>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customer_area.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
