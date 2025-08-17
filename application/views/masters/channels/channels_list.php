<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Code/Name</label>
    <input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Online</label>
		<select class="width-100 filter" name="is_online">
			<option value="all">All</option>
			<option value="1" <?php echo is_selected('1', $is_online); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $is_online); ?>>No</option>
		</select>
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:1110px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">Code</th>
					<th class="fix-width-200 middle">Name</th>
					<th class="fix-width-60 middle text-center">Online</th>
					<th class="fix-width-60 middle text-center">Position</th>
					<th class="fix-width-150 middle">Created By</th>
					<th class="fix-width-100 middle">Created date</th>
					<th class="fix-width-150 middle">Updated By</th>
					<th class="min-width-150 middle">Last Update</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr class="font-size-11">
						<td class="">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center">
						<?php if($this->pm->can_edit) : ?>
							<input type="hidden" id="online-<?php echo $rs->code; ?>" value="<?php echo $rs->is_online; ?>" />
								<a href="javascript:void(0)" id="online-label-<?php echo $rs->code; ?>" onclick="toggleOnline('<?php echo $rs->code; ?>')">
									<?php if($rs->is_online) : ?>
									<i class="fa fa-check green"></i>
									<?php else : ?>
									<i class="fa fa-times"></i>
									<?php endif; ?>
								</a>
						<?php else : ?>
							<?php if($rs->is_online) : ?>
							<i class="fa fa-check green"></i>
							<?php else : ?>
							<i class="fa fa-times"></i>
							<?php endif; ?>
						<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $rs->position; ?></td>
						<td class="middle"><?php echo $rs->user; ?></td>
						<td class="middle"><?php echo thai_date($rs->date_add, FALSE); ?></td>
						<td class="middle"><?php echo $rs->update_user; ?></td>
						<td class="middle"><?php echo empty($rs->date_upd) ? "-" : thai_date($rs->date_upd, TRUE); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/channels.js"></script>

<?php $this->load->view('include/footer'); ?>
