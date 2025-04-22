<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="form-horizontal margin-top-30">
	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">รหัส</label>
    <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-8">
      <input type="text" class="form-control input-sm e" id="code" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">อ้างอิง</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8">
			<input type="text" id="reference" class="form-control input-sm" maxlength="20" placeholder="เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ"/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="reference-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">งบประมาณ</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="amount" class="form-control input-sm e" value="0" placeholder="มูลค่างบประมาณเป็นจำนวนเงิน"/>
    </div>
  </div>

	<div class="form-group hide">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ใช้ไป</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="used" class="form-control input-sm e" value="0" disabled/>
    </div>
  </div>

	<div class="form-group hide">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">คงเหลือ</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="balance" class="form-control input-sm e" value="0" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ระยะเวลา</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-8">
			<div class="input-daterange input-group">
				<input type="text" id="from-date" class="form-control input-sm text-center e" style="border-right:0px;" placeholder="เริ่มต้น"/>
				<span class="input-group-addon">ถึง</span>
				<input type="text" id="to-date" class="form-control input-sm text-center e" style="border-left:0px;" placeholder="สิ้นสุด"/>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ปีงบประมาณ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
			<select class="form-control input-sm" id="budget-year">
				<option value="">เลือก</option>
				<?php echo select_years(date('Y')); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="budget-year-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">สถานะ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
			<select class="form-control input-sm" id="active">
				<option value="1">Active</option>
				<option value="0">Inactive</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-8">
			<textarea class="form-control input-sm" id="remark"></textarea>
    </div>
  </div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<?php if($this->pm->can_add) : ?>
  <div class="form-group">
		<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
			<button type="button" class="btn btn-sm btn-success btn-100" onclick="add()">Add</button>
    </div>
  </div>
	<?php endif; ?>
</div>
<script src="<?php echo base_url(); ?>scripts/masters/sponsor_budget.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
