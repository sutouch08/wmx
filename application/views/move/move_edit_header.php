<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
    <label>คลังต้นทาง</label>
    <select class="width-100" id="from-warehouse" disabled>
      <option value="">เลือก</option>
      <?php echo select_warehouse($doc->from_warehouse); ?>
    </select>
  </div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
    <label>คลังปลายทาง</label>
    <select class="width-100" id="to-warehouse" disabled>
      <option value="">เลือก</option>
      <?php echo select_warehouse($doc->to_warehouse); ?>
    </select>
  </div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm text-center"  value="<?php echo $doc->user; ?>" disabled>
  </div>
</div>
<input type="hidden" id="move_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
<script>
  $('#from-warehouse').select2();
  $('#to-warehouse').select2();
</script>
