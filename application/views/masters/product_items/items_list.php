<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Code</label>
			<input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Description</label>
			<input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Barcode</label>
			<input type="text" class="width-100" name="barcode" id="barcode" value="<?php echo $barcode; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Color</label>
			<input type="text" class="width-100" name="color" id="color" value="<?php echo $color; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Size</label>
			<input type="text" class="width-100" name="size" id="size" value="<?php echo $size; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Main Group</label>
			<select class="width-100" name="main_group" id="main-group" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_main_group($main_group); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Sub Group</label>
			<select class="width-100" name="group" id="group" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_group($group); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Segment</label>
			<select class="width-100" name="segment" id="segment" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_segment($segment); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Class</label>
			<select class="width-100" name="class" id="class" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_class($class); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Family</label>
			<select class="width-100" name="family" id="family" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_family($family); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Type</label>
			<select class="width-100" name="type" id="type" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_type($type); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Kind</label>
			<select class="width-100" name="kind" id="kind" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_kind($kind); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Gender</label>
			<select class="width-100" name="gender" id="gender" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_gender($gender); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Sport Type</label>
			<select class="width-100" name="sport_type" id="sport-type" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_sport_type($sport_type); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>collection</label>
			<select class="width-100" name="collection" id="collection" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_collection($collection); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Brand</label>
			<select class="width-100" name="brand" id="brand" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_product_brand($brand); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ปี</label>
			<select class="width-100" name="year" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_years($year); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
			<label>Active</label>
			<select class="width-100" name="active" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $active); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $active); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:400px; overflow:auto;">
		<table class="table table-striped table-bordered table-hover" style="min-width:2580px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 text-center"></th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-200 middle text-center">SKU</th>
					<th class="fix-width-120 middle text-center">Barcode</th>
					<th class="fix-width-150 middle text-center">Model</th>
					<th class="fix-width-60 middle text-center">Color</th>
					<th class="fix-width-60 middle text-center">Size</th>
					<th class="fix-width-80 middle text-center">Price</th>
					<th class="fix-width-150 middle text-center">Main Group</th>
					<th class="fix-width-150 middle text-center">Sub Group</th>
					<th class="fix-width-150 middle text-center">Segment</th>
					<th class="fix-width-150 middle text-center">Class</th>
					<th class="fix-width-150 middle text-center">Family</th>
					<th class="fix-width-150 middle text-center">Type</th>
					<th class="fix-width-150 middle text-center">Kind</th>
					<th class="fix-width-150 middle text-center">Gender</th>
					<th class="fix-width-150 middle text-center">Sport Type</th>
					<th class="fix-width-150 middle text-center">Club/Collection</th>
					<th class="fix-width-150 middle text-center">Brand</th>
					<th class="fix-width-80 middle text-center">Year</th>
					<th class="fix-width-40 middle text-center">Active</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $no; ?>" class="font-size-11">
						<td class="middle">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
						<?php if($this->_SuperAdmin) : ?>
							<button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>', <?php echo $no; ?>)"><i class="fa fa-trash"></i></button>
						<?php endif; ?>						
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->barcode; ?></td>
						<td class="middle"><?php echo $rs->model_code; ?></td>
						<td class="middle text-center"><?php echo $rs->color_code; ?></td>
						<td class="middle text-center"><?php echo $rs->size_code; ?></td>
						<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
						<td class="middle"><?php echo empty($rs->main_group_name) ? $rs->main_group_code : $rs->main_group_name; ?></td>
						<td class="middle"><?php echo empty($rs->group_name) ? $rs->group_code : $rs->group_name; ?></td>
						<td class="middle"><?php echo empty($rs->segment_name) ? $rs->segment_code : $rs->segment_name; ?></td>
						<td class="middle"><?php echo empty($rs->class_name) ? $rs->class_code : $rs->class_name; ?></td>
						<td class="middle"><?php echo empty($rs->family_name) ? $rs->family_code : $rs->family_name; ?></td>
						<td class="middle"><?php echo empty($rs->type_name) ? $rs->type_code : $rs->type_name; ?></td>
						<td class="middle"><?php echo empty($rs->kind_name) ? $rs->kind_code : $rs->kind_name; ?></td>
						<td class="middle"><?php echo empty($rs->gender_name) ? $rs->gender_code : $rs->gender_name; ?></td>
						<td class="middle"><?php echo empty($rs->sport_type_name) ? $rs->sport_type_code : $rs->sport_type_name; ?></td>
						<td class="middle"><?php echo empty($rs->collection_name) ? $rs->collection_code : $rs->collection_name; ?></td>
						<td class="middle"><?php echo empty($rs->brand_name) ? $rs->brand_code : $rs->brand_name; ?></td>
						<td class="middle text-center"><?php echo $rs->year; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

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
</script>

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
