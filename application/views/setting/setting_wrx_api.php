<form id="wrxForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<input type="hidden" name="WRX_API" value="<?php echo $WRX_API; ?>"/>
	<input type="hidden" name="WRX_OB_INTERFACE" value="<?php echo $WRX_OB_INTERFACE; ?>"/>
	<input type="hidden" name="WRX_IB_INTERFACE" value="<?php echo $WRX_IB_INTERFACE; ?>"/>
	<input type="hidden" name="WRX_RETURN_INTERFACE" value="<?php echo $WRX_RETURN_INTERFACE; ?>"/>
	<input type="hidden" name="WRX_LOG_JSON" value="<?php echo $WRX_LOG_JSON; ?>"/>
	<input type="hidden" name="WRX_API_TEST" value="<?php echo $WRX_API_TEST; ?>"/>
	<input type="hidden" name="WRX_SHOPEE_API" value="<?php echo $WRX_SHOPEE_API; ?>"/>
	<input type="hidden" name="WRX_TIKTOK_API" value="<?php echo $WRX_TIKTOK_API; ?>"/>
	<input type="hidden" name="WRX_LAZADA_API" value="<?php echo $WRX_LAZADA_API; ?>"/>
	<div class="row">
		<div class="col-sm-4">
			<span class="form-control left-label">WRX API</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_API" type="checkbox" value="1" <?php echo is_checked($WRX_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Wrx api endpoint</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_API_HOST"  value="<?php echo $WRX_API_HOST; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Wrx api credential</span>
		</div>
		<div class="col-sm-8">
			<textarea class="form-control input-sm" rows="4" name="WRX_API_CREDENTIAL"><?php echo $WRX_API_CREDENTIAL; ?></textarea>
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Shopee API</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_SHOPEE_API" type="checkbox" value="1" <?php echo is_checked($WRX_SHOPEE_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Titok API</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_TIKTOK_API" type="checkbox" value="1" <?php echo is_checked($WRX_TIKTOK_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Lazada API</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_LAZADA_API" type="checkbox" value="1" <?php echo is_checked($WRX_LAZADA_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Outbound Interface (INT21)</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_OB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_OB_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="col-sm-4">
			<span class="form-control left-label">Outbound api endpoint</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_OB_URL"  value="<?php echo $WRX_OB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">GRPO Interface (ADD24)</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_IB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_IB_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="col-sm-4">
			<span class="form-control left-label">GRPO api endpoint</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_IB_URL"  value="<?php echo $WRX_IB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Return Interface (ADD91)</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="WRX_RETURN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($WRX_RETURN_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="col-sm-4">
			<span class="form-control left-label">Return api endpoint</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="WRX_RETURN_URL"  value="<?php echo $WRX_RETURN_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input data-name="WRX_LOG_JSON" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($WRX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input data-name="WRX_API_TEST" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($WRX_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-8 col-sm-offset-4">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('wrxForm')">
				<i class="fa fa-save"></i> บันทึก
			</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
