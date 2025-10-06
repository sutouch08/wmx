<div class="filter-pad move-out" id="header-panel">
  <div class="nav-title nav-title-center">
  	<a onclick="toggleHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center"><?php echo $order->code; ?></div>
  </div>
  <div class="page-wrap">
    <div class="col-xs-12">
  		<label>เลขที่</label>
  		<input type="text" class="form-control text-center r e" value="<?php echo $order->code; ?>" disabled />
      <input type="hidden" id="id" value="<?php echo $order->id; ?>" />
      <input type="hidden" id="code" value="<?php echo $order->code; ?>" />
  	</div>
    <div class="divider-hidden"></div>
    <div class="col-xs-6">
  		<label>วันที่</label>
  		<input type="text" class="form-control text-center" value="<?php echo thai_date($order->date_add); ?>" readonly disabled />
  	</div>
    <div class="col-xs-6">
  		<label>รหัสลูกค้า</label>
  		<input type="text" class="form-control text-center" value="<?php echo $order->customer_code; ?>" disabled />
  	</div>
    <div class="divider-hidden"></div>

    <div class="col-xs-12">
  		<label>ชื่อลูกค้า/ผู้เบิก/ผู้ยืม</label>
  		<input type="text" class="form-control" value="<?php echo $order->customer_name;  ?>" disabled />
  	</div>
    <div class="divider-hidden"></div>

    <?php if($order->role == 'S') : ?>
      <div class="col-xs-12">
        <label>ช่องทางขาย</label>
        <input type="text" class="form-control" value="<?php echo $order->channels_name; ?>" disabled />
      </div>
      <div class="divider-hidden"></div>
    <?php endif; ?>

    <div class="col-xs-12">
      <label>คลัง</label>
      <input type="text" class="form-control" value="<?php echo $order->warehouse_name; ?>" disabled />
    </div>
    <div class="divider-hidden"></div>

    <div class="col-xs-12">
      <label>หมายเหตุ</label>
      <textarea class="form-control e" id="remark" disabled><?php echo $order->remark; ?></textarea>
    </div>
  </div>
</div>
