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
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">User name</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" id="uname"	class="width-100" value="<?php echo $user->uname; ?>" disabled />
			<input type="hidden" id="id" value="<?php echo $user->id; ?>" />
		</div>
		<div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="uname-error">&nbsp;</div>
	</div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Display name</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" id="dname" class="width-100 e" maxlength="100" value="<?php echo $user->name; ?>" />
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="dname-error">&nbsp;</div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Permission Profile</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<select class="width-100 e" name="profile" id="profile">
				<option value="">Please, select profile</option>
				<?php echo select_profile($user->id_profile); ?>
			</select>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="profile-error">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="active" value="1" <?php echo is_checked($user->active, '1'); ?> />
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red"></div>
  </div>

  <div class="form-group">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
			<button type="button" class="btn btn-white btn-success btn-100" onclick="update()">Save</button>
    </div>
  </div>
</div>

<script>
	$('#profile').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/users/users.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
