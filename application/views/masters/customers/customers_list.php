<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
	<?php if($this->pm->can_add) : ?>
		<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
	<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>รหัส/ชื่อ</label>
			<input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>กลุ่ม</label>
			<select class="form-control input-sm filter" name="group" id="customer_group">
				<option value="all">ทั้งหมด</option>
				<?php echo select_customer_group($group); ?>
				<option value="NULL" <?php echo is_selected('NULL', $group); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ประเภท</label>
			<select class="form-control input-sm filter" name="kind" id="customer_kind">
				<option value="all">ทั้งหมด</option>
				<?php echo select_customer_kind($kind); ?>
				<option value="NULL" <?php echo is_selected('NULL', $kind); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ชนิด</label>
			<select class="form-control input-sm filter" name="type" id="customer_type">
				<option value="all">ทั้งหมด</option>
				<?php echo select_customer_type($type); ?>
				<option value="NULL" <?php echo is_selected('NULL', $type); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>เขต</label>
			<select class="form-control input-sm filter" name="area" id="customer_area">
				<option value="all">ทั้งหมด</option>
				<?php echo select_customer_area($area); ?>
				<option value="NULL" <?php echo is_selected('NULL', $area); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>เกรด</label>
			<select class="form-control input-sm filter" name="class" id="customer_class">
				<option value="all">ทั้งหมด</option>
				<?php echo select_customer_class($class); ?>
				<option value="NULL" <?php echo is_selected('NULL', $class); ?>>ไม่มี</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status" id="status">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
		<input type="hidden" name="search" value="1" />
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:900px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 text-center"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-60 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="min-width-200 middle">ชื่อ</th>
					<th class="fix-width-100 middle">กลุ่ม</th>
					<th class="fix-width-100 middle">ประเภท</th>
					<th class="fix-width-100 middle">ชนิด</th>
					<th class="fix-width-60 middle">เกรด</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-11">
						<td class="middle">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->class; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>

<?php $this->load->view('include/footer'); ?>
