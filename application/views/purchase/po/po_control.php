<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 padding-5">
    <label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center c" name="pdCode" id="pd-code" value="" autofocus>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">ดึงรายการ</button>
  </div>

  <div class="col-lg-2 col-lg-offset-1 col-md-2 col-md-offset-1 col-sm-3 col-xs-4 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center c" name="itemCode" id="item-code" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center c" name="qty" id="item-qty" value="" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">qty</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addRow()">Add</button>
  </div>

  <div class="col-lg-1-harf col-lg-offset-2 col-md-1-harf col-md-offset-1 col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">delete</label>
    <button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeChecked()">ลบรายการ</button>
  </div>
</div>
<hr class="margin-top-15">
