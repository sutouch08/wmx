<div class="nav-title">
	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	<span class="font-size-14 text-left">Netsuite API Setting</span>
</div>
<form id="netsuiteForm"class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-xs-8 padding-top-5">Netsuite API</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_API" type="checkbox" value="1" <?php echo is_checked($NS_API , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_API" id="ns-api" value="<?php echo $NS_API; ?>" />
		</div>
		<div class="divider"></div>
		
		<div class="col-xs-8 padding-top-5">Outbound Interface (INT21)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_OB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_OB_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_OB_INTERFACE" value="<?php echo $NS_OB_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Outbound api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_OB_URL"  value="<?php echo $NS_OB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">GRPO Interface (ADD24)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_GR_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_GR_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_GR_INTERFACE" value="<?php echo $NS_GR_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">GRPO api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_GR_URL"  value="<?php echo $NS_GR_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Transfer Interface (ADD40)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_TR_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_TR_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_TR_INTERFACE" value="<?php echo $NS_TR_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-12 padding-top-5">Transfer api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_TR_URL"  value="<?php echo $NS_TR_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Return Interface (ADD91)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_RETURN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_RETURN_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_RETURN_INTERFACE" value="<?php echo $NS_RETURN_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Return api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_RETURN_URL"  value="<?php echo $NS_RETURN_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Adjust Interface (INT17)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_ADJ_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_ADJ_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_ADJ_INTERFACE" value="<?php echo $NS_ADJ_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Adjust api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_ADJ_URL"  value="<?php echo $NS_ADJ_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-xs-8 padding-top-5">Adjust Sale Channel</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_ADJ_CHANNEL"  value="<?php echo $NS_ADJ_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Consignment Interface (INT17.1)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_CONSIGNMENT_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_CONSIGNMENT_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_CONSIGNMENT_INTERFACE" value="<?php echo $NS_CONSIGNMENT_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Adjust api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_CONSIGNMENT_URL"  value="<?php echo $NS_CONSIGNMENT_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-xs-8 padding-top-5">Adjust Sale Channel</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_CONSIGNMENT_CHANNEL"  value="<?php echo $NS_CONSIGNMENT_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Consign Interface (INT23)</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_CONSIGN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_CONSIGN_INTERFACE , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_CONSIGN_INTERFACE" value="<?php echo $NS_CONSIGN_INTERFACE; ?>"/>
		</div>
		<div class="col-xs-8 padding-top-5">Adjust api endpoint</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_CONSIGN_URL"  value="<?php echo $NS_CONSIGN_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-xs-8 padding-top-5">Adjust Sale Channel</div>
		<div class="col-xs-12 text-right">
			<input type="text" class="form-control input-sm" name="NS_CONSIGN_CHANNEL"  value="<?php echo $NS_CONSIGN_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>


		<div class="col-xs-8 padding-top-5">Logs Json</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($NS_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_LOG_JSON" id="wrx-log-json" value="<?php echo $NS_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-xs-8 padding-top-5">Test Mode</div>
		<div class="col-xs-4 text-right">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_API_TEST" type="checkbox" value="1" <?php echo is_checked($NS_API_TEST , '1'); ?> onchange="toggleOption($(this))"/>
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_API_TEST" id="wrx-api-test" value="<?php echo $NS_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('netsuiteForm')">SAVE</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
