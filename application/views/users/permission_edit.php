<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>  Back</button>
		<?php if($this->pm->can_edit) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="update()"><i class="fa fa-save"></i>  Save</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr />
<div class="form-horizontal">
	<div class="form-group margin-top-30">
		<label class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 control-label no-padding-right">Profile Name</label>
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-9">
			<input type="text" class="width-100 e" id="name" maxlength="100" value="<?php echo $name; ?>" data-name="<?php echo $name; ?>" />
			<input type="hidden" id="profile-id" value="<?php echo $id; ?>" />
		</div>
		<div class="help-block col-lg-7-harf col-md-5 col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 control-label no-padding-right">Permission</label>
		<div class="col-lg-7 col-md-9 col-sm-10 col-xs-12 table-responsive">
			<table class="table table-striped table-bordered table-hover" style="min-width:560px; margin-bottom:0px;">
				<tbody>
					<?php if( ! empty($menus)) : ?>
						<?php foreach($menus as $groups) : ?>
							<?php 	$g_code = $groups->group_code; ?>
							<tr class="font-size-14" style="background-color:#428bca73;">
								<td class="min-width-200 middle"><?php echo $groups->group_name; ?></td>
								<td class="fix-width-60 middle text-center">
									<label>
										<input id="view-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupViewCheck($(this), '<?php echo $g_code; ?>')" />
										<span class="lbl"><br/>ดู</span>
									</label>
								</td>
								<td class="fix-width-60 middle text-center">
									<label>
									<input id="add-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAddCheck($(this), '<?php echo $g_code; ?>' )" />
									<span class="lbl"><br/>เพิ่ม</span>
									</label>
								</td>
								<td class="fix-width-60 middle text-center">
									<label>
										<input id="edit-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupEditCheck($(this), '<?php echo $g_code; ?>' )" />
										<span class="lbl"><br/>แก้ไข</span>
									</label>
								</td>
								<td class="fix-width-60 middle text-center">
									<label>
										<input id="delete-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupDeleteCheck($(this), '<?php echo $g_code; ?>' )"/>
										<span class="lbl"><br/>ลบ</span>
									</label>
								</td>
								<td class="fix-width-60 middle text-center">
									<label>
										<input id="approve-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupApproveCheck($(this), '<?php echo $g_code; ?>' )" />
										<span class="lbl"><br/>อนุมัติ</span>
									</label>
								</td>
								<td class="fix-width-60 middle text-center">
									<label>
										<input id="all-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAllCheck($(this), '<?php echo $g_code; ?>' )">
										<span class="lbl"><br/>ทั้งหมด</span>
									</label>
								</td>
							</tr>

							<?php if( ! empty($groups->menu)) : ?>
								<?php foreach($groups->menu as $menu) : ?>
									<?php $code = $menu->menu_code; ?>
									<?php $pm = $menu->permission; ?>
									<tr>
										<td class="middle" style="padding-left:20px;"> -
											<?php echo $menu->menu_name; ?>
											<input type="hidden" class="menu-code" value="<?php echo $code; ?>" />
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="view-<?php echo $code; ?>" class="ace view-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_view, 1); ?> />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="add-<?php echo $code; ?>"  class="ace add-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_add, 1); ?> />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="edit-<?php echo $code; ?>" class="ace edit-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_edit, 1); ?> />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="delete-<?php echo $code; ?>" class="ace delete-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_delete, 1); ?> />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="approve-<?php echo $code; ?>" class="ace approve-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_approve, 1); ?> />
												<span class="lbl"></span>
											</label>
										</td>
										<td class="middle text-center">
											<label>
												<input type="checkbox" id="all-<?php echo $code; ?>" class="ace all-<?php echo $g_code; ?>" onchange="checkAll('<?php echo $code; ?>')" />
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

	<div class="divider-hidden"></div>
	<div class="form-group">
		<div class="col-lg-8-harf col-md-11 col-sm-12 col-xs-12 text-right">
			<button type="button" class="btn btn-white btn-success btn-100" onclick="update()"><i class="fa fa-save"></i>&nbsp; Save</button>
		</div>
	</div>


</div>

<script src="<?php echo base_url(); ?>scripts/users/permission.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
