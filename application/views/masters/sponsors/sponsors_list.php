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
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัสงบ</label>
    <input type="text" class="form-control input-sm search-box" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ปีงบประมาณ</label>
    <select class="form-control input-sm filter" name="year">
			<option value="all">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm filter" name="active">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>
</div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped border-1" style="min-width:1140px; margin-bottom:0px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100"></th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-100">รหัส</th>
					<th class="min-width-200">ลูกค้า</th>
					<th class="fix-width-80">รหัสงบ</th>
					<th class="fix-width-80 text-center">ปีงบ</th>
					<th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-100 text-right">งบประมาณ</th>
					<th class="fix-width-100 text-right">ใช้ไป</th>
					<th class="fix-width-100 text-right">คงเหลือ</th>
					<th class="fix-width-80">แก้ไขล่าสุด</th>
					<th class="fix-width-100">แก้ไขโดย</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<?php $budget = $this->sponsor_budget_model->get($rs->budget_id); ?>
					<tr class="font-size-11">
						<td>
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->customer_code.' : '.$rs->customer_name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="text-center"><?php echo $no; ?></td>
						<td class=""><?php echo $rs->customer_code; ?></td>
						<td class=""><?php echo $rs->customer_name; ?></td>
						<td class=""><?php echo $rs->budget_code; ?></td>
						<td class="text-center"><?php echo $rs->year; ?></td>
						<td class="text-center"><?php echo is_active($rs->active); ?></td>
						<td class="text-right"><?php echo ( ! empty($budget) ? number($budget->amount, 2) : '0.00'); ?></td>
						<td class="text-right"><?php echo ( ! empty($budget) ? number($budget->used, 2) : '0.00'); ?></td>
						<td class="text-right"><?php echo ( ! empty($budget) ? number($budget->balance, 2) : '0.00'); ?></td>
						<td><?php echo empty($rs->date_upd) ? thai_date($rs->date_add, FALSE) : thai_date($rs->date_upd, FALSE); ?></td>
						<td><?php echo empty($rs->update_user) ? $rs->user : $rs->update_user; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/sponsors.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
