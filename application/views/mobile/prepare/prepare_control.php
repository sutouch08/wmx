<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-45 text-right"
        style="color:white !important;"
        id="pick-qty" value="<?php echo number($pickedQty); ?>" readonly />
        <span class="float-left font-size-16 text-center width-10">/</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-45 text-left"
        style="color:white !important;"
        id="order-qty" value="<?php echo number($orderQty); ?>" readonly />
      </div>
    </div>
  </div>
</div>

<?php $showKeyboard = get_cookie('showKeyboard'); ?>
<?php $inputmode = $showKeyboard ? 'text' : 'none'; ?>
<?php $keyboard = $showKeyboard ? '' : 'hide'; ?>
<?php $qr = $showKeyboard ? 'hide' : ''; ?>

<div id="control-box">
  <div class="control-box-inner">
    <div class="width-100 e-zone" id="zone-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus"
        style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="<?php echo $inputmode; ?>" placeholder="Barcode Zone" autocomplete="off">
        <i class="ace-icon fa fa-keyboard-o fa-2x control-icon <?php echo $keyboard; ?>" id="zone-keyboard" onclick="hideKeyboard('zone')"></i>
        <i class="ace-icon fa fa-qrcode fa-2x control-icon <?php echo $qr; ?>" id="zone-qr" onclick="showKeyboard('zone')"></i>
      </div>
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center e-item hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="form-control width-30 input-lg focus text-center" style="margin-left:10px; margin-right:10px; padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="input-group width-100 e-item hide" id="item-bc">
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="<?php echo $inputmode; ?>"  placeholder="Barcode Item" autocomplete="off">
      <i class="ace-icon fa fa-keyboard-o fa-2x control-icon <?php echo $keyboard; ?>"  onclick="hideKeyboard('item')"></i>
      <i class="ace-icon fa fa-qrcode fa-2x  control-icon <?php echo $qr; ?>" onclick="showKeyboard('item')"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="zone-name">กรุณาระบุโซน</div>
