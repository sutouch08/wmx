<form id="orderForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">รหัสลูกค้าเริ่มต้น</span></div>
		<div class="col-lg-2 col-md-2 col-sm-2 padding-5 first">
			<input type="text" class="width-100 text-center" name="DEFAULT_CUSTOMER" id="default-customer-code" value="<?php echo $DEFAULT_CUSTOMER; ?>" />
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 padding-5 last">
			<input type="text" class="width-100" id="default-customer-name" value="<?php echo customer_name($DEFAULT_CUSTOMER); ?>" readonly />
		</div>
		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">Website Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="WEB_SITE_CHANNELS_CODE" id="web-site-channels-code" >
				<?php echo select_channels($WEB_SITE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">SHOPEE Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="SHOPEE_CHANNELS_CODE" id="shopee-channels-code" >
				<?php echo select_channels($SHOPEE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">TiKTOK Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="TIKTOK_CHANNELS_CODE" id="tiktok-channels-code" >
				<?php echo select_channels($TIKTOK_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4"><span class="form-control left-label">LAZADA Channels</span></div>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="width-100" name="LAZADA_CHANNELS_CODE" id="lazada-channels-code" >
				<?php echo select_channels($LAZADA_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-8">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-100" onClick="updateConfig('orderForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>		
  </div>
</form>
