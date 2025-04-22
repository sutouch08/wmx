<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<select class="width-100 h f" id="from-warehouse" disabled>
			<option value="">เลือกคลังต้นทาง</option>
			<?php echo select_warehouse($doc->from_warehouse); ?>
		</select>
	</div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
    <label>คลังปลายทาง</label>
		<select class="width-100 h f" id="to-warehouse" disabled>
			<option value="">เลือกคลังปลายทาง</option>
			<?php echo select_warehouse($doc->to_warehouse); ?>
		</select>
  </div>

	<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit h" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <?php if(($doc->status == -1 OR $doc->status == 0)) : ?>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
  </div>
  <?php else : ?>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label>User</label>
      <input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled>
    </div>
  <?php endif; ?>
</div>

<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>

<script>
	$('#from-warehouse').select2();
	$('#to-warehouse').select2();
</script>
