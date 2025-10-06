<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
    <label>คลัง</label>
    <input type="text" class="width-100" value="<?php echo $doc->warehouse_code. ' | '.$doc->warehouse_name; ?>" disabled />
  </div>
  <div class="col-lg-4 col-md-4 col-sm-3-harf col-xs-6 padding-5">
    <label>อ้างอิง</label>
    <input type="text" class="width-100" value="<?php echo $doc->reference; ?>" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center"  value="<?php echo status_text($doc->status); ?>" disabled>
  </div>
  <div class="col-lg-9 col-md-8-harf col-sm-8-harf col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center"  value="<?php echo $doc->user; ?>" disabled>
  </div>
</div>
<input type="hidden" id="move-code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="warehouse-code" value="<?php echo $doc->warehouse_code; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
<?php if($doc->status == 'D') { $this->load->view('cancle_watermark'); } ?>
