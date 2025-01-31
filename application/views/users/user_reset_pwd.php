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
		<div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error">&nbsp;</div>
	</div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Display name</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" id="dname" class="width-100 e" value="<?php echo $user->name; ?>" disabled/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="dname-error">&nbsp;</div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">New password</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="password" name="pwd" id="pwd" class="width-100 e" style="padding-right:30px;" />
			<i class="fa fa-eye fa-lg show-eye" style="position:absolute; top:9px; right:20px;" onclick="showPwd()"></i/>
			<i class="fa fa-eye-slash fa-lg hide-eye hide" style="position:absolute; top:9px; right:20px;" onclick="hidePwd()"></i/>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="pwd-error" style="position:absolute;">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Confirm password</label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="password" name="cm-pwd" id="cm-pwd" class="width-100 e" style="padding-right:30px;"/>
			<i class="fa fa-eye fa-lg show-eye" style="position:absolute; top:9px; right:20px;" onclick="showPwd()"></i/>
			<i class="fa fa-eye-slash fa-lg hide-eye hide" style="position:absolute; top:9px; right:20px;" onclick="hidePwd()"></i/>
    </div>
    <div class="help-block col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sm-reset inline red" id="cm-pwd-error" style="position:absolute;">&nbsp;</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="margin-top:7px; padding-left:10px;">
				<input type="checkbox" class="ace" id="force-reset" value="1" checked />
				<span class="lbl">&nbsp;&nbsp; Force user to change password</span>
			</label>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline"></div>
  </div>

  <div class="form-group">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
			<button type="button" class="btn btn-white btn-success btn-100" onclick="changePassword()">Save</button>
    </div>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/users/users.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
