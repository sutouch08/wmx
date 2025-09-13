<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดโซน</label>
    <input type="text" class="form-control input-sm text-center" id="barcode-zone" value="<?php echo $doc->zone_code; ?>" onchange="getZone()" placeholder="ยิงบาร์โค้ดโซน"  />
    <input type="hidden" id="zone-code" value="<?php echo $doc->zone_code; ?>" />
  </div>
  <div class="col-lg-3 col-md-3 col-sm-2-harf col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm" id="zone-name" value="<?php echo $doc->zone_name; ?>" readonly/>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 padding-5">
    <label class="display-block not-show">Change</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="changeZone()">เปลี่ยน</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty" value="1" />
  </div>
  <div class="col-lg-2 col-md-3 col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off" autofocus />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 padding-5">
    <label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="doReceive()"><i class="fa fa-check"></i> ตกลง</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15"/>
