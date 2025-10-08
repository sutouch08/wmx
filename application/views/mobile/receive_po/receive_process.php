<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/receive_po/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="leave()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?></div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/receive_po/header_panel'); ?>

<div class="row">
  <div class="page-wrap" id="incomplete-box">
    <?php  if(!empty($incomplete)) : ?>
      <?php   foreach($incomplete as $rs) : ?>
        <div class="list-block receive-item unvalid" id="receive-item-<?php echo $rs->id; ?>">
          <div class="list-link">
            <div class="list-link-inner width-100">
              <div class="width-100">
                <span class="display-block font-size-11 b-click"><?php echo $rs->barcode; ?></span>
                <span class="display-block font-size-11">SKU : <?php echo $rs->product_code; ?></span>
                <span class="display-block font-size-11">Description : <?php echo $rs->product_name; ?></span>
                <div class="">
                  <span class="float-left font-size-14">QTY : </span>
                  <input type="text"
                  class="fix-width-50 text-right text-label font-size-14 receive-qty"
                  style="padding: 0px 3px"
                  id="receive-qty-<?php echo $rs->id; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-limit="<?php echo $rs->qty; ?>"
                  data-basecocd="<?php echo $rs->po_code; ?>"
                  data-baseline="<?php echo $rs->po_detail_id; ?>"
                  data-code="<?php echo $rs->product_code; ?>"
                  data-name="<?php echo $rs->product_name; ?>"
                  value="<?php echo number($rs->receive_qty); ?>" readonly/>
                  <span>/</span>
                  <input type="text"
                  class="fix-width-50 text-label text-left"
                  style="font-size:14px; padding: 0px 3px"
                  value="<?php echo number($rs->qty); ?>" readonly />
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
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php  if(!empty($complete)) : ?>
      <?php   foreach($complete as $rs) : ?>
        <div class="list-block receive-item valid" id="receive-item-<?php echo $rs->id; ?>">
          <div class="list-link">
            <div class="list-link-inner width-100">
              <div class="width-100">
                <span class="display-block font-size-11">SKU : <?php echo $rs->product_code; ?></span>
                <span class="display-block font-size-11">Description : <?php echo $rs->product_name; ?></span>
                <div class="">
                  <span class="float-left font-size-14">QTY : </span>
                  <input type="text"
                  class="fix-width-50 text-right text-label font-size-14 receive-qty"
                  style="padding: 0px 3px"
                  id="receive-qty-<?php echo $rs->id; ?>"
                  data-id="<?php echo $rs->id; ?>"
                  data-limit="<?php echo $rs->qty; ?>"
                  data-basecocd="<?php echo $rs->po_code; ?>"
                  data-baseline="<?php echo $rs->po_detail_id; ?>"
                  data-code="<?php echo $rs->product_code; ?>"
                  data-name="<?php echo $rs->product_name; ?>"
                  value="<?php echo number($rs->receive_qty); ?>" readonly/>
                  <span>/</span>
                  <input type="text"
                  class="fix-width-50 text-label text-left"
                  style="font-size:14px; padding: 0px 3px"
                  value="<?php echo number($rs->qty); ?>" readonly />
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
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>




<?php $this->load->view('mobile/receive_po/receive_control'); ?>
<?php $this->load->view('mobile/receive_po/process_menu'); ?>

<input type="hidden" id="order_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>" />
<input type="hidden" id="zone_code" />
<input type="hidden" id="finished" value="<?php echo $finished ? 1 : 0; ?>" />

<script src="<?php echo base_url(); ?>scripts/mobile/receive_po/receive_po.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/receive_po/receive_po_process.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
