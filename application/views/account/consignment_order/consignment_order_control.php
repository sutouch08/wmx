<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm c" id="barcode-item" />
  </div>
  <div class="col-lg-2-harf col-md-2 col-sm-4 col-xs-8 padding-5">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm c" id="item-code" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ราคา</label>
    <input type="number" class="form-control input-sm text-center c" id="item-price" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ส่วนลด</label>
    <input type="text" class="form-control input-sm text-center c" id="item-disc" />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-2 col-xs-4 padding-5">
    <label>ในโซน</label>
    <input type="text" class="form-control input-sm text-center blue e" id="stock-qty" value="0" readonly />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center c" id="item-qty" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-4 padding-5">
    <label>มูลค่า</label>
    <input type="text" class="form-control input-sm text-center" id="item-amount" value="0" readonly />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addToDetail()">เพิ่ม</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5">
    <label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-xs btn-default btn-block" onclick="clearFields()">เคลียร์</button>
  </div>

  <input type="hidden" id="product_code" />
  <input type="hidden" id="count_stock" value="1" />
</div>
<hr class="margin-top-15 margin-bottom-15">
<?php if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete) : ?>
<div class="row margin-bottom-10">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-xs btn-danger" onclick="removeChecked()"><i class="fa fa-trash"></i> &nbsp; Delete</button>
  </div>
</div>
<?php endif; ?>
