<div class="counter text-center">
  <input type="text" class="width-40 counter-input text-right" id="all-qty" value="<?php echo number($totalReceive); ?>" readonly/>
  /
  <input type="text" class="width-40 counter-input text-left" value="<?php echo number($allQty); ?>" readonly/>
</div>

<?php $this->load->view('inventory/receive_po/mobile/receive_item_mobile'); ?>

<input type="hidden" id="code" value="<?php echo $doc->code; ?>">
