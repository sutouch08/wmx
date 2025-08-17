<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Orders setting</span>
</div>
<form id="orderForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">		
		<div class="col-xs-6 padding-top-5">Website Channels</div>
		<div class="col-xs-6 text-right">
			<select class="width-100" name="WEB_SITE_CHANNELS_CODE" id="web-site-channels-code" >
				<?php echo select_channels($WEB_SITE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">SHOPEE Channels</div>
		<div class="col-xs-6 text-right">
			<select class="width-100" name="SHOPEE_CHANNELS_CODE" id="shopee-channels-code" >
				<?php echo select_channels($SHOPEE_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">TiKTOK Channels</div>
	<div class="col-xs-6 text-right">
			<select class="width-100" name="TIKTOK_CHANNELS_CODE" id="tiktok-channels-code" >
				<?php echo select_channels($TIKTOK_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="col-xs-6 padding-top-5">LAZADA Channels</div>
		<div class="col-xs-6 text-right">
			<select class="width-100" name="LAZADA_CHANNELS_CODE" id="lazada-channels-code" >
				<?php echo select_channels($LAZADA_CHANNELS_CODE); ?>
			</select>
		</div>
		<div class="divider"></div>

		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
    <div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('orderForm')">SAVE</button>
			<?php endif; ?>
		</div>
  </div>
</form>
