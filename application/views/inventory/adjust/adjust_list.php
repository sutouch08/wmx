<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
		<?php endif; ?>
	</div>
</div>
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง</label>
    <input type="text" class="width-100 search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
    <label>User</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">All</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

  <div class="col-lg-3-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
    <label>คลัง</label>
    <select class="width-100 filter" name="warehouse" id="warehouse">
    	<option value="all">All</option>
			<?php echo select_warehouse($warehouse); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>การอนุมัติ</label>
		<select class="width-100 filter" name="approve">
			<option value="all">All</option>
			<option value="P" <?php echo is_selected($approve, "P"); ?>>Pending</option>
			<option value="A" <?php echo is_selected($approve, "A"); ?>>Approved</option>
			<option value="R" <?php echo is_selected($approve, "R"); ?>>Rejected</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
		<select class="width-100 filter" name="status">
			<option value="all" <?php echo is_selected($status, 'all'); ?>>All</option>
			<option value="-1" <?php echo is_selected($status, '-1'); ?>>Draft</option>
      <option value="0" <?php echo is_selected($status, '0'); ?>>Pending</option>
			<option value="1" <?php echo is_selected($status, '1'); ?>>Success</option>
      <option value="2" <?php echo is_selected($status, '2'); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered border-1" style="min-width:1100px;">
      <thead>
        <tr>
					<th class="fix-width-100"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
					<th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-80 text-center">การอนุมัติ</th>
          <th class="fix-width-150">คลัง</th>
					<th class="fix-width-150">อ้างอิง</th>
          <th class="fix-width-150">user</th>
          <th class="min-width-100">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($list))  : ?>
<?php $no = $this->uri->segment($this->segment) + 1; ?>
<?php   foreach($list as $rs)  : ?>
        <tr class="font-size-12">
					<td class="">
						<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
							<i class="fa fa-eye"></i>
						</button>

						<?php if($rs->status < 1 && $this->pm->can_edit) : ?>
							<button type="button" class="btn btn-minier btn-warning" onclick="edit('<?php echo $rs->code; ?>')">
								<i class="fa fa-pencil"></i>
							</button>
						<?php endif; ?>

						<?php if($rs->status != 2 && $this->pm->can_delete) : ?>
							<button type="button" class="btn btn-minier btn-danger" onclick="confirmCancel('<?php echo $rs->code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
          <td class="text-center"><?php echo $no; ?></td>
          <td class="text-center"><?php echo thai_date($rs->date_add); ?></td>
          <td class="text-center"><?php echo $rs->code; ?></td>
					<td class="text-center"><?php echo status_label($rs->status); ?></td>
					<td class="text-center">
						<?php if($rs->approve == 'A') : ?>
							<span class="green">Approved</span>
						<?php elseif($rs->approve == 'P') : ?>
							<span class="orange">Pending</span>
						<?php elseif($rs->approve == 'R') : ?>
							<span class="red">Rejected</span>
						<?php endif; ?>
					</td>
					<td class=""><?php echo warehouse_code($rs->warehouse_id); ?></td>
          <td class=""><?php echo $rs->reference; ?></td>
          <td class=""><?php echo $rs->user; ?></td>
          <td class=""><?php echo $rs->remark; ?></td>
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

<?php $this->load->view('cancel_modal'); ?>

<script type="text/javascript">
	$('#user').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
