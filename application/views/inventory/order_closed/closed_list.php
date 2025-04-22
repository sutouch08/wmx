<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pakking-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
    <input type="text" class="form-control input-sm search-box" name="reference"  value="<?php echo $reference; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>User</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>รูปแบบ</label>
		<select class="width-100 filter" name="role" id="role">
      <option value="all">ทั้งหมด</option>
      <?php echo select_order_role($role); ?>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="width-100 filter" name="channels" id="channels">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-3 col-md-2 col-sm-5 col-xs-6 padding-5">
    <label>คลังสินค้า</label>
		<select class="width-100 filter" id="warehouse" name="warehouse">
      <option value="all">ทั้งหมด</option>
      <?php echo select_sell_warehouse($warehouse); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่จัดส่ง</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="ship_from_date" id="shipFromDate" value="<?php echo $ship_from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="ship_to_date" id="shipToDate" value="<?php echo $ship_to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>
</div>

<hr class="margin-top-15">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>
<?php $sort_code = $order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered border-1" style="min-width:1000px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-80 text-center">วันที่</th>
					<th class="fix-width-80 text-center">วันที่จัดส่ง</th>
          <th class="fix-width-100">เลขที่เอกสาร</th>
					<th class="fix-width-120">เลขที่อ้างอิง</th>
					<th class="fix-width-100">ช่องทางขาย</th>
          <th class="min-width-200">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="fix-width-100 text-right">ยอดเงิน</th>
          <th class="fix-width-150">User</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php $ch = get_channels_array(); ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-11">
          <td class="text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $no; ?>
          </td>
          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->date_add); ?>
          </td>
					<td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->shipped_date); ?>
          </td>
          <td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->code; ?>
          </td>
					<td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->reference; ?>
          </td>
					<td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo empty($ch[$rs->channels_code]) ? "" : $ch[$rs->channels_code]; ?>
          </td>
          <td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
						<?php if($rs->role == 'L') : ?>
							<?php echo $rs->empName; ?>
						<?php else : ?>
							<?php echo $rs->customer_code ." : ".$rs->customer_name; ?>
						<?php endif; ?>            
          </td>
          <td class="pointer text-right" onclick="viewDetail('<?php echo $rs->code; ?>')">
						<?php echo ($rs->doc_total <= 0 ? number($this->invoice_model->get_billed_amount($rs->code), 2) : number($rs->doc_total,2)); ?>
          </td>
          <td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->user; ?>
          </td>
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

<script>
	$('#user').select2();
	$('#role').select2();
	$('#channels').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
