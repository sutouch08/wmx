<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="goGen()"><i class="fa fa-plus"></i> Gen Location</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">

	<div class="form-group margin-top-30">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Warehouse</label>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
      <select class="width-100 e" id="warehouse">
				<option value="">Please Select</option>
				<?php echo select_warehouse($zone->warehouse_id); ?>
			</select>
			<div class="red" id="warehouse-error"></div>
		</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Dimension</label>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4" style="padding-left:12px; padding-right:5px;">
      <div class="input-group width-100">
      	<span class="input-group-addon fix-width-50 text-center">Row</span>
				<input type="text" class="form-control input-sm text-center e"
					id="row" autocapitalize="characters"	placeholder="A - Z" maxlength="1" onchange="updateCode()" value="<?php echo $zone->row; ?>"/>
      </div>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      <div class="input-group width-100">
      	<span class="input-group-addon fix-width-50 text-center">Col</span>
				<input type="number" class="form-control input-sm text-center e" id="col" placeholder="01 - 99" maxlength="3" onchange="updateCode()" value="<?php echo $zone->col; ?>"/>
      </div>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5" style="padding-right:12px;">
      <div class="input-group width-100">
      	<span class="input-group-addon fix-width-50 text-center">Loc</span>
				<input type="text" class="form-control input-sm text-center e"
					id="loc" autocapitalize="characters" placeholder="A - Z" maxlength="1" onchange="updateCode()" value="<?php echo $zone->loc; ?>"/>
      </div>
		</div>
  </div>

	<div class="form-group hide">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Pre Code</label>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
      <input type="text" class="width-100 e" id="code" maxlength="50" onkeyup="validCode(this)"
			placeholder="Allow &nbsp; a-z,  A-Z,  0-9,  &quot;-&quot;,  &quot;_&quot;,  &quot;.&quot;,  &quot;@&quot;" value=""/>
			<div class="red" id="code-error"></div>
		</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Code</label>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
      <input type="text" class="width-100 e" id="full-code" value="<?php echo $zone->code; ?>"/>
			<div class="red" id="full-code-error"></div>
		</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Barcode</label>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
      <input type="text" class="width-100 e" id="barcode" maxlength="50" onkeyup="validCode(this)"
			placeholder="Allow &nbsp; a-z,  A-Z,  0-9,  &quot;-&quot;,  &quot;_&quot;,  &quot;.&quot;,  &quot;@&quot;" value="<?php echo $zone->barcode; ?>"/>
			<div class="red" id="barcode-error"></div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-5 col-xs-4">
			<label style="padding-top:7px;">
				<input type="checkbox" class="ace" id="chk-barcode" onchange="updateBarcode()" />
				<span class="lbl">&nbsp; ใช้รหัส</span>
			</label>
		</div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Name</label>
		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<input type="text" class="width-100 e" id="name" maxlength="100" value="<?php echo $zone->name; ?>"/>
			<div class="red" id="name-error"></div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-5 col-xs-4">
			<label style="padding-top:7px;">
				<input type="checkbox" class="ace" id="chk-name" onchange="updateName()" />
				<span class="lbl">&nbsp; ใช้รหัส</span>
			</label>
		</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="freeze" value="1" <?php echo is_checked($zone->freeze, '1'); ?>/>
				<span class="lbl">&nbsp;&nbsp; Freeze</span>
			</label>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="active" value="1" <?php echo is_checked($zone->active, '1'); ?>/>
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
  </div>

	<input type="hidden" id="id" value="<?php echo $zone->id; ?>" />
	<div class="divider-hidden"></div>

	<?php if($this->pm->can_add) : ?>
	  <div class="form-group">
	    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
				<button type="button" class="btn btn-white btn-success btn-100" onclick="update()">Save</button>
	    </div>
	  </div>
	<?php endif; ?>
</div>
<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
