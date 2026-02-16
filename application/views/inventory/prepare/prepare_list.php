<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right hidden-xs">
		<!-- <button type="button" class="btn btn-white btn-primary top-btn" onclick="genPickList()">พิมพ์ใบจัด(ชั่วคราว)</button> -->
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="goProcess()"><i class="fa fa-external-link-square"></i> รายการกำลังจัด</button>
	</div>
</div><!-- End Row -->
<hr class="hidden-xs"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row hidden-xs">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
			<label>MKP No.</label>
			<input type="text" class="width-100 search" name="reference"  value="<?php echo $reference; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
			<label>SO No.</label>
			<input type="text" class="width-100 search" name="so_no" value="<?php echo $so_no; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
			<label>Fulfillment No.</label>
			<input type="text" class="width-100 search" name="fulfillment_code"  value="<?php echo $fulfillment_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="width-100 search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-12 padding-5">
			<label>ช่องทางขาย</label>
			<select class="width-100 filter" name="channels" id="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>Shop Name</label>
			<select class="width-100 filter" name="shop_id" id="shop-id">
				<option value="all">ทั้งหมด</option>
				<?php echo select_shop_name($shop_id); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>ออนไลน์</label>
			<select class="width-100 filter" name="is_online">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected($is_online, '1'); ?>>ออนไลน์</option>
				<option value="0" <?php echo is_selected($is_online, '0'); ?>>ออฟไลน์</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>รูปแบบ</label>
			<select class="width-100 filter" name="role" id="role">
	      <option value="all">ทั้งหมด</option>
	      <?php echo select_order_role($role); ?>
	    </select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5 fi">
			<label>Backorder</label>
			<select class="width-100 filter" name="is_backorder">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_backorder);?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_backorder); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label>Canceled</label>
			<select class="width-100 filter" name="is_cancled">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_cancled); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_cancled); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" readonly value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" readonly value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5 fi">
			<label>Due Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_due_date" id="fromDueDate" readonly value="<?php echo $from_due_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_due_date" id="toDueDate" readonly value="<?php echo $to_due_date; ?>" />
			</div>
		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 padding-5">
	    <label>การจัดส่ง</label>
	    <select class="width-100 filter" name="id_sender" id="sender">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sender($id_sender); ?>
			</select>
	  </div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>สถานะ</label>
			<select class="width-100" name="stated">
				<option value="">เลือกสถานะ</option>
				<option value="3" <?php echo is_selected($stated, '3'); ?>>รอจัดสินค้า</option>
				<option value="4" <?php echo is_selected($stated, '4'); ?>>กำลังจัดสินค้า</option>
				<option value="5" <?php echo is_selected($stated, '5'); ?>>รอตรวจ</option>
				<option value="6" <?php echo is_selected($stated, '6'); ?>>กำลังตรวจ</option>
				<option value="7" <?php echo is_selected($stated, '7'); ?>>รอเปิดบิล</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>เริ่มต้น</label>
			<select class="width-100" name="startTime">
				<?php echo selectTime($startTime); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สิ้นสุด</label>
			<select class="width-100" name="endTime">
				<?php echo selectTime($endTime); ?>
			</select>
		</div>

		<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-12 padding-5">
			<label>รหัสสินค้า</label>
			<input type="text" class="form-control input-sm search" name="item_code" id="item_code" value="<?php echo $item_code; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">&nbsp;</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">&nbsp;</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 hidden-xs">
<div class="hidden-xs">
	<?php echo $this->pagination->create_links(); ?>
</div>

<div class="row hidden-xs">
	<div class="col-lg-3 col-md-4 col-sm-4 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon">จัดออเดอร์</span>
			<input type="text" class="form-control input-sm text-center" id="order-code" autofocus />
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5 ">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="goToProcess()">จัดสินค้า</button>
	</div>
</div>
<hr class="margin-top-15 hidden-xs">
<div class="row hidden-xs">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-hover border-1 no-border-xs table-listing" style="min-width:1680px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-60"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle text-center">วันที่</th>
					<th class="fix-width-80 middle text-center">Due date</th>
					<th class="fix-width-120 middle">Order No.</th>
					<th class="fix-width-100 middle">SO No.</th>
					<th class="fix-width-100 middle">Fulfil No.</th>
					<th class="fix-width-150 middle">MKP No.</th>
					<th class="fix-width-150 middle">ช่องทาง</th>
					<th class="fix-width-150 middle">การจัดส่ง</th>
					<th class="fix-width-80 middle text-center">จำนวน</th>
					<th class="fix-width-200 middle">ลูกค้า/ผู้เบิก</th>
					<th class="min-width-300 middle">คลังปลายทาง</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $whName = []; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code); ?>
						<?php if( empty($whName[$rs->warehouse_code])) : ?>
							<?php $whName[$rs->warehouse_code] = warehouse_name($rs->warehouse_code); ?>
						<?php endif; ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
						<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
            <tr id="row-<?php echo $rs->code; ?>" class="font-size-11">
							<td class="middle ">
          <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
                <button type="button" class="btn btn-white btn-mini btn-info" onClick="goPrepare('<?php echo $rs->code; ?>')">จัดสินค้า</button>
          <?php endif; ?>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, TRUE,'/'); ?></td>
							<td class="middle text-center"><?php echo empty($rs->due_date) ? "-" : thai_date($rs->due_date, FALSE,'/'); ?></td>
							<td class="middle"><?php echo $rs->code . $cn_text; ?></td>
							<td class="middle"><?php echo $rs->so_no; ?></td>
							<td class="middle"><?php echo $rs->fulfillment_code; ?></td>
							<td class="middle"><?php echo $rs->reference; ?></td>
							<td class="middle"><?php echo $rs->channels_name; ?></td>
							<td class="middle"><?php echo $rs->sender_name; ?></td>
							<td class="middle text-center"><?php echo number($rs->qty); ?></td>
							<td class="middle"><?php echo $customer_name; ?></td>
							<td class="middle"><?php echo ! empty($rs->to_warehouse) ? $rs->to_warehouse.' | '.warehouse_name($rs->to_warehouse) : ''; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="13" class="text-center">--- No Pending Orders ---</td>
          </tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row visible-xs">
	<div class="col-xs-12 text-center">
		Not Support Mobile
	</div>
</div>

<script>
	$('#shop-id').select2();
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
