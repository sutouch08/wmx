<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="form-horizontal">
	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
      <input type="text" id="code" class="width-100 code e" maxlength="20" value="<?php echo $code; ?>" disabled/>
			<input type="hidden" id="id" value="<?php echo $id; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" id="name" class="width-100 e" maxlength="150" value="<?php echo $name; ?>" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เครดิตเทอม(วัน)</label>
    <div class="col-xs-12 col-sm-1">
			<input type="number" id="credit_term" class="width-100" value="<?php echo get_zero($credit_term); ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="credit_term-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เลขที่ประจำตัวผู้เสียภาษี</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="tax_id" class="width-100" maxlength="20" value="<?php echo $tax_id; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="tax_id-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสสาขา</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="branch_code" class="width-100" maxlength="20" value="<?php echo $branch_code; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="branch_code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="branch_name" class="width-100" maxlength="50" value="<?php echo $branch_name; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="branch_name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ที่อยู่</label>
    <div class="col-xs-12 col-sm-6">
			<input type="text" name="address" id="address" class="width-100" value="<?php echo $address; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="address-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" id="phone" class="width-100" maxlength="50" value="<?php echo $phone; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="phone-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
    <div class="col-xs-12 col-sm-1">
			<input type="text" class="width-100 text-center" value="<?php echo $status == 1 ? 'Active' : 'Inactive'; ?>" disabled />
    </div>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/vender.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
