<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">

	<div class="form-group margin-top-30">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสคลัง</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <input type="text" class="width-100 e" id="code" maxlength="8" onkeyup="validCode(this)"
			placeholder="Allow &nbsp; a-z,  A-Z,  0-9,  &quot;-&quot;,  &quot;_&quot;,  &quot;.&quot;,  &quot;@&quot;" autofocus />
		</div>
		<div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="code-error">&nbsp;</div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อคลัง</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			  <input type="text" class="width-100 e" id="name" maxlength="100" />
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="name-error">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="auz" value="1" />
				<span class="lbl">&nbsp;&nbsp; อนุญาติให้ติดลบ</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="freeze" value="1" />
				<span class="lbl">&nbsp;&nbsp; Freeze</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="active" value="1" checked/>
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red">&nbsp;</div>
  </div>

	<div class="divider-hidden"></div>

	<?php if($this->pm->can_add) : ?>
	  <div class="form-group">
	    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
				<button type="button" class="btn btn-white btn-success btn-100" onclick="add()">Add</button>
	    </div>
	  </div>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
