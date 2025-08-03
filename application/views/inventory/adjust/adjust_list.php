<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Doc No.</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>ERP No.</label>
			<input type="text" class="width-100 search" name="DocNum"  value="<?php echo $DocNum; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Ref No.</label>
			<input type="text" class="width-100 search" name="reference" value="<?php echo $reference; ?>" />
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
			<label>Warehouse</label>
			<select class="width-100 filter" name="warehouse_code" id="warehouse">
				<option value="all">All</option>
				<?php echo select_warehouse($warehouse_code); ?>
			</select>
		</div>

		<div class="col-lg-2-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
	    <label>Owner</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
	  </div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="width-100 filter" name="status" id="status">
				<option value="all" <?php echo is_selected($status, 'all'); ?>>ทั้งหมด</option>
				<option value="P" <?php echo is_selected($status, 'P'); ?>>Draft</option>
				<option value="A" <?php echo is_selected($status, 'A'); ?>>Approval</option>
				<option value="R" <?php echo is_selected($status, 'R'); ?>>Rejected</option>
				<option value="C" <?php echo is_selected($status, 'C'); ?>>Closed</option>
				<option value="D" <?php echo is_selected($status, 'D'); ?>>Canceled</option>
			</select>
		</div>
		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered border-1" style="min-width:1020px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-100"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-100 text-center">Date</th>
          <th class="fix-width-120 text-center">Doc No.</th>
					<th class="fix-width-100 text-center">ERP No.</th>
					<th class="fix-width-50 text-center">Status</th>
          <th class="fix-width-150">Reference</th>
          <th class="fix-width-150">Owner</th>
					<th class="min-width-200">Remark</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($list))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($list as $rs)  : ?>
        <tr class="font-size-11">
					<td class="middle">
						<button type="button" class="btn btn-minier btn-info" onclick="goDetail('<?php echo $rs->code; ?>')">
							<i class="fa fa-eye"></i>
						</button>

						<?php if($this->pm->can_edit && ($rs->status == 'P' OR $rs->status == 'A' OR $rs->status == 'R')) : ?>
							<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
								<i class="fa fa-pencil"></i>
							</button>
						<?php endif; ?>

						<?php if($rs->status != 'D' && $this->pm->can_delete) : ?>
							<?php if($rs->status != 'C' && $rs->DocNum == NULL OR $this->_SuperAdmin) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="confirmCancel('<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						<?php endif; ?>
					</td>
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
          <td class="middle text-center"><?php echo $rs->code; ?></td>
					<td class="middle text-center"><?php echo $rs->DocNum; ?></td>
					<td class="middle text-center">
						<?php if($rs->status == 'D') : ?>
							<span class="red">Canceled</span>
						<?php elseif($rs->status == 'C') : ?>
							<span class="green">Closed</span>
						<?php elseif($rs->status == 'A') : ?>
							<span class="blue">Approval</span>
						<?php else : ?>
							<span class="orange">Draft</span>
						<?php endif?>
					</td>
          <td class="middle"><?php echo $rs->reference; ?></td>
          <td class="middle"><?php echo $rs->user; ?></td>
          <td class="middle"><?php echo $rs->remark; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<script>
	$('#warehouse').select2();
	$('#user').select2();
	$('#status').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
