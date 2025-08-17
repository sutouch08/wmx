<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">WRX API Setting</span>
</div>
<form id="wrxForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">WRX API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API" type="checkbox" value="1" <?php echo is_checked($WRX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API" id="wrx-api" value="<?php echo $WRX_API; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Endpoint</div>
		<div class="col-xs-12 padding-top-5">
			<input type="text" class="width-100" name="WRX_API_HOST"  value="<?php echo $WRX_API_HOST; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-12">API Credential</div>
		<div class="col-xs-12 padding-top-5">
			<textarea class="width-100" rows="6" name="WRX_API_CREDENTIAL"><?php echo $WRX_API_CREDENTIAL; ?></textarea>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Shopee API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_SHOPEE_API" type="checkbox" value="1" <?php echo is_checked($WRX_SHOPEE_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_SHOPEE_API" value="<?php echo $WRX_SHOPEE_API; ?>"/>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Titok API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_TIKTOK_API" type="checkbox" value="1" <?php echo is_checked($WRX_TIKTOK_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_TIKTOK_API" value="<?php echo $WRX_TIKTOK_API; ?>"/>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Lazada API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LAZADA_API" type="checkbox" value="1" <?php echo is_checked($WRX_LAZADA_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LAZADA_API" value="<?php echo $WRX_LAZADA_API; ?>"/>
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Outbound Interface (INT21)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_OB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_OB_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_OB_INTERFACE" value="<?php echo $WRX_OB_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Outbound api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="WRX_OB_URL"  value="<?php echo $WRX_OB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">GRPO Interface (ADD24)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_IB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_IB_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_IB_INTERFACE" value="<?php echo $WRX_IB_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">GRPO api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="WRX_IB_URL"  value="<?php echo $WRX_IB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Return Interface (ADD91)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_RETURN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_RETURN_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_RETURN_INTERFACE" value="<?php echo $WRX_RETURN_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Return api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="WRX_RETURN_URL"  value="<?php echo $WRX_RETURN_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Adjust Interface (INT17)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_ADJ_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_ADJ_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_ADJ_INTERFACE" value="<?php echo $WRX_ADJ_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Adjust api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="WRX_ADJ_URL"  value="<?php echo $WRX_ADJ_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-xs-8 padding-top-5">Adjust Sale Channel</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="WRX_ADJ_CHANNEL"  value="<?php echo $WRX_ADJ_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Logs Json</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($WRX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_LOG_JSON" id="wrx-log-json" value="<?php echo $WRX_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Test Mode</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API_TEST" type="checkbox" value="1" <?php echo is_checked($WRX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="WRX_API_TEST" id="wrx-api-test" value="<?php echo $WRX_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('wrxForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
