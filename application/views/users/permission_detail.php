<?php $this->load->view('include/header'); ?>
<style>
	input[type=checkbox].ace:disabled + .lbl::before, input[type=radio].ace:disabled + .lbl::before, input[type=checkbox].ace[disabled] + .lbl::before, input[type=radio].ace[disabled] + .lbl::before, input[type=checkbox].ace.disabled + .lbl::before,
	input[type=radio].ace.disabled + .lbl::before {
		background-color: #FFF !important;
		border-color: #abbac3 !important;
		box-shadow: none !important;
		color: #6fb3e0;
	}
</style>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>  Back</button>
		<?php if($this->pm->can_delete) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="confirmDelete(<?php echo $id; ?>, '<?php echo $name; ?>', 'goBack')"> Delete</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr />
<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 control-label no-padding-right">Profile Name</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-9">
			<input type="text" class="width-100 e" id="name" maxlength="100" value="<?php echo $name; ?>" readonly />
			<input type="hidden" id="profile-id" value="<?php echo $id; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 control-label no-padding-right">Permission</label>
		<div class="col-lg-7 col-md-9 col-sm-10 col-xs-12 table-responsive">
			<table class="table table-striped table-bordered table-hover" style="min-width:500px; margin-bottom:0px;">
				<tbody>
					<?php if( ! empty($menus)) : ?>
						<?php foreach($menus as $groups) : ?>
							<?php 	$g_code = $groups->group_code; ?>
							<tr class="font-size-14" style="background-color:#428bca73;">
								<td class="min-width-200 middle"><?php echo $groups->group_name; ?></td>
								<td class="fix-width-60 middle text-center">ดู</td>
								<td class="fix-width-60 middle text-center">เพิ่ม</td>
								<td class="fix-width-60 middle text-center">แก้ไข</td>
								<td class="fix-width-60 middle text-center">ลบ</td>
								<td class="fix-width-60 middle text-center">อนุมัติ</td>
							</tr>
							<?php if( ! empty($groups->menu)) : ?>
								<?php foreach($groups->menu as $menu) : ?>
									<?php $code = $menu->menu_code; ?>
									<?php $pm = $menu->permission; ?>
									<tr>
										<td class="middle" style="padding-left:20px;"> - <?php echo $menu->menu_name; ?></td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" class="ace" <?php echo is_checked($pm->can_view, 1); ?> disabled />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" class="ace" <?php echo is_checked($pm->can_add, 1); ?> disabled />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" class="ace" <?php echo is_checked($pm->can_edit, 1); ?> disabled />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" class="ace" <?php echo is_checked($pm->can_delete, 1); ?> disabled />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" class="ace" <?php echo is_checked($pm->can_approve, 1); ?> disabled />
												<span class="lbl"></span>
											</label>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/users/permission.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
