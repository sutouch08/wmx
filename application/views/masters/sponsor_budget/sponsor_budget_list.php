<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title hidden-xs"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>อ้างอิง</label>
    <input type="text" class="form-control input-sm search-box" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="from-date" value="<?php echo $from_date; ?>" placeholder="เริ่มต้น" />
			<input type="text" class="form-cotnrol input-sm width-50 text-center to-date" name="to_date" id="to-date" value="<?php echo $to_date; ?>" placeholder="สิ้นสุด"/>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ปีงบประมาณ</label>
    <select class="form-control input-sm filter" name="year">
			<option value="all">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm filter" name="active">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>
</div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped border-1" style="min-width:1250px; margin-bottom:0px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100"></th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-80">รหัส</th>
					<th class="fix-width-100">อ้างอิง</th>
					<th class="fix-width-80 text-center">เริ่มต้น</th>
					<th class="fix-width-80 text-center">สิ้นสุด</th>
					<th class="fix-width-80 text-center">ปีงบประมาณ</th>
					<th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-80 text-center">ผู้ใช้งาน</th>
					<th class="fix-width-100 text-right">งบประมาณ</th>
					<th class="fix-width-100 text-right">ใช้ไป</th>
					<th class="fix-width-100 text-right">คงเหลือ</th>
					<th class="fix-width-150 text-center">แก้ไขล่าสุด</th>
					<th class="min-width-100">แก้ไขโดย</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-11">
						<td>
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i>	</button>
							<?php endif; ?>
						</td>
						<td class="text-center"><?php echo $no; ?></td>
						<td class=""><?php echo $rs->code; ?></td>
						<td class=""><?php echo $rs->reference; ?></td>
						<td class="text-center"><?php echo thai_date($rs->from_date); ?></td>
						<td class="text-center"><?php echo thai_date($rs->to_date); ?></td>
						<td class="text-center"><?php echo $rs->budget_year; ?></td>
						<td class="text-center"><?php echo is_active($rs->active); ?></td>
						<td class="text-center"><?php echo $this->sponsor_budget_model->count_members($rs->id); ?></td>
						<td class="text-right"><?php echo number($rs->amount, 2); ?></td>
						<td class="text-right"><?php echo number($rs->used, 2); ?></td>
						<td class="text-right"><?php echo number($rs->balance, 2); ?></td>
						<td class="text-center"><?php echo empty($rs->date_upd) ? thai_date($rs->date_add, TRUE) : thai_date($rs->date_upd, TRUE); ?></td>
						<td><?php echo empty($rs->update_user) ? $rs->user : $rs->update_user; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/sponsor_budget.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
