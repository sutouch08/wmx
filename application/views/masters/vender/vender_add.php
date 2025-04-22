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
      <input type="text" id="code" class="width-100 code e" maxlength="20"
			data-runno="<?php echo $run_no; ?>" value="<?php echo $code; ?>"
			placeholder="Required" onkeyup="validCode(this)" autofocus <?php echo $autorun ? 'disabled' : ''; ?> />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" id="name" class="width-100 e" maxlength="150" value="" placeholder="Required" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เครดิตเทอม(วัน)</label>
    <div class="col-xs-12 col-sm-1">
			<input type="number" id="credit_term" class="width-100" value="0" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="credit_term-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เลขที่ประจำตัวผู้เสียภาษี</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="tax_id" class="width-100" maxlength="20" value="" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="tax_id-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสสาขา</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="branch_code" class="width-100" maxlength="20" value="00000" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="branch_code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-xs-12 col-sm-2">
			<input type="text" id="branch_name" class="width-100" maxlength="50" value="สำนักงานใหญ่" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="branch_name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ที่อยู่</label>
    <div class="col-xs-12 col-sm-6">
			<input type="text" name="address" id="address" class="width-100" value="" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="address-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" id="phone" class="width-100" maxlength="50" value="" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="phone-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
    <div class="col-xs-12 col-sm-3">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm btn-success width-50" id="active-on" onclick="toggleActive(1)">ใช้งาน</button>
				<button type="button" class="btn btn-sm width-50" id="active-off" onclick="toggleActive(0)">ไม่ใช้งาน</button>
				<input type="hidden" id="active" name="active" value="1">
			</div>
    </div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="add()"><i class="fa fa-save"></i> บันทึก</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/vender.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
