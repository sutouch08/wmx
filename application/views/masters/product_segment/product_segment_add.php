<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Code</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 r" onkeyup="validCode(this)" maxlength="20" value="" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="code-error"></div>
  </div>


  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Name</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100 r" value="" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success btn-100" onclick="add()">Add</button>
      </p>
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/product_segment.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
