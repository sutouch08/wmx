<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">
	<div class="form-group margin-top-30">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Warehouse</label>
    <div class="col-lg-4 col-md-6 col-sm-5 col-xs-12">
      <select class="width-100 e" id="warehouse">
				<option value="">Please Select</option>
				<?php echo select_warehouse(); ?>
			</select>
		</div>
		<div class="help-block col-lg-4 col-md-4 col-sm-4 col-xs-12 col-sm-reset inline red" id="warehouse-error">&nbsp;</div>
  </div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Rows</label>
    <div class="col-lg-1-harf col-md-2 col-sm-2">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 text-center">
				<h4 class="title-xs">Rows</h4>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-0 margin-bottom-15 e" id="row" style="height:250px; overflow:auto;">
				<table class="table table-striped tableFixHead">
					<thead>
						<tr>
							<th class="width-100 fix-header">
								<label>
									<input type="checkbox" class="ace" id="chk-all-row" onchange="checkAllRow()">
									<span class="lbl">&nbsp; All</span>
								</label>
							</th>
						</tr>
					</thead>
					<tbody>
			<?php $rows = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');	?>
			<?php foreach($rows AS $row) : ?>
						<tr>
							<td class="width-100" style="padding:8px !important;">
								<label>
									<input type="checkbox" class="ace chk-row" value="<?php echo $row; ?>" />
									<span class="lbl">&nbsp; <?php echo $row; ?></span>
								</label>
							</td>
						</tr>
			<?php endforeach; ?>
					</tbody>
				</table>
			</div>
    </div>


		<div class="col-lg-2 col-md-2 col-sm-3">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 text-center">
				<h4 class="title-xs">Column</h4>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
				<div class="input-group width-100">
					<span class="input-group-addon fix-width-60">Digit</span>
					<select class="form-control input-sm" id="column-digit">
						<option value="2">2 หลัก</option>
						<option value="3">3 หลัก</option>						
					</select>
				</div>
			</div>
			<div class="divider-hidden"></div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
				<div class="input-group width-100">
					<span class="input-group-addon fix-width-60">Start</span>
					<input type="number" class="width-100 text-center e" id="column-start" value="" />
				</div>
			</div>
			<div class="divider-hidden"></div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0  margin-bottom-15">
				<div class="input-group width-100">
					<span class="input-group-addon fix-width-60">End</span>
					<input type="number" class="width-100 text-center e" id="column-end" value="" />
				</div>
			</div>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 text-center">
				<h4 class="title-xs">Loc</h4>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 padding-0 e" id="loc" style="height:250px; overflow:auto;">
				<table class="table table-striped tableFixHead">
					<thead>
						<tr>
							<th class="width-100 fix-header">
								<label>
									<input type="checkbox" class="ace" id="chk-all-loc" onchange="checkAllLoc()">
									<span class="lbl">&nbsp; All</span>
								</label>
							</th>
						</tr>
					</thead>
					<tbody>
			<?php $locs = array('A', 'B', 'C', 'D', 'E', 'F');	?>
			<?php foreach($locs AS $loc) : ?>
						<tr>
							<td class="width-100" style="padding:8px !important;">
								<label>
									<input type="checkbox" class="ace chk-loc" value="<?php echo $loc; ?>" />
									<span class="lbl">&nbsp; <?php echo $loc; ?></span>
								</label>
							</td>
						</tr>
			<?php endforeach; ?>
					</tbody>
				</table>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right hidden-xs">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="freeze" value="1" />
				<span class="lbl">&nbsp;&nbsp; Freeze</span>
			</label>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right hidden-xs">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="active" value="1" checked/>
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
  </div>

	<div class="divider-hidden"></div>

	<?php if($this->pm->can_add) : ?>
	  <div class="form-group">
	    <div class="col-lg-8 col-md-9 col-sm-7-harf col-xs-8 text-right">
				<button type="button" class="btn btn-white btn-success btn-100" onclick="genZone()">Generate</button>
	    </div>
	  </div>
	<?php endif; ?>
</div>
<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
