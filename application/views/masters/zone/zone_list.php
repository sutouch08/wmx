<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
			<button type="button" class="btn btn-sm btn-purple top-btn" onclick="goGen()"><i class="fa fa-plus"></i> Generate Location</button>
		<?php endif; ?>
		<!-- <button type="button" class="btn btn-white btn-purple  top-btn" onclick="exportFilter()">
			<i class="fa fa-file-excel-o"></i> Export
		</button> -->
		<button type="button" class="btn btn-sm btn-info top-btn" onclick="generateQrcode()"><i class="fa fa-qrcode"></i> Generate QR</button>
  </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>Code/Name</label>
    <input type="text" class="width-100 filter" name="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>Barcode</label>
    <input type="text" class="width-100 filter" name="barcode" value="<?php echo $barcode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>Row</label>
    <input type="text" class="width-100 filter" name="row" value="<?php echo $row; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>Column</label>
    <input type="text" class="width-100 filter" name="col" value="<?php echo $col; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>Loc</label>
    <input type="text" class="width-100 filter" name="loc" value="<?php echo $loc; ?>" />
  </div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-5 col-xs-12 padding-5">
    <label>Warehouse</label>
    <select class="width-100 filter" name="warehouse" id="warehouse">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
    <label>Status</label>
    <select class="width-100 filter" name="active">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
    <label>Freeze</label>
    <select class="width-100 filter" name="freeze">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $freeze); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $freeze); ?>>No</option>
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
</div>
</form>
<hr class="margin-top-15">
<form class="hidden" id="exportForm" method="post" action="<?php echo $this->home; ?>/export_filter">
	<input type="hidden" name="zone_code" id="export-code" value="<?php echo $code; ?>">
	<input type="hidden" name="zone_barcode" id="export-barcode" value="<?php echo $barcode; ?>">
	<input type="hidden" name="zone_warehouse" id="export-warehouse" value="<?php echo $warehouse; ?>">
	<input type="hidden" name="zone_status" id="export-status" value="<?php echo $active; ?>" />
	<input type="hidden" name="token" id="token" value="<?php echo genUid(); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1180px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center">
						<label>
							<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll()"/>
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-80 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">Barcode</th>
					<th class="fix-width-150 middle">Code</th>
					<th class="min-width-100 middle">Name</th>
					<th class="fix-width-150 middle">Warehosue</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-80 middle text-center">Freeze</th>
					<th class="fix-width-150 middle">Last Update</th>
					<th class="fix-width-150 middle">Update By</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr class="fon-size-11" id="row-<?php echo $rs->id; ?>">
						<td class="middle text-center">
							<label>
								<input type="checkbox" class="ace chk"
								value="<?php echo $rs->code; ?>"
								data-code="<?php echo $rs->code; ?>"
								data-name="<?php echo $rs->name; ?>" />
								<span class="lbl"></span>
							</label>
						</td>
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="edit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->barcode; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->warehouse_code; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->freeze, FALSE); ?></td>
						<td class="middle"><?php echo (empty($rs->date_update) ? thai_date($rs->date_add, TRUE) : thai_date($rs->date_update, TRUE)); ?></td>
						<td class="middle"><?php echo (empty($rs->update_by) ? uname($rs->create_by) : uname($rs->update_by)); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="11" class="text-center">--- No zone ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
