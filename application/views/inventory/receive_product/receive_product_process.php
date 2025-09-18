<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>/assets/js/md5.min.js"></script>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 'O' && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
			<div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-save icon-on-left"></i>
          บันทึก
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="primary">
            <a href="javascript:saveAsDraft()">บันทึกเป็นดราฟท์</a>
          </li>
					<li class="success">
            <a href="javascript:save()">บันทึกรับเข้าทันที</a>
          </li>
        </ul>
      </div>
		<?php endif; ?>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
  	<label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Return Ref.</label>
		<input type="text" class="form-control input-sm text-center edit" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>From Whs</label>
		<input type="text" class="form-control input-sm text-center edit" id="from-warehouse" value="<?php echo $doc->from_warehouse; ?>" disabled />
	</div>
  <div class="col-lg-6-harf col-md-6 col-sm-4-harf col-xs-12 padding-5">
  	<label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
  </div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />

<hr class="margin-top-10 margin-bottom-10"/>
<?php $this->load->view('inventory/receive_product/receive_product_control'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1 table-padding-3" style="margin-bottom:0px; min-width:700px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">barcode</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-200">สินค้า</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">รับ</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php  $total_qty = 0; ?>
				<?php  $total_receive = 0; ?>
				<?php if(!empty($details)) : ?>
					<?php  $no = 1; ?>
					<?php  foreach($details as $rs) : ?>
						<tr class="font-size-11" id="row_<?php echo $rs->id; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle <?php echo $rs->id; ?>"><span class="bc"><?php echo $rs->bc; ?></span></td>
							<td class="middle <?php echo $rs->id; ?>"><?php echo $rs->product_code; ?></td>
							<td class="middle"><input type="text" class="form-control input-sm text-label" style="font-size:11px !important;" value="<?php echo $rs->product_name; ?>" readonly/></td>
							<td class="middle text-right">
								<input type="text"
								class="form-control input-sm text-center text-label receive-qty"
								id="receive-qty-<?php echo $rs->id; ?>"
								value="<?php echo number($rs->qty, 2); ?>" readonly/>
							</td>
							<td class="middle">
								<input type="number"
								class="form-control input-sm text-center input-qty text-label <?php echo $rs->barcode; ?> input-<?php echo $no; ?>"
								id="qty-<?php echo $rs->id; ?>"
								data-no="<?php echo $no; ?>"
								data-id="<?php echo $rs->id; ?>"
								data-linenum="<?php echo $rs->line_num; ?>"
								data-pdcode="<?php echo $rs->product_code; ?>"
								data-pdname="<?php echo $rs->product_name; ?>"
								data-qty="<?php echo round($rs->qty, 2); ?>"
								value="<?php echo $rs->receive_qty; ?>"
								onchange="recalRow(<?php echo $rs->id; ?>)"	/>
							</td>
						</tr>
						<?php		$no++;	?>
						<?php $total_qty += $rs->qty; ?>
						<?php $total_receive += $rs->receive_qty; ?>
					<?php  endforeach; ?>
				<?php endif; ?>
				<tr>
					<td colspan="4" class="middle text-right" style="padding:8px !important;">รวม</td>
					<td class="middle text-center" id="total-qty" style="padding:8px !important;"><?php echo number($total_qty); ?></td>
					<td class="middle text-center" id="total-receive" style="padding:8px !important;"><?php echo number($total_receive, 2); ?></td>
				</tr>
			</tbody>
		</table>

		<?php if( ! empty($barcode_list)) : ?>
			<?php foreach($barcode_list as $bc) : ?>
				<input type="hidden" class="bc" id="<?php echo $bc->barcode; ?>" data-sku="<?php echo $bc->product_code; ?>"  value="<?php echo $bc->product_code; ?>" />
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>



<script src="<?php echo base_url(); ?>scripts/inventory/receive_product/receive_product.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_product/receive_product_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_product/receive_product_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
