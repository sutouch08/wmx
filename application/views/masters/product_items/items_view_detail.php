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
					<input type="text" class="width-100" value="<?php echo $code; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Description</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="name" id="name" class="width-100 r" value="<?php echo $name; ?>" disabled />
				</div>
				<div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Model</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="style" id="style" class="width-100 r" value="<?php echo $model_code; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Color</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="color" id="color" class="width-100 r" value="<?php echo $color_code; ?>" disabled />
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Size</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="size" id="size" class="width-100 r" value="<?php echo $size_code; ?>" disabled />
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Barcode</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" name="barcode" id="barcode" class="width-100" value="<?php echo $barcode; ?>" disabled/>
				</div>
			</div>


			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Cost</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="number" step="any" name="cost" id="cost" class="width-100 r" value="<?php echo $cost; ?>"  disabled/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Price</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="number" step="any" name="price" id="price" class="width-100 r" value="<?php echo $price; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Unit</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="unit-code" value="<?php echo $unit_code; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Brand</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="brand-code" value="<?php echo empty($brand_code) ? NULL : $brand_code . " : ".$brand_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Main Group</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="main-group-code" value="<?php echo empty($main_group_code) ? NULL : $main_group_code . " : ".$main_group_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Group</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="group-code" value="<?php echo empty($group_code) ? NULL : $group_code . " : ".$group_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Segment</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="segment-code" value="<?php echo empty($segment_code) ? NULL : $segment_code . " : ".$segment_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Class</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="class-code" value="<?php echo empty($class_code) ? NULL : $class_code . " : ".$class_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Family</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="family-code" value="<?php echo empty($family_code) ? NULL : $family_code . " : ".$family_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Type</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="type-code" value="<?php echo empty($type_code) ? NULL : $type_code . " : ".$type_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Kind</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="kind-code" value="<?php echo empty($kind_code) ? NULL : $kind_code . " : ".$kind_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Gender</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="gender-code" value="<?php echo empty($gender_code) ? NULL : $gender_code . " : ".$gender_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Sport type</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="sport-type-code" value="<?php echo empty($sport_type_code) ? NULL : $sport_type_code . " : ".$sport_type_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Club/Collection</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="collection-code" value="<?php echo empty($collection_code) ? NULL : $collection_code . " : ".$collection_name; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Year</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="year" value="<?php echo empty($year) ? NULL : $year; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">API Rate</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<input type="text" class="width-100" id="api-rate" value="<?php echo $api_rate; ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">API</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="is_api" class="ace ace-switch ace-switch-7" type="checkbox" id="is_api" value="1" <?php echo is_checked($is_api,1); ?> disabled/>
						<span class="lbl"></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Inventory Item</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" id="count_stock" value="1" <?php echo is_checked($count_stock,1); ?> disabled/>
						<span class="lbl"></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Actinve</label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<label style="padding-top:5px;">
						<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($active,1); ?> disabled/>
						<span class="lbl"></span>
					</label>
				</div>
			</div>
		</div>
	</div>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
