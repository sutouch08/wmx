<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
	<?php if($this->pm->can_add) : ?>
		<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-add"></i> Add New</button>
	<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>รหัส/ชื่อ</label>
			<input type="text" class="width-100 filter" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="width-100 filter" name="active">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Freeze</label>
			<select class="width-100 filter" name="freeze">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $freeze); ?>>YES</option>
				<option value="0" <?php echo is_selected('0', $freeze); ?>>NO</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ติดลบ</label>
			<select class="width-100 filter" name="auz">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $auz); ?>>YES</option>
				<option value="0" <?php echo is_selected('0', $auz); ?>>NO</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="padding-5 margin-top-15">

<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-hover border-1" style="min-width:1000px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">Code</th>
					<th class="min-width-250 middle">Name</th>
					<th class="fix-width-80 middle text-center">Location</th>
					<th class="fix-width-60 middle text-center">Active</th>
					<th class="fix-width-60 middle text-center">Freeze</th>
					<th class="fix-width-60 middle text-center">AUZ</th>
					<th class="fix-width-150 middle text-center">Last Update</th>
					<th class="fix-width-100 middle text-center">Update by</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
						<td>
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>,'<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="text-center no"><?php echo $no; ?></td>
						<td class=""><?php echo $rs->code; ?></td>
						<td class=""><?php echo $rs->name; ?></td>
						<td class="text-center"><?php echo number($rs->zone_count); ?></td>
						<td class="text-center"><?php echo is_active($rs->active); ?></td>
						<td class="text-center"><?php echo is_active($rs->freeze, FALSE); ?></td>
						<td class="text-center"><?php echo is_active($rs->auz, FALSE); ?></td>
						<td class="text-center"><?php echo empty($rs->date_update) ? thai_date($rs->date_add, TRUE) : thai_date($rs->date_update, TRUE); ?></td>
						<td class="text-center"><?php echo empty($rs->update_by) ? uname($rs->create_by) : uname($rs->update_by); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
