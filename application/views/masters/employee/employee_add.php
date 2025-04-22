<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">
	<div class="form-group margin-top-30">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <input type="text" name="code" id="code" class="width-100 e" maxlength="20" value="" onkeyup="validCode(this)" autofocus  />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" class="width-100 e" maxlength="150" value=""  />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">สถานะ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm btn-success width-50" id="active-on" onclick="toggleActive(1)">Active</button>
				<button type="button" class="btn btn-sm width-50" id="active-off" onclick="toggleActive(0)">Inactive</button>
				<input type="hidden" id="active" name="active" value="1">
			</div>
    </div>
  </div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 text-right">
			<button type="button" class="btn btn-sm btn-success btn-100" onclick="add()"><i class="fa fa-save"></i>&nbsp;&nbsp; Add</button>
    </div>
  </div>

</div>

<script src="<?php echo base_url(); ?>scripts/masters/employee.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
