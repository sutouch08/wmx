<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-4 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-4 col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-4 col-xs-4 padding-5">
    <label>Posting date</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="posting-date" value="<?php echo empty($doc->shipped_date) ? "" : thai_date($doc->shipped_date); ?>" readonly disabled />
  </div>

  <div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-12 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm" id="fromWhs" value="<?php echo $doc->from_warehouse.' | '.warehouse_name($doc->from_warehouse); ?>" disabled />
    <input type="hidden" id="from-warehouse" value="<?php echo $doc->from_warehouse; ?>" />
	</div>

  <div class="col-lg-4-harf col-md-3-harf col-sm-8 col-xs-12 padding-5">
    <label>คลังปลายทาง</label>
		<select class="width-100 h edit" id="to-warehouse" disabled>
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->to_warehouse); ?>
		</select>
  </div>

	<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit h" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <?php if(($doc->status == 'P')) : ?>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
      <label class="display-block not-show">Submit</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
  		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
    </div>
  <?php endif; ?>
</div>

<hr class="margin-top-15 margin-bottom-15"/>

<script>
	$('#to-warehouse').select2();
</script>
