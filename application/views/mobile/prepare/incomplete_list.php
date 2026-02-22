<div class="row">
  <div class="page-wrap" id="incomplete-box">
    <div id="close-bar" class="text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
    </div>

    <?php  if(!empty($incomplete)) : ?>
      <?php $no = 1; ?>
      <?php   foreach($incomplete as $rs) : ?>
        <div class="col-xs-12 incomplete-item" id="incomplete-<?php echo $rs->id; ?>">
          <div class="width-100" style="padding: 3px 3px 3px 10px;">
            <div class="margin-bottom-3 pre-wrap b-click " id="b-click-<?php echo $rs->id; ?>"><?php echo $rs->barcode; ?></div>
            <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
            <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
            <div class="margin-bottom-3 pre-wrap" style="display:flex;">
              <div class="width-33 float-left">จำนวน : <span id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span></div>
              <div class="width-33 float-left">จัดแล้ว : <span id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
              <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-<?php echo $rs->id; ?>"><?php echo number($rs->balance); ?></span></div>
            </div>
            <div class="divider margin-top-10 margin-bottom-10"></div>
            <span class="btn btn-minier btn-info stock-reload"
              onclick="reloadStockInZone(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>','<?php echo $order->warehouse_code; ?>')">
              <i class="fa fa-refresh"></i>
            </span>
            <div class="margin-bottom-3 stock-pre" id="stock-e-<?php echo $rs->id; ?>"><?php echo $rs->stock_in_zone; ?></div>
          </div>
          <span class="badge-qty" id="badge-qty-<?php echo $rs->id; ?>"><?php echo number($rs->balance); ?></span>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>

      

    <?php else : ?>
      <div class="text-center" id="close-bar">
        <button type="button" class="btn btn-lg btn-success" onclick="finishPrepare()">จัดเสร็จแล้ว</button>
      </div>
    <?php endif; ?>
  </div>
</div>
