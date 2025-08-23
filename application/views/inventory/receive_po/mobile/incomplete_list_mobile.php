<div class="incomplete-box" id="incomplete-box">
  <?php  if(!empty($uncomplete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($uncomplete as $rs) : ?>
      <div class="col-xs-12 receive-item unvalid" id="receive-item-<?php echo $rs->id; ?>">
        <div class="width-100">
          <div class="margin-bottom-3 pre-wrap b-click"><?php echo $rs->barcode; ?></div>
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

        <div class="btn-group option-right">
          <button class="btn btn-minier dropdown-toggle btn-options" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-ellipsis-v fa-lg"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="javascript:decreaseReceived(<?php echo $rs->id; ?>)">Remove 1 pcs</a></li>
            <li><a href="javascript:resetReceived(<?php echo $rs->id; ?>)">Reset received Qty</a></li>
          </ul>
        </div>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>

    <div id="close-bar" class="width-100 text-center hide <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success btn-block hide" onclick="finishReceive()"><i class="fa fa-check"></i> รับเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="width-100 text-center hide" id="close-bar">
      <button type="button" class="btn btn-lg btn-success btn-block hide" onclick="finishReceive()"><i class="fa fa-check"></i> รับเสร็จแล้ว</button>
    </div>
  <?php endif; ?>


  <?php  if( ! empty($complete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($complete as $rs) : ?>
      <div class="col-xs-12 receive-item valid" id="receive-item-<?php echo $rs->id; ?>">
        <div class="width-100">
          <div class="margin-bottom-3 pre-wrap b-click"><?php echo $rs->barcode; ?></div>
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
          <div class="btn-group option-right">
            <button class="btn btn-minier dropdown-toggle btn-options" data-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-ellipsis-v fa-lg"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
              <li><a href="javascript:decreaseReceived(<?php echo $rs->id; ?>)">Remove 1 pcs</a></li>
              <li><a href="javascript:resetReceived(<?php echo $rs->id; ?>)">Reset received Qty</a></li>
            </ul>
          </div>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
