<div class="pg-summary pg-top" style="height:60px;">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-50">
        <span class="float-left font-size-16 width-50">Pending</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-50"
        style="color:white !important;"
        id="order-qty" value="<?php echo number($total_orders); ?>" readonly />
      </div>
      <div class="summary-text width-50">
        <span class="float-left font-size-16 width-50">Total</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-50"
        style="color:white !important;"
        id="total-qty" value="<?php echo number($total_qty); ?>" readonly />
      </div>
      <div class="summary-text width-50">
        <span class="float-left font-size-16 width-50">Boxes</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-50"
        style="color:white !important;"
        id="total-carton" value="<?php echo number($total_carton); ?>" readonly />
      </div>

      <div class="summary-text width-50">
        <span class="float-left font-size-16 width-50">Shipped</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-50"
        style="color:white !important;"
        id="total-shipped" value="<?php echo number($total_shipped); ?>" readonly />
      </div>
    </div>
  </div>
</div>


<div class="control-box" id="control-box">
  <div class="control-box-inner">
    <div class="width-100" id="order-add">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg text-center focus" style="padding-left:15px; padding-right:80px;" id="order-no" inputmode="none" autofocus placeholder="สแกนเพื่อเพิ่มออเดอร์">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:50px; color:grey; z-index:2;"></i>
        <i class="ace-icon fa fa-plus fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;"></i>
      </div>
    </div>
		<div class="width-100 hide" id="order-del">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg text-center focus" style="padding-left:15px; padding-right:80px;" id="del-order-no" inputmode="none" placeholder="สแกนเพื่อลบออเดอร์">
        <i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:10px; right:50px; color:grey; z-index:2;"></i>
        <i class="ace-icon fa fa-minus fa-2x" style="position:absolute; top:10px; right:15px; color:grey; z-index:2;"></i>
      </div>
    </div>
  </div>
</div>
