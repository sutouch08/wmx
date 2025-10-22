<div class="row">
  <div class="col-lg-2-harf col-md-2 col-sm-4 col-xs-8 padding-5">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm e" id="item-code" autofocus />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ราคา</label>
    <input type="text" class="form-control input-sm text-center e" id="item-price" value="" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ส่วนลด</label>
    <input type="text" class="form-control input-sm text-center e" id="item-disc" value="" />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-2 col-xs-4 padding-5">
    <label>ในโซน</label>
    <input type="text" class="form-control input-sm text-center blue" id="item-stock" value="" readonly />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center e" id="input-qty" value="" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-4 padding-5">
    <label>มูลค่า</label>
    <input type="text" class="form-control input-sm text-center e" id="item-amount" value="" readonly />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5">
    <label class="not-show">submit</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addToDetail()">เพิ่ม</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5">
    <label class="not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-default btn-block" onclick="clearFields()">เคลียร์</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-xs btn-danger btn-clock" onclick="removeChecked()"><i class="fa fa-trash"></i> ลบรายการ</button>
  </div>
</div>

<input type="hidden" id="product_code" />
<input type="hidden" id="count-stock" value="1" />
<hr />
<div class="row margin-bottom-5">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
  <?php if(getConfig('ALLOW_EDIT_PRICE')) : ?>
    <button type="button" class="btn btn-xs btn-warning" id="btn-edit-price" onclick="getEditPrice()">แก้ไขราคา</button>
    <button type="button" class="btn btn-xs btn-primary hide" id="btn-update-price" onclick="updatePrice()">บันทึกราคา</button>
  <?php endif; ?>
  <?php if(getConfig('ALLOW_EDIT_DISCOUNT')) : ?>
    <button type="button" class="btn btn-xs btn-warning" id="btn-edit-disc" onclick="getEditDiscount()">แก้ไขส่วนลด</button>
    <button type="button" class="btn btn-xs btn-primary hide" id="btn-update-disc" onclick="updateDiscount()">บันทึกส่วนลด</button>
  <?php endif; ?>
  </div>
</div>
