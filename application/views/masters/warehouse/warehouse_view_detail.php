<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($this->pm->can_add) : ?>
			<!-- <button type="button" class="btn btn-white btn-primary top-btn" onclick="getUploadFile()"><i class="fa fa-file-excel-o"></i> &nbsp; Import Location</button> -->
		<?php endif; ?>
		<!-- <button type="button" class="btn btn-white btn-purple top-btn" onclick="getTemplate()"><i class="fa fa-download"></i> &nbsp; Template Location file</button> -->
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">
	<input type="hidden" id="id" value="<?php echo $wh->id; ?>" />
	<div class="form-group margin-top-30">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสคลัง</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <input type="text" class="width-100 e" id="code" value="<?php echo $wh->code; ?>" readonly/>
			<input type="hidden" id="id" value="<?php echo $wh->id; ?>" />
		</div>
		<div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="code-error">&nbsp;</div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อคลัง</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			  <input type="text" class="width-100 e" id="name" maxlength="100" value="<?php echo $wh->name; ?>" readonly/>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="name-error">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace acex" id="auz" value="1" disabled <?php echo is_checked($wh->auz, '1'); ?>/>
				<span class="lbl">&nbsp;&nbsp; อนุญาติให้ติดลบ</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace acex" id="freeze" value="1" disabled <?php echo is_checked($wh->freeze, '1'); ?>/>
				<span class="lbl">&nbsp;&nbsp; Freeze</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace acex" id="active" value="1" disabled <?php echo is_checked($wh->active, '1'); ?>/>
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>
</div>

<?php $this->load->view('masters/warehouse/import_file'); ?>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
