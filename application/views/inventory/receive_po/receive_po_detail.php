<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($this->pm->can_edit && $doc->status == 'P') : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
		<?php endif; ?>
		<?php if(($this->pm->can_delete && $doc->status != 'D' && $doc->status != 'C' && empty($doc->DocNum)) OR ($doc->status != 'D' && $this->_SuperAdmin)) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
		<?php endif; ?>
		<?php if(($this->pm->can_edit && $doc->status == 'O') OR ($doc->status != 'P' && $this->_SuperAdmin)) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="pullBack('<?php echo $doc->code; ?>')"><i class="fa fa-refresh"></i> ย้อนสถานะ</button>
		<?php endif; ?>
		<?php if($doc->status == 'C' && empty($doc->DocNum)) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToERP('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send To ERP</button>
		<?php endif; ?>
		<button type="button" class="btn btn-white btn-info top-btn hidden-xs" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div>
<hr class="padding-5" />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center e" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>รหัสผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm text-center e" value="<?php echo $doc->vender_code; ?>" disabled />
  </div>
  <div class="col-lg-4-harf col-md-6 col-sm-5-harf col-xs-12 padding-5">
  	<label>ผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vender_name; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>ใบสั่งซื้อ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-3 col-sm-2 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
	<div class="col-lg-3 col-md-7 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->warehouse_code . ' | '.$doc->warehouse_name; ?>" disabled />
	</div>
  <div class="col-lg-2 col-md-3 col-sm-2 col-xs-6 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-lg-5-harf col-md-7 col-sm-3 col-xs-6 padding-5">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled/>
	</div>
	<div class="col-xs-6 padding-5 visible-xs">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo receive_status_text($doc->status); ?>" disabled />
	</div>
  <div class="col-lg-9 col-md-8-harf col-sm-7 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-1-harf padding-5 hidden-xs">
		<label>ERP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5 hidden-xs">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo receive_status_text($doc->status); ?>" disabled />
	</div>
  <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
</div>

<?php
	if($doc->status == 'D')
	{
		$this->load->view('cancle_watermark');
	}

	if($doc->status == 'O')
	{
		$this->load->view('on_process_watermark');
	}
?>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered" style="min-width:700px;">
      <thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200 text-center">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
          <th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-100 text-right">จำนวนรับ</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
					<?php $total_receive = 0; ?>
					<?php $total_amount = 0; ?>
          <?php foreach($details as $rs) : ?>
						<?php $red = ($rs->qty == $rs->receive_qty) ? '' : 'red'; ?>
            <tr class="font-size-12 <?php echo $red; ?>">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
              <td class="middle text-right"><?php echo number($rs->qty); ?></td>
							<td class="middle text-right"><?php echo number($rs->receive_qty); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
						<?php $total_receive += $rs->receive_qty; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="3" class="text-right"><strong>รวม</strong></td>
            <td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_receive); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if($doc->status == 'D') : ?>
			<span class="red display-block">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancel_date, TRUE); ?> </span>
			<span class="red display-block">สาเหตุ : <?php echo $doc->cancle_reason; ?> </span>
		<?php endif; ?>
	</div>
</div>


<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
