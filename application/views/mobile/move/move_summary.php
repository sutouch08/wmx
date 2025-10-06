<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <span class="float-left font-size-16 width-50">รายการโอนย้าย</span>
        <span class="float-left font-size-16 width-20">Total</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-30 text-center"
        style="color:white !important;"
        id="move-total" value="<?php echo number($totalQty); ?>" readonly />
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="page-wrap" id="summary-list">
    <?php if( ! empty($details)) : ?>
      <?php $no = 1; ?>
      <?php foreach($details as $rs) : ?>
        <div class="list-block move-row" id="move-row-<?php echo $rs->id; ?>" onclick="toggleMoveChecked(<?php echo $rs->id; ?>)">
          <input type="checkbox" class="move-chk hide" id="move-chk-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="no"><?php echo $no; ?></div>
							<div class="padding-5">
								<span class="display-block font-size-11"><?php echo $rs->product_code; ?></span>
								<span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
                <span class="display-block font-size-11 blue">From : <?php echo $rs->from_zone; ?></span>
								<span class="display-block font-size-11 green">To : <?php echo $rs->to_zone; ?></span>
							</div>
              <div class="text-center move-qty-box">
                <span class="font-size-11">Qty</span>
                <input type="text" class="text-center text-label move-qty" id="move-qty-<?php echo $rs->id; ?>" value="<?php echo number($rs->qty); ?>" />
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>


<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>

      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('move_out')">
					<i class="fa fa-upload fa-2x"></i><span>ย้ายออก</span>
				</span>
			</div>

      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('move_in')">
					<i class="fa fa-download fa-2x"></i><span>ย้ายเข้า</span>
				</span>
			</div>

      <div class="footer-menu">
        <span class="pg-icon" onclick="deleteMoveItems()">
          <i class="fa fa-times fa-2x"></i><span>ลบ</span>
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
      <div class="footer-menu">
        <span class="pg-icon" onclick="confirmCancel()">
          <i class="fa fa-exclamation-triangle fa-2x"></i><span>ยกเลิก</span>
        </span>
      </div>
      <div class="footer-menu">
        <span class="pg-icon" onclick="findItem()">
          <i class="fa fa-search fa-2x"></i><span>Find item</span>
        </span>
      </div>

      <div class="footer-menu">
				<span class="pg-icon" onclick="confirmSave()">
					<i class="fa fa-save fa-2x"></i><span>Save</span>
				</span>
			</div>
    </div>
  </div>
</div>
