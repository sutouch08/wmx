<form id="orderForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">รหัสลูกค้าเริ่มต้น</span></div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<input type="text" class="form-control input-sm input-small text-center" name="DEFAULT_CUSTOMER" required value="<?php echo $DEFAULT_CUSTOMER; ?>" />
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">Website Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="WEB_SITE_CHANNELS_CODE" id="web-site-channels-code" >
				<?php echo select_channels($WEB_SITE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">SHOPEE Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="SHOPEE_CHANNELS_CODE" id="shopee-channels-code" >
				<?php echo select_channels($SHOPEE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">TiKTOK Channels</span></div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="TIKTOK_CHANNELS_CODE" id="tiktok-channels-code" >
				<?php echo select_channels($TIKTOK_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><span class="form-control left-label">LAZADA Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="LAZADA_CHANNELS_CODE" id="lazada-channels-code" >
				<?php echo select_channels($LAZADA_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider-hidden"></div>


    <div class="col-sm-9 col-sm-offset-3">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('orderForm')"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>
  </div>
</form>
