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
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสคลัง</label>
    <div class="col-lg-lg-1-harf col-md-2 col-sm-3 col-xs-12">
      <input type="text" class="form-control input-sm e" id="code" maxlength="8" onkeyup="validCode(this)" value="" autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อคลัง</label>
    <div class="col-lg-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm e" id="name" maxlength="100" value="" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ประเภทคลัง</label>
 	 <div class="col-lg-lg-1-harf col-md-2 col-sm-3 col-xs-12">
 		 <select class="form-control input-sm" id="role" >
 		 	<option value="">กรุณาเลือก</option>
			<?php echo select_warehouse_role(); ?>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้ขาย</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
			 <input name="sell" class="ace ace-switch ace-switch-7" type="checkbox" id="sell" value="1" checked />
			 <span class="lbl"></span>
		 </label>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้จัด</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
 			<input name="prepare" class="ace ace-switch ace-switch-7" type="checkbox" id="prepare" value="1" checked />
 			<span class="lbl"></span>
 		</label>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้ยืม</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
 			<input name="lend" class="ace ace-switch ace-switch-7" type="checkbox" id="lend" value="1" checked />
 			<span class="lbl"></span>
 		</label>
 	 </div>
  </div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="padding-top:5px;">
				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" checked />
				<span class="lbl"></span>
			</label>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<?php if($this->pm->can_add) : ?>
		<div class="form-group">
	    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
	    <div class="col-lg-lg-2 col-md-2 col-sm-3 col-xs-12 text-right">
				<button type="button" class="btn btn-sm btn-success btn-100" onclick="add()">Add</button>
	    </div>
	  </div>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
