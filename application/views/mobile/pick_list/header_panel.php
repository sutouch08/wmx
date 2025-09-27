<div class="filter-pad move-out" id="header-panel">
  <div class="nav-title nav-title-center">
    <a class="hidden-lg" onclick="toggleHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center"><?php echo $doc->code; ?></div>
  </div>
  <div class="page-wrap">
    <div class="col-xs-6">
  		<label>เลขที่</label>
  		<input type="text" class="form-control text-center r e" id="code" value="<?php echo $doc->code; ?>" disabled/>
      <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  	</div>
  	<div class="col-xs-6">
  		<label>วันที่</label>
  		<input type="text" class="form-control text-center r e" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
  	</div>
    <div class="divider-hidden"></div>

    <div class="col-xs-12">
  		<label>ช่องทางขาย</label>
      <select class="form-control" id="channels" disabled>
        <option value="">ไม่ระบุ</option>
        <?php echo select_channels($doc->channels_code); ?>
      </select>
  	</div>
  	<div class="divider-hidden"></div>

    <div class="col-xs-12">
  		<label>คลังต้นทาง</label>
      <select class="form-control" id="warehouse" disabled>
        <option value="">เลือก</option>
        <?php echo select_common_warehouse($doc->warehouse_code); ?>
      </select>
  	</div>
  	<div class="divider-hidden"></div>

  	<div class="col-xs-12">
  		<label>โซนปลายทาง</label>
      <select class="form-control" id="zone-code" disabled>
        <option value="">ไม่ระบุ</option>
        <?php echo select_pickface_zone($doc->zone_code); ?>
      </select>
  	</div>
  	<div class="divider-hidden"></div>

  	<div class="col-xs-12">
  		<label>หมายเหตุ</label>
      <textarea class="form-control e" id="remark" disabled><?php echo $doc->remark; ?></textarea>
  	</div>
  </div>
</div>
