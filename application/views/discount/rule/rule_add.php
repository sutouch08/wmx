<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>Document No.</label>
    <input type="text" class="form-control input-sm text-center" name="code" id="code" value="" disabled />
  </div>

  <div class="col-lg-9-harf col-md-9-harf col-sm-9 col-xs-6 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="" required />
  </div>
  <?php if($this->pm->can_add) : ?>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="saveAdd()">Add</button>
  </div>
  <?php endif; ?>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_add.js?v=<?php echo date('Ymd'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
