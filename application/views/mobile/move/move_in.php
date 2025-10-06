<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <span class="float-left font-size-16 width-40">ย้ายสินค้าเข้า</span>
        <span class="float-left font-size-16 width-30">Temp Qty</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-30 text-center"
        style="color:white !important;"
        id="temp-total" value="<?php echo number($totalQty); ?>" readonly />
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="page-wrap" id="temp-list">
    <?php if( ! empty($details)) : ?>
      <?php $no = 1; ?>
      <?php foreach($details as $rs) : ?>
        <div class="list-block temp-row" id="temp-row-<?php echo $rs->id; ?>" onclick="toggleTempChecked(<?php echo $rs->id; ?>)">
          <input type="checkbox" class="temp-chk hide" id="temp-chk-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="no"><?php echo $no; ?></div>
							<div class="padding-5">
								<span class="display-block font-size-11"><?php echo $rs->product_code; ?></span>
								<span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
                <span class="display-block font-size-11 blue">From : <?php echo $rs->zone_name; ?></span>
							</div>
              <div class="text-center temp-qty-box">
                <span class="font-size-12">Qty</span>
                <input type="text" class="text-center text-label temp-qty" id="temp-qty-<?php echo $rs->id; ?>" value="<?php echo number($rs->qty); ?>" readonly/>
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="control-box" id="control-box">
  <div class="control-box-inner">
    <div class="width-100" id="zone-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="none" placeholder="Barcode Zone" autocomplete="off">
        <i class="ace-icon fa fa-keyboard-o fa-2x control-icon icon-keyboard hide" onclick="hideKeyboard()"></i>
        <i class="ace-icon fa fa-qrcode fa-2x control-icon icon-qr" onclick="showKeyboard()"></i>
      </div>
      <input type="hidden" id="zone-code" value="" />
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="form-control width-30 input-lg focus text-center" style="margin-left:10px; margin-right:10px; padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="input-group width-100 hide" id="item-bc">
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
      <i class="ace-icon fa fa-keyboard-o fa-2x control-icon icon-keyboard hide" onclick="hideKeyboard()"></i>
      <i class="ace-icon fa fa-qrcode fa-2x control-icon icon-qr" onclick="showKeyboard()"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text" id="zone-name">กรุณาระบุโซน</div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="changeZone()">
					<i class="fa fa-repeat fa-2x"></i><span>เปลี่ยนโซน</span>
				</span>
			</div>

      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('summary')">
					<i class="fa fa-list fa-2x"></i><span>รายการโอน</span>
				</span>
			</div>

      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('move_out')">
					<i class="fa fa-upload fa-2x"></i><span>ย้ายออก</span>
				</span>
			</div>

			<div class="footer-menu">
				<span class="pg-icon" onclick="toggleExtramenu()">
					<i class="fa fa-bars fa-2x"></i><span>เพิ่มเติม</span>
				</span>
			</div>
		</div>
 </div>
</div>

<div class="extra-menu slide-out" id="extra-menu">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu not-show">
        <span class="pg-icon">
          <i class="fa fa-search fa-2x"></i><span>Dummy</span>
        </span>
      </div>
      <div class="footer-menu not-show">
        <span class="pg-icon">
          <i class="fa fa-search fa-2x"></i><span>Dummy</span>
        </span>
      </div>
      <div class="footer-menu not-show">
        <span class="pg-icon">
          <i class="fa fa-search fa-2x"></i><span>Dummy</span>
        </span>
      </div>
      <div class="footer-menu">
        <span class="pg-icon" onclick="findItem()">
          <i class="fa fa-search fa-2x"></i><span>Find item</span>
        </span>
      </div>
      <div class="footer-menu">
        <span class="pg-icon" onclick="deleteTemp()">
          <i class="fa fa-times fa-2x"></i><span>ลบ</span>
        </span>
      </div>
    </div>
  </div>
</div>
