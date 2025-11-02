<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr/>
<div class="form-horizontal">
	<div class="form-group margin-top-30">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสคลัง</label>
    <div class="col-lg-lg-1-harf col-md-2 col-sm-3 col-xs-12">
      <input type="text" class="form-control input-sm e" id="code" value="<?php echo $ds->code; ?>" readonly />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อคลัง</label>
    <div class="col-lg-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm e" id="name" maxlength="100" value="<?php echo $ds->name; ?>" readonly/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ประเภทคลัง</label>
 	 <div class="col-lg-lg-1-harf col-md-2 col-sm-3 col-xs-12">
		 <input type="text" class="form-control input-sm" value="<?php echo warehouse_role_name($ds->role); ?>" readonly />
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้ขาย</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
			 <input name="sell" class="ace ace-switch ace-switch-7" type="checkbox" id="sell" value="1" <?php echo is_checked($ds->sell,1); ?> disabled/>
			 <span class="lbl"></span>
		 </label>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้จัด</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
 			<input name="prepare" class="ace ace-switch ace-switch-7" type="checkbox" id="prepare" value="1" <?php echo is_checked($ds->prepare,1); ?> disabled/>
 			<span class="lbl"></span>
 		</label>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อนุญาติให้ยืม</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
 			<input name="lend" class="ace ace-switch ace-switch-7" type="checkbox" id="lend" value="1" <?php echo is_checked($ds->lend,1); ?> disabled/>
 			<span class="lbl"></span>
 		</label>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		 <label style="padding-top:5px;">
 			<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($ds->active,1); ?>disabled />
 			<span class="lbl"></span>
 		</label>
 	 </div>
  </div>
	<div class="divider-hidden"></div>
	<div class="divider"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
		<label class="col-lg- col-md-3 col-sm-1-harf col-xs-12 control-label no-padding-right">&nbsp;</label>
		<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
			<table class="table table-striped border-1">
				<thead>
					<tr class="font-size-11">
						<th class="fix-width-40 text-center">No.</th>
						<th class="fix-width-100">รหัสลูกค้า</th>
						<th class="fix-width-250">ชิ้อลูกค้า</th>
					</tr>
				</thead>
				<tbody id="cust-table">
	<?php if( ! empty($customers)) : ?>
		<?php $no = 1; ?>
		<?php foreach($customers as $rs) : ?>
					<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
					</tr>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php else : ?>
					<tr class="font-size-11">
						<td colspan="3" class="text-center">--- No customer ---</td>
					</tr>
	<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
