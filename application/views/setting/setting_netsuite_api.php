<form id="nsForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Netsuite API</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_API" type="checkbox" value="1" <?php echo is_checked($NS_API, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_API" value="<?php echo $NS_API; ?>" />
		</div>
		<div class="divider-hidden"></div>
		
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Netsuite Main Company</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="NS_MAIN_COMPANY" value="<?php echo $NS_MAIN_COMPANY; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Netsuite Consign Company</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm input-xxlarge" name="NS_CONSIGN_COMPANY" value="<?php echo $NS_CONSIGN_COMPANY; ?>" />
		</div>
		<div class="divider"></div>
		
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Outbound Interface (INT21)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_OB_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_OB_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_OB_INTERFACE" value="<?php echo $NS_OB_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Outbound api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_OB_URL" value="<?php echo $NS_OB_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">GRPO Interface (ADD24)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_GRPO_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_GRPO_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_GRPO_INTERFACE" value="<?php echo $NS_GRPO_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">GRPO api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_GRPO_URL" value="<?php echo $NS_GRPO_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Goods Receive Interface (INT16)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_GR_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_GR_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_GR_INTERFACE" value="<?php echo $NS_GR_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Goods Receive api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_GR_URL" value="<?php echo $NS_GR_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Return Interface (ADD91)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_RETURN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_RETURN_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_RETURN_INTERFACE" value="<?php echo $NS_RETURN_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Return api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_RETURN_URL" value="<?php echo $NS_RETURN_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Transfer Interface (ADD40)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_TR_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_TR_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_TR_INTERFACE" value="<?php echo $NS_TR_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Transfer api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_TR_URL" value="<?php echo $NS_TR_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Adjust Interface (INT17)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_ADJ_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_ADJ_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_ADJ_INTERFACE" value="<?php echo $NS_ADJ_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Adjust api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_ADJ_URL" value="<?php echo $NS_ADJ_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Adjust Sale Channel</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_ADJ_CHANNEL" value="<?php echo $NS_ADJ_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consignment Interface (INT17.1)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_CONSIGNMENT_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_CONSIGNMENT_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_CONSIGNMENT_INTERFACE" value="<?php echo $NS_CONSIGNMENT_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consignment api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_CONSIGNMENT_URL" value="<?php echo $NS_CONSIGNMENT_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consignment Sale Channel</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_CONSIGNMENT_CHANNEL" value="<?php echo $NS_CONSIGNMENT_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consign Interface (INT23)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="NS_CONSIGN_INTERFACE" type="checkbox" value="1" <?php echo is_checked($NS_CONSIGN_INTERFACE, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_CONSIGN_INTERFACE" value="<?php echo $NS_CONSIGN_INTERFACE; ?>" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consign api endpoint</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_CONSIGN_URL" value="<?php echo $NS_CONSIGN_URL; ?>" />
		</div>
		<div class="divider-hidden"></div>
		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Consign Sale Channel</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_CONSIGN_CHANNEL" value="<?php echo $NS_CONSIGN_CHANNEL; ?>" />
		</div>
		<div class="divider"></div>


		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Check Stock Endpoint (INT03)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_CHECK_STOCK_URL" value="<?php echo $NS_CHECK_STOCK_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Check Stock Endpoint (ADD16)</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<input type="text" class="form-control input-sm" name="NS_GET_STOCK_URL" value="<?php echo $NS_GET_STOCK_URL; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Logs Json</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input data-name="NS_LOG_JSON" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($NS_LOG_JSON, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_LOG_JSON" value="<?php echo $NS_LOG_JSON; ?>" />
		</div>
		<div class="divider"></div>

		<div class="col-lg-4 col-md-4 col-sm-4">
			<span class="form-control left-label">Test Mode</span>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-8">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input data-name="NS_API_TEST" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($NS_API_TEST, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
			</label>
			<input type="hidden" name="NS_API_TEST" value="<?php echo $NS_API_TEST; ?>" />
		</div>
		<div class="divider"></div>
		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-sm-8 col-sm-offset-4">
			<?php if ($this->pm->can_add or $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('nsForm')">
					<i class="fa fa-save"></i> บันทึก
				</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>