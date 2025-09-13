<div class="receive-box">
  <?php  if(!empty($uncomplete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($uncomplete as $rs) : ?>
      <div class="col-xs-12 receive-item unvalid" id="receive-item-<?php echo $rs->id; ?>">
        <div class="width-100">
          <div class="margin-bottom-3 pre-wrap b-click"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap">SKU Code : <?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap">Description : <?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 font-size-18 text-right" style="padding-right:15px;">QTY : <?php echo number($rs->receive_qty); ?> / <?php echo number($rs->qty); ?></div>
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
          <div class="margin-bottom-3 font-size-18 text-right" style="padding-right:15px;">QTY : <?php echo number($rs->receive_qty); ?> / <?php echo number($rs->qty); ?></div>
        </div>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
