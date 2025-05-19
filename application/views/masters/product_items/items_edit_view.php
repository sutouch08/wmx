<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<div class="row">
	<div class="form-horizontal">
		<div class="row">
			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Code</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="code" value="<?php echo $code; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Description</label>
				<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
					<input type="text" name="name" id="name" class="width-100 r" value="<?php echo $name; ?>"  />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Model</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="model" id="model" class="width-100 r" value="<?php echo $model_code; ?>"  />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Color</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="color" id="color" class="width-100 r" value="<?php echo $color_code; ?>"  />
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Size</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="size" id="size" class="width-100 r" value="<?php echo $size_code; ?>"  />
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Barcode</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="barcode" id="barcode" class="width-100" value="<?php echo $barcode; ?>" />
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Cost</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="number" step="any" name="cost" id="cost" class="width-100 r" value="<?php echo $cost; ?>"  />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Price</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="number" step="any" name="price" id="price" class="width-100 r" value="<?php echo $price; ?>"  />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Unit</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select class="width-100 r" name="unit_code" id="unit-code" required>
						<option value="">โปรดเลือก</option>
						<?php echo select_unit($unit_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Brand</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="brand_code" id="brand" class="width-100 r">
						<option value="">โปรดเลือก</option>
						<?php echo select_product_brand($brand_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Main Group</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="main_group_code" id="main-group" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_main_group($main_group_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sub Group</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="group_code" id="group" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_group($group_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Segment</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="segment" id="segment" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_segment($segment_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Class</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="class" id="class" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_class($class_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Family</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="family" id="family" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_family($family_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Type</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="type" id="type" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_type($type_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Kind</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="kind" id="kind" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_kind($kind_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Gender</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="gender" id="gender" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_gender($gender_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sport type</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="sport_type" id="sport-type" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_sport_type($sport_type_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Club/Collection</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="collection" id="collection" class="width-100 r" >
						<option value="">โปรดเลือก</option>
						<?php echo select_product_collection($collection_code); ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ปี</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<select name="year" id="year" class="width-100 r">
						<option value="">โปรดเลือก</option>
						<?php echo select_years($year); ?>
					</select>
				</div>
				<div class="help-block col-xs-12 col-sm-reset inline red e" id="year-error"></div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">API Rate</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="api-rate" value="<?php echo $api_rate; ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">API</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="is_api" class="ace ace-switch ace-switch-7" type="checkbox" id="is_api" value="1" <?php echo is_checked($is_api,1); ?>/>
						<span class="lbl"></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Inventory item</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" id="count_stock" value="1" <?php echo is_checked($count_stock,1); ?> />
						<span class="lbl"></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Active</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($active,1); ?> />
						<span class="lbl"></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label not-show">บันทึก</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
				</div>
			</div>

			<input type="hidden" id="id" value="<?php echo $id; ?>"/>
		</div>
	</div>
</div><!--/ row  -->

<script>
	$('#main-group').select2();
	$('#group').select2();
	$('#segment').select2();
	$('#class').select2();
	$('#family').select2();
	$('#type').select2();
	$('#kind').select2();
	$('#gender').select2();
	$('#sport-type').select2();
	$('#collection').select2();
	$('#brand').select2();
	$('#year').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
