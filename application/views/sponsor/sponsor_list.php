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
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>User</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

	<div class="col-lg-3-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>คลังสินค้า</label>
		<select class="width-100 filter" name="warehouse" id="warehouse">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>การอนุมัติ</label>
		<select class="width-100 filter" name="is_approved">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected($is_approved, "0"); ?>>รออนุมัติ</option>
			<option value="1" <?php echo is_selected($is_approved, "1"); ?>>อนุมัติแล้ว</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>status</label>
		<select class="width-100 filter" name="status">
			<option value="all">ทั้งหมด</option>
			<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Approval</option>
			<option value="A" <?php echo is_selected('A', $status); ?>>Approved</option>
			<option value="R" <?php echo is_selected('R', $status); ?>>Rejected</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">search</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:840px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">Date</th>
					<th class="fix-width-120 middle">Doc Num.</th>
					<th class="min-width-250 middle">Customer</th>
					<th class="fix-width-100 middle text-center">Budget</th>
					<th class="fix-width-120 middle text-right">Doc Total</th>
					<th class="fix-width-150 middle text-center">Status</th>
					<th class="fix-width-100 middle text-center">User</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr class="font-size-11 pointer" id="row-<?php echo $rs->id; ?>" onclick="edit('<?php echo $rs->code; ?>')">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->customer_name; ?></td>
							<td class="middle text-center"><?php echo $rs->budget_code; ?></td>
              <td class="middle text-right"><?php echo number($rs->doc_total, 2); ?></td>
              <td class="middle text-center"><?php echo sponsor_status_name($rs->status); ?></td>
							<td class="middle text-center"><?php echo $rs->user; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#user').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
