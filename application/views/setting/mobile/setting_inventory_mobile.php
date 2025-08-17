<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Inventory setting</span>
</div>

<form id="inventoryForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">สต็อกติดลบได้</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_UNDER_ZERO" type="checkbox" value="1" <?php echo is_checked($ALLOW_UNDER_ZERO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รับสินค้าเกินใบสั่งซื้อ</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_RECEIVE_OVER_PO" type="checkbox" value="1" <?php echo is_checked($ALLOW_RECEIVE_OVER_PO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="ALLOW_RECEIVE_OVER_PO" id="allow-receive-over-po" value="<?php echo $ALLOW_RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อหรือไม่</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">รับสินค้าเกินไปสั่งซื้อ(%)</div>
		<div class="col-xs-4 text-right">
			<input type="text" class="width-100 text-center" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อได้กี่ %</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">คลังซื้อ-ขาย เริ่มต้น</div>
		<div class="col-xs-12">
			<select class="width-100" id="default-warehouse" name="DEFAULT_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดคลังซื้อ - ขาย เริ่มต้น กรณีไม่ระบุคลังจะใช้คลังนี้เป็นคลังหลัก</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">
			<span class="form-control left-label">สต็อกขั้นต่ำในโซน Fast move</span>
		</div>
		<div class="col-xs-4 text-right">
			<input type="number" class="width-100 text-center" name="MIN_STOCK"  value="<?php echo $MIN_STOCK; ?>" />
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดจำนวนขั้นต่ำในโซน fast move หากจำนวนคงเหลือในโซนเหลือด่ำกว่าที่กำหนดจะแสดงผลในรายงาน</span>
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">Default package</div>
		<div class="col-xs-12">
			<select class="width-100" name="DEFAULT_PACKAGE" id="default-package">
				<?php echo select_active_package($DEFAULT_PACKAGE); ?>
			</select>
		</div>
		<div class="col-xs-12 padding-top-5">
			<span class="help-block">กำหนดขนาด package เริ่มต้นสำหรับการแพ็คสินค้า</span>
		</div>
		<div class="divider"></div>
	
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('inventoryForm')">SAVE</button>
			<?php endif; ?>
		</div>
	</div><!--/ row -->
</form>
