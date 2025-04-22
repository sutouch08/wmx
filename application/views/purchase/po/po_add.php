<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 paddig-top-5">
    <h3 class="title"> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home.'/add'; ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="" readonly>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center e" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>ผู้ขาย</label>
      <input type="text" class="form-control input-sm text-center e" name="vender_code" id="vender_code" value="" autofocus>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
      <label class="not-show">ผู้ขาย</label>
      <input type="text" class="form-control input-sm e" name="vender_name" id="vender_name" value="" required>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>กำหนดส่ง</label>
      <input type="text" class="form-control input-sm text-center" name="require_date" id="require_date" value="" readonly>
    </div>
    <div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">add</label>
      <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
    </div>
  </div>
</form>
<hr class="margin-top-15">
<script src="<?php echo base_url(); ?>scripts/purchase/po.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
