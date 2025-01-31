<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
	<?php if($this->pm->can_add) : ?>
		<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> &nbsp; Add New</button>
	<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5">
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-5 padding-5">
    <label>Profile Name</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-7 padding-5">
		<label>Menu</label>
		<select class="width-100" id="menu-x" name="menu">
			<option value="all">ทั้งหมด</option>
			<?php $groups = $this->menu->get_active_menu_groups();		?>
			<?php 	if(!empty($groups)) : ?>
			<?php			 foreach($groups as $group) : ?>
			<?php 			if($group->pm == 1) : ?>
			<?php 			$menu_list = $this->menu->get_valid_menus_by_group($group->code); ?>
			<?php 			if(!empty($menu_list)) : ?>
			<?php 			foreach($menu_list as $rs) : ?>
			<?php echo '<option value="'.$rs->code.'" '.is_selected($rs->code, $menu).'>'.$rs->name.'</option>'; ?>
			<?php 			endforeach; ?>
			<?php 			endif; ?>
			<?php 		endif; ?>
			<?php 	endforeach; ?>
			<?php endif;?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Permission</label>
		<select class="form-control input-sm" name="permission" id="permission">
			<option value="all">ทั้งหมด</option>
			<option value="view" <?php echo is_selected('view', $permission); ?>>ดู</option>
			<option value="add" <?php echo is_selected('add', $permission); ?>>เพิ่ม</option>
			<option value="edit" <?php echo is_selected('edit', $permission); ?>>แก้ไข</option>
			<option value="delete" <?php echo is_selected('delete', $permission); ?>>ลบ</option>
			<option value="approve" <?php echo is_selected('approve', $permission); ?>>อนุมัติ</option>
		</select>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">

</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered" style="min-width:800px;">
			<thead>
				<tr>
					<th class="fix-width-120">&nbsp;</th>
					<th class="fix-width-50 text-center">#</th>
					<th class="min-width-200">Profile Name</th>
					<th class="fix-width-100 text-center">Members</th>
					<th class="fix-width-150 text-center">Last Update</th>
					<th class="fix-width-150 text-center">Update By</th>
				</tr>
			</thead>
			<tbody>
			<?php if( ! empty($list)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<?php $member = $this->profile_model->count_members($rs->id); ?>
					<tr>
						<td>
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							<?php if(($this->pm->can_add OR $this->pm->can_edit) && $rs->id > 0) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="edit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($this->pm->can_delete && $rs->id > 0) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="confirmDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
							<?php endif; ?>
						</td>
						<td class="text-center no"><?php echo $no; ?></td>
						<td class=""><?php echo $rs->name; ?></td>
						<td class="text-center"><?php echo number($member); ?></td>
						<td class="text-center"><?php echo thai_date($rs->date_update, TRUE); ?></td>
						<td class="text-center"><?php echo $rs->update_by; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/users/permission.js?v=<?php echo date('Ymd'); ?>"></script>

<script>
	$('#menu-x').select2();
</script>

<?php $this->load->view('include/footer'); ?>
