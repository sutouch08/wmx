<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>  Back</button>
  </div>
</div>
<hr>
<div class="row">
  <div class="form-horizontal">
    <div class="form-group margin-top-30">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
        <input type="text" class="width-100 e" id="code" maxlength="50" onkeyup="validCode(this)" autofocus/>
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <input type="text" class="width-100 e" id="name" maxlength="150" />
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">คลัง</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <select class="width-100 e" id="warehouse">
          <option value="">เลือกคลัง</option>
          <?php echo select_warehouse(); ?>
        </select>
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="warehouse-error"></div>
    </div>

    <div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Active</label>
  		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
  			<label style="padding-top:5px;">
  				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" checked />
  				<span class="lbl"></span>
  			</label>
  		</div>
  	</div>

    <div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Pickface</label>
  		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
  			<label style="padding-top:5px;">
  				<input name="is_pickface" class="ace ace-switch ace-switch-7" type="checkbox" id="is_pickface" value="1"  />
  				<span class="lbl"></span>
  			</label>
  		</div>
  	</div>

    <div class="divider-hidden"></div>
  	<div class="divider-hidden"></div>

  	<?php if($this->pm->can_add) : ?>
  		<div class="form-group">
  	    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
  	    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right">
  				<button type="button" class="btn btn-sm btn-success btn-100" onclick="add()">Add</button>
  	    </div>
  	  </div>
  	<?php endif; ?>

  </div><!-- form horizontal -->
</div>

<script>
  $('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
