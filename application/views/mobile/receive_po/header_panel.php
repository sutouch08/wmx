<div class="filter-pad move-out" id="header-panel">
  <div class="nav-title nav-title-center">
  	<a onclick="toggleHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center"><?php echo $doc->code; ?></div>
  </div>
  <div class="page-wrap">
    <div class="row">
      <div class="col-xs-12 fi">
        <label>Document No.</label>
        <input type="text" class="form-control" id="code" value="<?php echo $doc->code; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Document date</label>
        <input type="text" class="form-control" value="<?php echo thai_date($doc->date_add); ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Posting date</label>
        <input type="text" class="form-control" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Vender</label>
        <input type="text" class="form-control" value="<?php echo $doc->vender_code; ?> | <?php echo $doc->vender_name; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Po No.</label>
        <input type="text" class="form-control" value="<?php echo $doc->po_code; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>ERP Ref.</label>
        <input type="text" class="form-control" value="<?php echo $doc->po_ref; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Invoice</label>
        <input type="text" class="form-control" value="<?php echo $doc->invoice_code; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Warehouse</label>
        <input type="text" class="form-control" value="<?php echo $doc->warehouse_code; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Bin Location</label>
        <input type="text" class="form-control" value="<?php echo empty($zone) ? NULL : $zone->name; ?>" readonly />
      </div>
      <div class="col-xs-12 fi">
        <label>Remark</label>
        <textarea class="form-control" readonly><?php echo $doc->remark; ?></textarea>
      </div>
    </div>
  </div>
</div>
