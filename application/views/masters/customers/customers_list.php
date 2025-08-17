<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>  
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Code/Name</label>
			<input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Group</label>
			<select class="form-control input-sm filter" name="group" id="customer_group">
				<option value="all">All</option>
				<?php echo select_customer_group($group); ?>
				<option value="NULL" <?php echo is_selected('NULL', $group); ?>>No value</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Grade</label>
			<select class="form-control input-sm filter" name="class" id="customer_class">
				<option value="all">All</option>
				<?php echo select_customer_class($class); ?>
				<option value="NULL" <?php echo is_selected('NULL', $class); ?>>No value</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Category</label>
			<select class="form-control input-sm filter" name="kind" id="customer_kind">
				<option value="all">All</option>
				<?php echo select_customer_kind($kind); ?>
				<option value="NULL" <?php echo is_selected('NULL', $kind); ?>>No value</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Type</label>
			<select class="form-control input-sm filter" name="type" id="customer_type">
				<option value="all">All</option>
				<?php echo select_customer_type($type); ?>
				<option value="NULL" <?php echo is_selected('NULL', $type); ?>>No value</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Area</label>
			<select class="form-control input-sm filter" name="area" id="customer_area">
				<option value="all">All</option>
				<?php echo select_customer_area($area); ?>
				<option value="NULL" <?php echo is_selected('NULL', $area); ?>>No value</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status" id="status">
				<option value="all">All</option>
				<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
		<input type="hidden" name="search" value="1" />
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1000px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 text-center"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-60 middle text-center">Status</th>
					<th class="fix-width-100 middle">Code</th>
					<th class="min-width-200 middle">Name</th>
					<th class="fix-width-100 middle">Group</th>
					<th class="fix-width-100 middle">Grade</th>
					<th class="fix-width-100 middle">Category</th>
					<th class="fix-width-100 middle">Type</th>
					<th class="fix-width-100 middle">Area</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-11">
						<td class="middle">
							<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($this->_SuperAdmin) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->class; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle"><?php echo $rs->area; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>

<?php $this->load->view('include/footer'); ?>
