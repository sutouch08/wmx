<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <span class="font-size-16 width-30">Total</span>
        <input type="text"
        class="font-size-16 text-label padding-0 width-80 text-center"
        style="color:white !important;"
        id="total-qty" value="<?php echo number($totalQty); ?>" readonly />
      </div>
    </div>
  </div>
</div>

<div class="control-box" id="control-box">
  <div class="control-box-inner">
    <div class="width-100 e-zone" id="zone-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="none" placeholder="Barcode Zone" autocomplete="off">
        <i class="ace-icon fa fa-qrcode fa-2x control-icon"></i>
      </div>
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="form-control width-30 input-lg focus text-center" style="margin-left:10px; margin-right:10px; padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="input-group width-100 e-item hide" id="item-bc">
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
      <i class="ace-icon fa fa-qrcode fa-2x  control-icon"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="zone-name">กรุณาระบุโซน</div>
