<?php $this->load->view('include/header'); ?>
<style>
	.backorder {
		color:#811818 !important;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Order No.</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>SO No.</label>
			<input type="text" class="form-control input-sm search" name="so_no"  value="<?php echo $so_no; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>MKP No.</label>
			<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="width-100 filter" name="channels" id="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>การชำระเงิน</label>
			<select class="width-100 filter" name="payment" id="payment">
				<option value="all">ทั้งหมด</option>
				<?php echo select_payment_method($payment); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>Back order</label>
			<select class="form-control input-sm" name="is_backorder" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $is_backorder); ?>>No</option>
				<option value="1" <?php echo is_selected('1', $is_backorder); ?>>Yes</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Pre order</label>
			<select class="form-control input-sm" name="is_pre_order" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_pre_order); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_pre_order); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>Add By</label>
			<select class="form-control input-sm" name="method" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $method); ?>>Manual</option>
				<option value="1" <?php echo is_selected('1', $method); ?>>Upload</option>
				<option value="2" <?php echo is_selected('2', $method); ?>>API</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>User</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
			<label>คลัง</label>
			<select class="width-100" name="warehouse" id="warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">search</label>
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">reset</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<hr class="margin-top-15">

	<div class="row margin-top-10">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<button type="button" id="btn-state-1" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_1']; ?>" onclick="toggleState(1)">รอดำเนินการ</button>
			<button type="button" id="btn-state-2" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_2']; ?>" onclick="toggleState(2)">รอชำระเงิน</button>
			<button type="button" id="btn-state-3" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_3']; ?>" onclick="toggleState(3)">รอจัด</button>
			<button type="button" id="btn-state-4" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_4']; ?>" onclick="toggleState(4)">กำลังจัด</button>
			<button type="button" id="btn-state-5" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_5']; ?>" onclick="toggleState(5)">รอตรวจ</button>
			<button type="button" id="btn-state-6" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_6']; ?>" onclick="toggleState(6)">กำลังตรวจ</button>
			<button type="button" id="btn-state-7" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_7']; ?>" onclick="toggleState(7)">รอเปิดบิล</button>
			<button type="button" id="btn-state-8" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_8']; ?>" onclick="toggleState(8)">เปิดบิลแล้ว</button>
			<button type="button" id="btn-state-9" class="btn btn-sm margin-bottom-5 <?php echo $btn['state_9']; ?>" onclick="toggleState(9)">ยกเลิก</button>
			<!-- <button type="button" id="btn-not-save" class="btn btn-sm margin-bottom-5 <?php echo $btn['not_save']; ?>" onclick="toggleNotSave()">ไม่บันทึก</button>
			<button type="button" id="btn-expire" class="btn btn-sm margin-bottom-5 <?php echo $btn['is_expire']; ?>" onclick="toggleIsExpire()">หมดอายุ</button>
			<button type="button" id="btn-only-me" class="btn btn-sm margin-bottom-5 <?php echo $btn['only_me']; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button> -->
		</div>
	</div>

	<input type="hidden" name="state_1" id="state_1" value="<?php echo $state[1]; ?>" />
	<input type="hidden" name="state_2" id="state_2" value="<?php echo $state[2]; ?>" />
	<input type="hidden" name="state_3" id="state_3" value="<?php echo $state[3]; ?>" />
	<input type="hidden" name="state_4" id="state_4" value="<?php echo $state[4]; ?>" />
	<input type="hidden" name="state_5" id="state_5" value="<?php echo $state[5]; ?>" />
	<input type="hidden" name="state_6" id="state_6" value="<?php echo $state[6]; ?>" />
	<input type="hidden" name="state_7" id="state_7" value="<?php echo $state[7]; ?>" />
	<input type="hidden" name="state_8" id="state_8" value="<?php echo $state[8]; ?>" />
	<input type="hidden" name="state_9" id="state_9" value="<?php echo $state[9]; ?>" />
	<input type="hidden" name="notSave" id="notSave" value="<?php echo $notSave; ?>" />
	<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
	<input type="hidden" name="isExpire" id="isExpire" value="<?php echo $isExpire; ?>" />
	<hr class="margin-top-15 padding-5">
	<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
	<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
	<input type="hidden" name="search" value="1" />
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="order-table" style="overflow:auto;">
		<table class="table tableFixHead" style="min-width:1260px; margin-bottom:20px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 middle text-center fix-header">ลำดับ</th>
					<th class="fix-width-80 middle text-center fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">Order No.</th>
					<th class="fix-width-120 middle fix-header">SO No.</th>
					<th class="fix-width-120 middle fix-header">MKP No.</th>
					<th class="min-width-250 middle fix-header">ลูกค้า</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-150 middle fix-header">ช่องทางขาย</th>
					<th class="fix-width-150 middle fix-header">การชำระเงิน</th>
					<th class="fix-width-150 middle fix-header">สถานะ</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $cus_ref = empty($rs->customer_ref) ? '' : ' ['.$rs->customer_ref.']'; ?>
						<?php $cn_text = $rs->state != 9 && $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
            <tr class="font-size-11 <?php echo $rs->is_backorder && $rs->state < 5 ? 'backorder': ''; ?>" id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code . $cn_text; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->so_no; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->reference; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo empty($rs->customer_name) ? $this->customers_model->get_name($rs->customer_code) : $rs->customer_name; ?>
									<?php echo $cus_ref; ?>
								<?php endif; ?>
							</td>
              <td class="middle pointer text-right" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo $rs->doc_total <= 0 ? number($this->orders_model->get_order_total_amount($rs->code), 2) : number($rs->doc_total, 2); ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo empty($channelsList[$rs->channels_code]) ? "" : $channelsList[$rs->channels_code]; ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php echo empty($paymentList[$rs->payment_code]) ? "" : $paymentList[$rs->payment_code];  ?>
							</td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->is_expired) : ?>
									หมดอายุ
								<?php else : ?>
									<?php echo get_state_name($rs->state); ?>
								<?php endif; ?>
							</td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php if($this->_SuperAdmin) : ?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">	<?php	echo "Start @ {$start} <br/> End&nbsp; @ {$end}";	?></div>
	<?php endif; ?>
</div>

<script>
	$('#channels').select2();
	$('#payment').select2();
	$('#user').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
