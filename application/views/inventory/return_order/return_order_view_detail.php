<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-xs btn-info top-btn" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>Doc No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Date</label>
		<input type="text" class="form-control input-sm text-center edit" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>Ref No.</label>
		<input type="text" class="form-control input-sm text-center edit" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-5-harf col-sm-4-harf col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm edit" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled/>
	</div>
	<div class="col-lg-3 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" class="form-control input-sm edit" id="warehouse" value="<?php echo $doc->warehouse_name; ?>" disabled />
	</div>
	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center edit" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>
	<div class="col-lg-6-harf col-md-4-harf col-sm-4-harf col-xs-8 padding-5">
		<label>ชื่อโซน]</label>
		<input type="text" class="form-control input-sm edit" id="zone-name" value="<?php echo $doc->zone_name; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm" id="status" disabled>
			<option value="all">ทั้งหมด</option>
			<option value="P" <?php echo is_selected('P', $doc->status); ?>>Pending</option>
			<option value="O" <?php echo is_selected('O', $doc->status); ?>>In progress</option>
			<option value="C" <?php echo is_selected('C', $doc->status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $doc->status); ?>>Canceled</option>
		</select>
	</div>
	<div class="col-lg-9 col-md-8 col-sm-8 col-xs-8 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled/>
	</div>

	<input type="hidden" id="return-code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="return-id" value="<?php echo $doc->id; ?>" />
</div>

<hr class="margin-top-15 margin-bottom-15"/>

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
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered border-1" style="min-width:700px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-50 text-center">#</th>
					<th class="fix-width-150">SKU</th>
					<th class="min-width-200">Description</th>
					<th class="fix-width-100 text-right">Return Qty</th>
					<th class="fix-width-100 text-right">Received Qty</th>
					<th class="fix-width-100 text-center">Status</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $no = 1; ?>
<?php  $ro = 5; ?>
<?php  $total_qty = 0; ?>
<?php  $total_reveice_qty = 0; ?>
<?php if(!empty($details)) : ?>
<?php  foreach($details as $rs) : ?>
	<?php $hilight = $rs->qty > $rs->receive_qty ? "color:red;" : ""; ?>
	<tr class="font-size-11" style="<?php echo $hilight; ?>">
		<td class="middle text-center no"><?php echo $no; ?></td>
		<td class="middle"><?php echo $rs->product_code; ?></td>
		<td class="middle"><?php echo $rs->product_name; ?></td>
		<td class="middle text-right"><?php echo round($rs->qty,2); ?></td>
		<td class="middle text-right"><?php echo round($rs->receive_qty,2); ?></td>
		<td class="middle text-center">
			<?php echo $rs->line_status == 'D' ? 'Canceled' : ($rs->line_status == 'C' ? 'Closed' : ($rs->line_status == 'O' ? 'In Progress' : 'Pending')); ?>
		</td>
	</tr>
	<?php $no++; ?>
	<?php $total_qty += $rs->qty; ?>
	<?php $total_reveice_qty += $rs->receive_qty; ?>
<?php  endforeach; ?>
<?php endif; ?>

<?php  while($ro >= $no) : ?>
	<tr class="font-size-11">
		<td class="middle text-center no"><?php echo $no; ?></td>
		<td class="middle"></td>
		<td class="middle"></td>
		<td class="middle"></td>
		<td class="middle"></td>
		<td class="middle"></td>
	</tr>
	<?php $no++; ?>
<?php endwhile; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if(!empty($approve_list)) :?>
			<?php foreach($approve_list as $appr) : ?>
					<?php if($appr->approve == 1) : ?>
						<span class="green display-block">อนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?></span>
					<?php endif; ?>
					<?php if($appr->approve == 0) : ?>
						<span class="red display-block">ยกเลิกการอนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?></span>
					<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>

<script>
	$('#ship-date').datepicker({
		'dateFormat' : 'dd-mm-yy'
	});

	function activeShipDate() {
		$('#ship-date').removeAttr('disabled');
		$('#btn-edit-ship-date').addClass('hide');
		$('#btn-update-ship-date').removeClass('hide');
	}

	function updateShipDate() {
		let shipDate = $('#ship-date').val();
		let code = $('#return_code').val();

		$.ajax({
			url:BASE_URL + 'inventory/return_order/update_shipped_date',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'shipped_date' : shipDate
			},
			success:function(rs) {
				if(rs.trim() === 'success') {
					$('#ship-date').attr('disabled', 'disabled');
					$('#btn-update-ship-date').addClass('hide');
					$('#btn-edit-ship-date').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						type:'error',
						text:rs
					});
				}
			}
		})
	}
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('YmdH');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('YmdH');?>"></script>
<?php $this->load->view('include/footer'); ?>
