<?php $this->load->view('include/header_mobile'); ?>
<div class="nav-title nav-title-center" style="position:fixed;">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center">เพิ่ม เอกสารตัดยอดใหม่</div>
</div>
<div class="row padding-top-20">
  <div class="col-xs-6">
		<label>เลขที่</label>
		<input type="text" class="form-control text-center" id="code" value="" disabled />
	</div>
	<div class="col-xs-6">
		<label>วันที่</label>
		<input type="text" class="form-control text-center r" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
  <div class="divider-hidden"></div>

	<div class="col-xs-12">
		<label>ช่องทางขาย</label>
    <select class="form-control" id="channels">
      <option value="">เลือก</option>
      <?php echo select_channels(); ?>
    </select>
	</div>
	<div class="divider-hidden"></div>

  <div class="col-xs-12">
		<label>คลังต้นทาง</label>
    <select class="form-control" id="warehouse">
      <option value="">เลือก</option>
      <?php echo select_common_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
    </select>
	</div>
	<div class="divider-hidden"></div>

	<div class="col-xs-12">
		<label>โซนปลายทาง</label>
    <select class="form-control" id="zone-code">
      <option value="">เลือก</option>
      <?php echo select_pickface_zone(); ?>
    </select>
	</div>
	<div class="divider-hidden"></div>

	<div class="col-xs-12">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control r" id="remark" />
	</div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
  <?php if($this->pm->can_add) : ?>
  	<div class="col-xs-12">
  		<button type="button" class="btn btn-sm btn-primary btn-block" onclick="add()"><i class="fa fa-plus"></i>&nbsp; &nbsp; เพิ่ม</button>
  	</div>
  <?php endif; ?>
</div>

<script>
$('#warehouse').select2();
$('#zone').select2();
$('#channels').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
