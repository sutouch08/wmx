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
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Doc No.</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Order No.</label>
			<input type="text" class="width-100 search" name="order_no" value="<?php echo $order_no; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>Vendor</label>
			<input type="text" class="width-100 search" name="vendor" value="<?php echo $vendor; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>REF_NO1</label>
			<input type="text" class="width-100 search" name="ref_no1" value="<?php echo $ref_no1; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>REF_NO2</label>
			<input type="text" class="width-100 search" name="ref_no2" value="<?php echo $ref_no2; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>Type</label>
			<select class="width-100 filter" name="order_type">
				<option value="all">All</option>
				<option value="WR" <?php echo is_selected($order_type, "WR"); ?>>WR</option>
				<option value="RT" <?php echo is_selected($order_type, "RT"); ?>>RT</option>
				<option value="RN" <?php echo is_selected($order_type, "RN"); ?>>RN</option>
				<option value="CN" <?php echo is_selected($order_type, "CN"); ?>>CN</option>
				<option value="SM" <?php echo is_selected($order_type, "SM"); ?>>SM</option>
				<option value="WW" <?php echo is_selected($order_type, "WW"); ?>>WW</option>
			</select>
		</div>

		<div class="col-lg-3-harf col-md-3-harf col-sm-4 col-xs-6 padding-5">
			<label>Warehouse</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">All</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Status</label>
			<select class="width-100 filter" name="status">
				<option value="all" <?php echo is_selected($status, 'all'); ?>>All</option>
				<option value="P" <?php echo is_selected($status, 'P'); ?>>Pending</option>
				<option value="R" <?php echo is_selected($status, 'R'); ?>>In progress</option>
				<option value="C" <?php echo is_selected($status, 'C'); ?>>Completed</option>
				<option value="D" <?php echo is_selected($status, 'D'); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Doc Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>Order Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="order_from_date" id="orderFromDate" value="<?php echo $order_from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="order_to_date" id="orderToDate" value="<?php echo $order_to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered border-1" style="min-width:1210px;">
      <thead>
        <tr>
					<th class="fix-width-100"></th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-100 text-center">Doc Date</th>
          <th class="fix-width-120 text-center">Doc No.</th>
					<th class="fix-width-100 text-center">Order Date</th>
          <th class="fix-width-120 text-center">Order No.</th>
					<th class="fix-width-60 text-center">Status</th>
					<th class="fix-width-60 text-center">Type</th>
          <th class="fix-width-150 text-center">Warehouse</th>
					<th class="fix-width-150 text-center">REF_NO1</th>
					<th class="fix-width-150 text-center">REF_NO2</th>
          <th class="fix-width-150 text-center">Vendor</th>
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

						<?php if($rs->status != 'D' && $this->pm->can_delete) : ?>
							<button type="button" class="btn btn-minier btn-danger" onclick="confirmCancel('<?php echo $rs->code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
          <td class="text-center"><?php echo $no; ?></td>
          <td class="text-center"><?php echo thai_date($rs->date_add); ?></td>
          <td class="text-center"><?php echo $rs->code; ?></td>
					<td class="text-center"><?php echo thai_date($rs->order_date); ?></td>
          <td class="text-center"><?php echo $rs->order_no; ?></td>
					<td class="text-center"><?php echo status_label($rs->status); ?></td>
					<td class="text-center"><?php echo $rs->order_type; ?></td>
					<td class="text-center"><?php echo $rs->warehouse_code; ?></td>
          <td class="text-center"><?php echo $rs->ref_no1; ?></td>
          <td class="text-center"><?php echo $rs->ref_no2; ?></td>
          <td class=""><?php echo $rs->vendor_name; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="12" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancel_modal'); ?>

<script type="text/javascript">
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/inbound/inbound.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/inbound/inbound_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
