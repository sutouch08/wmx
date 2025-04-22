<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="customer_name" id="customer_name" class="width-100" required />
			<input type="hidden" name="customer_code" id="customer_code" value="">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งหลัก</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="main_sender" id="main_sender" class="width-100" required />
			<input type="hidden" name="main_sender_id" id="main_sender_id" value="">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งสำรอง 1</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="second_sender" id="second_sender" class="width-100" />
			<input type="hidden" name="second_sender_id" id="second_sender_id" value="">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ขนส่งสำรอง 2</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="third_sender" id="third_sender" class="width-100" />
			<input type="hidden" name="third_sender_id" id="third_sender_id" value="">
    </div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/transport.js"></script>
<?php $this->load->view('include/footer'); ?>
