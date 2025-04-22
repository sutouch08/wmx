<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<select class="width-100 h f" id="from-warehouse" disabled>
			<option value="">เลือกคลังต้นทาง</option>
			<?php echo select_warehouse($doc->from_warehouse); ?>
		</select>
	</div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
    <label>คลังปลายทาง</label>
		<select class="width-100 h f" id="to-warehouse" disabled>
			<option value="">เลือกคลังปลายทาง</option>
			<?php echo select_warehouse($doc->to_warehouse); ?>
		</select>
  </div>


  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm edit" disabled>
			<option>Unknow</option>
      <option <?php echo is_selected('-1', $doc->status); ?>>ยังไม่บันทึก</option>
      <option <?php echo is_selected('0', $doc->status); ?>>รออนุมัติ</option>
      <option <?php echo is_selected('4', $doc->status); ?>>รอยืนยัน</option>
      <option <?php echo is_selected('3', $doc->status); ?>>WMS Process</option>
      <option <?php echo is_selected('1', $doc->status); ?>>สำเร็จ</option>
      <option <?php echo is_selected('2', $doc->status); ?>>ยกเลิก</option>
		</select>
	</div>

  <div class="col-lg-8-harf col-md-8 col-sm-8 col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5 hidden-xs">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled >
  </div>

  <?php if($doc->status == 2 && ! empty($doc->cancle_reason)) : ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
      <label>เหตุผลในการยกเลิก</label>
      <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
    </div>
  <?php endif; ?>
</div>
<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
<?php if($doc->must_accept == 1) : ?>
<div class="row margin-bottom-10">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <span class="">รายชื่อเจ้าของโซน : </span>
    <?php if( ! empty($accept_list)) : ?>
      <?php foreach($accept_list AS $ac) : ?>
        <?php if($ac->is_accept == 1) : ?>
          <span class="label label-success label-white middle"><i class="fa fa-check-circle"></i> <?php echo $ac->display_name; ?></span>
        <?php else : ?>
          <span class="label label-default label-white middle"><?php echo $ac->display_name; ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>

<script>
	$('#from-warehouse').select2();
	$('#to-warehouse').select2();
</script>
