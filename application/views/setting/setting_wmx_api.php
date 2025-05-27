	<form id="wmxForm" method="post" action="<?php echo $this->home; ?>/update_config">
		<input type="hidden" name="IX_API" value="<?php echo $IX_API; ?>"/>
		<input type="hidden" name="SYNC_IX_STOCK" value="<?php echo $SYNC_IX_STOCK; ?>"/>
		<input type="hidden" name="IX_LOG_JSON" value="<?php echo $IX_LOG_JSON; ?>"/>
		<input type="hidden" name="IX_TEST" value="<?php echo $IX_TEST; ?>"/>
  	<div class="row">
			<div class="col-sm-4">
        <span class="form-control left-label">WMS API</span>
      </div>
			<div class="col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_API" type="checkbox" value="1" <?php echo is_checked($IX_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl" data-lbl="ON&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OFF"></span>
				</label>
			</div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">รหัสคลัง WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-warehouse" name="IX_WAREHOUSE" value="<?php echo $IX_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซน WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-zone" name="IX_ZONE" value="<?php echo $IX_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสคลังรับคืน WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-return-warehouse" name="IX_RETURN_WAREHOUSE" value="<?php echo $IX_RETURN_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">รหัสโซนรับคืน WMS</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-large" id="ix-return-zone" name="IX_RETURN_ZONE" value="<?php echo $IX_RETURN_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">SYNC API STOCK</span>
      </div>
			<div class="col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SYNC_IX_STOCK" type="checkbox" value="1" <?php echo is_checked($SYNC_IX_STOCK , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl" data-lbl="ON&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OFF"></span>
				</label>
			</div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Logs Json</span>
      </div>
			<div class="col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($IX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl" data-lbl="ON&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OFF"></span>
				</label>
			</div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Test Mode</span>
      </div>
			<div class="col-sm-8">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_TEST" type="checkbox" value="1" <?php echo is_checked($IX_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl" data-lbl="ON&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OFF"></span>
				</label>
			</div>
      <div class="divider-hidden"></div>

			<div class="col-sm-8 col-sm-offset-4">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('wmxForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
