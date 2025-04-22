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
      <input type="text" class="form-control input-sm e" id="code" value="<?php echo $budget->code; ?>" disabled />
			<input type="hidden" id="id" value="<?php echo $budget->id; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">อ้างอิง</label>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-8">
			<input type="text" id="reference" class="form-control input-sm" maxlength="20" value="<?php echo $budget->reference; ?>" disabled placeholder="เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ"/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">งบประมาณ</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="amount" class="form-control input-sm e" value="<?php echo number($budget->amount, 2); ?>" disabled placeholder="มูลค่างบประมาณเป็นจำนวนเงิน"/>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="amount-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ใช้ไป</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="used" class="form-control input-sm e" value="<?php echo number($budget->used, 2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">คงเหลือ</label>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8">
			<input type="text" id="balance" class="form-control input-sm e" value="<?php echo number($budget->balance, 2); ?>" disabled/>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ระยะเวลา</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-8">
			<div class="input-daterange input-group">
				<input type="text" id="from-date" class="form-control input-sm text-center e" disabled value="<?php echo thai_date($budget->from_date); ?>" style="border-right:0px;" placeholder="เริ่มต้น"/>
				<span class="input-group-addon">ถึง</span>
				<input type="text" id="to-date" class="form-control input-sm text-center e" disabled value="<?php echo thai_date($budget->to_date); ?>" style="border-left:0px;" placeholder="สิ้นสุด"/>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ปีงบประมาณ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
			<select class="form-control input-sm" id="budget-year" disabled>
				<option value="">เลือก</option>
				<?php echo select_years($budget->budget_year); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="budget-year-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">สถานะ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
			<select class="form-control input-sm" id="active" disabled>
				<option value="1" <?php echo is_selected('1', $budget->active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $budget->active); ?>>Inactive</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">หมายเหตุ</label>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-8">
			<textarea class="form-control input-sm" id="remark" disabled><?php echo $budget->remark; ?></textarea>
    </div>
  </div>

	<div class="divider-hidden"></div>

	<div class="form-group">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label no-padding-right">ผู้ใช้งาน</label>
    <div class="col-lg-5 col-md-7 col-sm-8 col-xs-8 table-responsive">
		<?php if( ! empty($members)) : ?>
			<table class="table table-striped border-1" style="min-width:450px;">
				<tr>
					<td class="fix-width-40 text-center">#</td>
					<td class="fix-width-150">รหัสลูกค้า</td>
					<td class="min-width-200">ชื่อลูกค้า</td>
					<td class="fix-width-60">สถานะ</td>
				</tr>
				<?php $no = 1; ?>
				<?php foreach($members as $rs) : ?>
					<tr class="font-size-12">
						<td class="text-center"><?php echo $no; ?></td>
						<td><?php echo $rs->customer_code; ?></td>
						<td><?php echo $rs->customer_name; ?></td>
						<td class="text-center"><?php echo is_active($rs->active); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			</table>
		<?php else : ?>
			--- ไม่พบผู้ถืองบประมาณ --
		<?php endif; ?>
    </div>
  </div>

	<div class="divider"></div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p>สร้างโดย : <?php echo $budget->user; ?> @ <?php echo thai_date($budget->date_add, TRUE); ?></p>
			<?php if( ! empty($budget->date_upd)) : ?>
				<p>แก้ไขล่าสุดโดย : <?php echo $budget->update_user; ?> @ <?php echo thai_date($budget->date_upd, TRUE); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>scripts/masters/sponsor_budget.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
