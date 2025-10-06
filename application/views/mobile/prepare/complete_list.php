<div class="row">
  <div class="page-wrap move-out" id="complete-box">
    <div class="nav-title nav-title-center">
      <a onclick="toggleCompleteBox()"><i class="fa fa-angle-left fa-2x"></i></a>
      <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
    </div>
    <div id="complete-list">
      <?php  if( ! empty($complete)) : ?>
        <?php $no = 1; ?>
        <?php   foreach($complete as $rs) : ?>
          <div class="col-xs-12 complete-item" id="complete-<?php echo $rs->id; ?>">
            <div class="width-100" style="padding: 3px 3px 3px 10px;">
              <div class="margin-bottom-3 pre-wrap"><?php echo $rs->barcode; ?></div>
              <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
              <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
              <div class="margin-bottom-3 pre-wrap">
                <div class="width-33 float-left">จำนวน : <span id="order-qty-<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span></div>
                <div class="width-33 float-left">จัดแล้ว : <span id="prepared-qty-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></span></div>
                <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-<?php echo $rs->id; ?>">0</span></div>
              </div>
              <div class="margin-bottom-3 pre-wrap">Location : <?php echo $rs->from_zone; ?></div>
            </div>
            <button type="button" class="btn btn-mini btn-danger"
              style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
              onclick="removeBuffer('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>', '<?php echo $rs->id; ?>')">
            <i class="fa fa-trash"></i>
          </button>
          </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    </div>
  </div>
</div>

<script id="complete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 complete-item" id="complete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{{from_zone}}}</div>
    </div>
    <button type="button" class="btn btn-mini btn-danger"
      style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
      onclick="removeBuffer('{{order_code}}', '{{product_code}}', '{{id}}')">
    <i class="fa fa-trash"></i>
  </button>
  </div>
</script>
