<div class="complete-pad move-out" id="complete-pad">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeComplete()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  <div class="" id="complete-box">
    <?php  if( ! empty($complete)) : ?>
      <?php $no = 1; ?>
      <?php   foreach($complete as $rs) : ?>
        <div class="col-xs-12 receive-item valid" id="receive-item-<?php echo $rs->id; ?>">
          <div class="width-100">
            <div class="margin-bottom-3 pre-wrap b-click hide">Barcode : <?php echo $rs->barcode; ?></div>
            <div class="margin-bottom-3 pre-wrap">SKU Code : <?php echo $rs->product_code; ?></div>
            <div class="margin-bottom-3 pre-wrap">Description : <?php echo $rs->product_name; ?></div>
            <div class="margin-bottom-3 font-size-14">
              QTY :
              <input type="text"
              class="fix-width-50 text-right text-label receive-qty" style="font-size:14px; padding: 0px 3px"
              id="receive-qty-<?php echo $rs->id; ?>"
              data-id="<?php echo $rs->id; ?>"
              data-limit="<?php echo $rs->qty; ?>"
              data-basecocd="<?php echo $rs->po_code; ?>"
              data-baseline="<?php echo $rs->po_detail_id; ?>"
              data-code="<?php echo $rs->product_code; ?>"
              data-name="<?php echo $rs->product_name; ?>"
              value="<?php echo number($rs->receive_qty); ?>" readonly/>
              <span>/</span>
              <input type="text" class="fix-width-50 text-label text-left" style="font-size:14px; padding: 0px 3px"
               value="<?php echo number($rs->qty); ?>" readonly />
            </div>
          </div>
            <input type="hidden" id="balance-<?php echo $rs->id; ?>" value="<?php echo number($rs->qty - $rs->receive_qty); ?>" />
            <input type="hidden" class="buffer <?php echo $rs->barcode; ?>"
            id="buffer-<?php echo $rs->id; ?>"
            data-code="<?php echo $rs->product_code; ?>"
            data-limit="<?php echo $rs->qty; ?>"
            data-id="<?php echo $rs->id; ?>"
            value="0"	/>
            <button class="btn btn-minier btn-white btn-warning"
            style="position:absolute; top:10px; right:10px; border-radius:4px !important;"
            onclick="decreaseReceived(<?php echo $rs->id; ?>)">
            <i class="fa fa-minus"></i>
          </button>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
