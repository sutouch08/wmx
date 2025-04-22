<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>รหัส</label>
		<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
	</div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เบอร์โทร</label>
    <input type="text" class="form-control input-sm search-box" name="phone" value="<?php echo $phone; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $status); ?>>Inactive</option>
    </select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:900px;">
			<thead>
				<tr>
					<th class="fix-width-100"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-150 middle">รหัส</th>
					<th class="fix-width-200 middle">ชื่อ</th>
					<th class="fix-width-150 middle">เบอร์โทร</th>
					<th class="fix-width-50 middle text-center">สถานะ</th>
          <th class="min-width-200 middle">ที่อยู่</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->pm->can_edit OR $this->pm->can_add): ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($this->pm->can_delete): ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
							<?php endif; ?>
						</td>
						<td class="text-center no"><?php echo $no; ?></td>
						<td class=""><?php echo $rs->code; ?></td>
						<td class=""><?php echo $rs->name; ?></td>
            <td class=""><?php echo $rs->phone; ?></td>
            <td class="text-center"><?php echo is_active($rs->status); ?></td>
						<td class=""><?php echo $rs->address; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7" class="text-center">--- ไม่พบรายการ ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/vender.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
