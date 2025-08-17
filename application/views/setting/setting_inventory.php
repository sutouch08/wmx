<form id="inventoryForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">สต็อกติดลบได้</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_UNDER_ZERO" type="checkbox" value="1" <?php echo is_checked($ALLOW_UNDER_ZERO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
			<span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">รับสินค้าเกินใบสั่งซื้อ</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="ALLOW_RECEIVE_OVER_PO" type="checkbox" value="1" <?php echo is_checked($ALLOW_RECEIVE_OVER_PO , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="ALLOW_RECEIVE_OVER_PO" id="allow-receive-over-po" value="<?php echo $ALLOW_RECEIVE_OVER_PO; ?>" />
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อหรือไม่</span>
		</div>
		<div class="divider"></div>


		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">รับสินค้าเกินไปสั่งซื้อ(%)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-small text-center" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">คลังซื้อ-ขาย เริ่มต้น</span>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-8">
			<select class="width-100" id="default-warehouse" name="DEFAULT_WAREHOUSE" onchange="defaultZoneInit()" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">รหัสคลังสินค้าระหว่างทำ</span>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-8">
			<select class="width-100" id="transform-warehouse" name="TRANSFORM_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_transform_warehouse($TRANSFORM_WAREHOUSE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">รหัสคลังยืมสินค้า</span>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-8">
			<select class="width-100" id="lend-warehouse" name="LEND_WAREHOUSE" required>
				<option value="">เลือกคลัง</option>
				<?php echo select_lend_warehouse($LEND_WAREHOUSE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">สต็อกขั้นต่ำในโซน Fast move</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="number" class="form-control input-sm input-small text-center" name="MIN_STOCK"  value="<?php echo $MIN_STOCK; ?>" />
			<span class="help-block">กำหนดจำนวนขั้นต่ำในโซน fast move หากจำนวนคงเหลือในโซนเหลือด่ำกว่าที่กำหนดจะแสดงผลในรายงาน</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Default package</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<select class="form-control input-xlarge" name="DEFAULT_PACKAGE" id="default-package">
				<?php echo select_active_package($DEFAULT_PACKAGE); ?>
			</select>
			<span class="help-block">กำหนดขนาด package เริ่มต้นสำหรับการแพ็คสินค้า</span>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>



		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('inventoryForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>
	</div><!--/ row -->
</form>
