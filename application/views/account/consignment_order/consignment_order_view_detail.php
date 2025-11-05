<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status != 'D') : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					<i class="ace-icon fa fa-flash icon-on-left"></i>
					Actions
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if($doc->status == 'A' && $this->pm->can_approve) : ?>
						<li class="success">
							<a href="javascript:doApprove('<?php echo $doc->code; ?>')"><i class="fa fa-check"></i>&nbsp; Approve</a>
						</li>
					<?php endif; ?>
					<?php if(($doc->status == 'P' OR $doc->status == 'A') && $this->pm->can_edit) : ?>
						<li class="warning">
							<a href="javascript:goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i>&nbsp; Edit</a>
						</li>
					<?php endif; ?>
					<?php if($doc->status == 'C' && is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGN_INTERFACE'))) : ?>
						<li class="success">
							<a href="javascript:sendToErp('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i>&nbsp; Send To ERP</a>
						</li>
					<?php endif; ?>
					<?php if($doc->is_exported != 'Y' && $doc->status != 'D' && $this->pm->can_delete) : ?>
						<li class="danger">
							<a href="javascript:confirmCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i>&nbsp; Cancel</a>
						</li>
						<?php if($doc->status != 'P') : ?>
							<li class="purple">
								<a href="javascript:rollback('<?php echo $doc->code; ?>')"><i class="fa fa-history"></i>&nbsp; Rollback</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php if($doc->status == 'D') : ?>
<?php 	$this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Posting date</label>
    <input type="text" class="form-control input-sm text-center" id="posting-date" value="<?php echo thai_date($doc->shipped_date); ?>" readonly disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-8 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm" id="customer-name" value="<?php echo $doc->customer_name; ?>" readonly disabled />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>GP (%)</label>
		<input type="number" class="form-control input-sm text-center edit r" id="gp" value="<?php echo $doc->gp; ?>" disabled />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-9 padding-5">
    <label>คลัง</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->warehouse_code.' | '.warehouse_name($doc->warehouse_code); ?>" disabled />
  </div>
  <div class="col-lg-7 col-md-7 col-sm-6-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
  </div>
	<div class="divider"></div>
	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-4 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo status_text($doc->status); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ERP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
	</div>
</div>

<hr class="margin-top-15">

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:900px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-150">รหัสสินค้า</th>
          <th class="min-width-300">สินค้า</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="detail-table">
        <?php  $no = 1; ?>
        <?php  $totalQty = 0; ?>
        <?php  $totalAmount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-11">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle hide-text"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-right"><?php echo $rs->discount; ?></td>
          <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>
<?php endif; ?>
      <tr class="font-size-11" id="total-row">
        <td colspan="5" class="middle text-right"><strong>รวม</strong></td>
        <td id="total-qty" class="middle text-right"><?php echo number($totalQty, 2); ?></td>
        <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="log-text">
			<?php if( ! empty($logs)) : ?>
				<?php foreach($logs as $log) : ?>
					อนุมัติโดย : <?php echo $log->approver; ?> @ <?php echo thai_date($log->date_upd, TRUE); ?> <br/>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if($doc->status == 'D') : ?>
				ยกเลิกโดย : <?php echo $doc->cancel_user; ?> @ <?php echo thai_date($doc->date_upd, TRUE); ?><br/>
				หมายเหตุ : <?php echo $doc->cancel_reason; ?>
			<?php endif; ?>
		</p>
	</div>
</div>


<?php $this->load->view('cancel_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consignment_order/consignment_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
