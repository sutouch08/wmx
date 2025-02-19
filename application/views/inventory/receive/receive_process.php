<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-xs btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status != 'D' && $this->pm->can_delete) : ?>
      <button type="button" class="btn btn-white btn-xs btn-danger top-btn" onclick="confirmCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> Cancel</button>
		<?php endif; ?>
	</div>
</div>
<hr/>
<?php if($doc->status == 'D') : ?>
	<?php $this->load->view('cancel_watermark'); ?>
<?php endif; ?>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Doc Date</label>
    <input type="text" class="width-100 text-center e" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Order Date</label>
    <input type="text" class="width-100 text-center e" id="order-date" value="<?php echo thai_date($doc->order_date); ?>" readonly disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Doc No.</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Order No.</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->order_no; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>REF_NO1</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->ref_no1; ?>" disabled/>
	</div>
  <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>REF_NO2</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->ref_no2; ?>" disabled/>
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Type</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->order_type; ?>" disabled/>
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center" value="<?php echo status_text($doc->status); ?>" disabled/>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_warehouse($doc->warehouse_id); ?>
		</select>
	</div>
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>Vendor Code</label>
		<input type="text" class="width-100" value="<?php echo $doc->vendor_code; ?>" disabled/>
	</div>
  <div class="col-lg-6 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>Vendor Name</label>
		<input type="text" class="width-100" value="<?php echo $doc->vendor_name; ?>" disabled/>
	</div>

	<div class="col-lg-12 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="warehouse-id" value="<?php echo $doc->warehouse_id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1000px;">
      <thead>
        <tr>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-100 text-center">จำนวน</th>
          <th class="fix-width-100 text-center">รับแล้ว</th>
          <th class="fix-width-100 text-center">คงเหลือ</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if( ! empty($uncomplete)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($uncomplete as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
        <td class="text-center"><?php echo number($rs->receive_qty); ?></td>
        <td class="text-center"><?php echo number($rs->qty - $rs->receive_qty); ?></td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancel_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive/receive.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive/receive_process.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
