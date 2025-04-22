<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 paddig-top-5">
    <h3 class="title"> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($po->status == 'O' && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="closePO()"><i class="fa fa-lock"></i> Close PO</button>
		<?php endif; ?>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $po->status == 'P') : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $po->code; ?>')"><i class="fa fa-pencil"></i> Edit</button>
		<?php endif; ?>
		<?php if(($po->status == 'P' OR $po->status == 'O') && $this->pm->can_delete) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="goCancel('<?php echo $po->code; ?>')"><i class="fa fa-times"></i> Cancel</button>
		<?php endif; ?>
		<?php if($po->status == 'O' OR (($po->status == 'C' OR $po->status == 'D') && $this->_SuperAdmin)) : ?>
			<?php if(($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-white btn-purple top-btn" onclick="unsave()"><i class="fa fa-refresh"></i> ย้อนสถานะ</button>
			<?php endif; ?>
		<?php endif; ?>
		<?php if($po->status != 'P' && $po->status != 'D') : ?>
			<button type="button" class="btn btn-white btn-info top-btn btn-100" onclick="printPO()"><i class="fa fa-print"></i> พิมพ์</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="form-horizontal">
  <div class="row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>เลขที่</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $po->code; ?>" disabled>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" disabled readonly required>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>ผู้ขาย</label>
			<input type="text" class="form-control input-sm text-center edit" name="vender_code" id="vender_code" value="<?php echo $po->vender_code; ?>" disabled required>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
			<label class="not-show">ผู้ขาย</label>
			<input type="text" class="form-control input-sm edit" name="vender_name" id="vender_name" value="<?php echo $po->vender_name; ?>" disabled required>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>กำหนดส่ง</label>
			<input type="text" class="form-control input-sm text-center edit" name="require_date" id="require_date" value="<?php echo thai_date($po->due_date); ?>" disabled readonly required>
		</div>

		<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $po->remark; ?>" disabled>
		</div>

		<?php $status = $po->status == 'D' ? 'Canceled' : ($po->status == 'C' ? 'Closed' : ($po->status == 'O' ? 'Open' : 'Draft')); ?>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สถานะ</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $status; ?>" disabled/>
		</div>
  </div>

	<input type="hidden" id="code" value="<?php echo $po->code; ?>">
	<input type="hidden" id="id" value="<?php echo $po->id; ?>">
</div>

<?php if($po->status == 'D') { $this->load->view('cancle_watermark'); } ?>

<hr class="margin-top-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:930px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-150 text-center">รหัสสินค้า</th>
          <th class="min-width-250 text-center">ชื่อสินค้า</th>
          <th class="fix-width-80 text-center">หน่วยนับ</th>
          <th class="fix-width-100 text-center">ราคา</th>
          <th class="fix-width-100 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">ค้างรับ</th>
          <th class="fix-width-100 text-center">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="detail-table">
      <?php if( ! empty($details)) : ?>
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
				<?php $total_open = 0; ?>
        <?php $total_amount = 0; ?>
        <?php foreach($details as $rs) : ?>
					<?php $line_total = $rs->qty * $rs->price; ?>
        <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-center"><?php echo $rs->unit_code; ?></td>
          <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
					<td class="middle text-right"><?php echo number($rs->open_qty, 2); ?></td>
          <td class="middle text-right"><?php echo number($line_total, 2); ?></td>
        </tr>
          <?php $no++; ?>
          <?php $total_qty += $rs->qty; ?>
					<?php $total_open += $rs->open_qty; ?>
          <?php $total_amount += $line_total; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="5" class="text-right">รวม</td>
          <td class="text-right" id="total-qty"><?php echo number($total_qty, 2); ?></td>
					<td class="text-right" id="total-open"><?php echo number($total_open, 2); ?></td>
          <td class="text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
        </tr>
      <?php else : ?>
        <tr>
          <td colspan="9" class="text-center">--- ไม่พบรายการ ---</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/purchase/po.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
