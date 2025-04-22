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
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">รหัสลูกค้า</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8">
      <input type="text" class="form-control input-sm e" id="customer-code" value="<?php echo $ds->customer_code; ?>" disabled />
			<input type="hidden" id="id" value="<?php echo $ds->id; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ชื่อลูกค้า</label>
    <div class="col-lg-5 col-md-6 col-sm-7 col-xs-8">
			<input type="text" id="customer-name" class="form-control input-sm" value="<?php echo $ds->customer_name; ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">สถานะ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4">
			<select class="form-control input-sm" id="active" disabled>
				<option value="1" <?php echo is_selected('1', $ds->active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $ds->active); ?>>Inactive</option>
			</select>
    </div>
  </div>

	<div class="divider-hidden"></div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">งบที่ใช้</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-code" value="<?php echo $budget->code; ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">อ้างอิง</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-reference" value="<?php echo $budget->reference; ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">งบประมาณ</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-amount" value="<?php echo number($budget->amount, 2); ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ใช้ไป</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-used" value="<?php echo number($budget->used, 2); ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">คงเหลือ</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-balance" value="<?php echo number($budget->balance, 2); ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ระยะเวลา</label>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-8 padding-right-5">
			<div class="input-daterange input-group">
				<input type="text" id="from-date" class="form-control input-sm text-center" value="<?php echo thai_date($budget->from_date, FALSE); ?>" disabled style="border-right:0px;" placeholder="เริ่มต้น"/>
				<span class="input-group-addon">ถึง</span>
				<input type="text" id="to-date" class="form-control input-sm text-center" value="<?php echo thai_date($budget->to_date, FALSE); ?>" disabled style="border-left:0px;" placeholder="สิ้นสุด"/>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ปีงบประมาณ</label>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-right-5">
			<input type="text" class="form-control input-sm" id="budget-year" value="<?php echo $budget->budget_year; ?>" disabled />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">สถานะ</label>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-right-5">
			<select class="form-control input-sm" id="budget-active" disabled>
				<option value="">&nbsp;</option>
				<option value="1" <?php echo is_selected('1', $budget->active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $budget->active); ?>>Inactive</option>
			</select>
    </div>
  </div>

	<div class="divider"></div>
	<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p>สร้างโดย : <?php echo $ds->user; ?> @ <?php echo thai_date($ds->date_add, TRUE); ?></p>
			<?php if( ! empty($ds->date_upd)) : ?>
				<p>แก้ไขล่าสุดโดย : <?php echo $ds->update_user; ?> @ <?php echo thai_date($ds->date_upd, TRUE); ?></p>
			<?php endif; ?>
    </div>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/masters/sponsors.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
