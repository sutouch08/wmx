<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status != 'D' && $this->pm->can_delete) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="confirmCancel('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i>  ยกเลิก</button>
		<?php endif; ?>
		<?php if($doc->status != 'P' && $this->pm->can_delete) : ?>
			<button type="button" class="btn btn-white btn-purple top-btn" onclick="rollback('<?php echo $doc->code; ?>')"><i class="fa fa-history"></i>  ย้อนสถานะ</button>
		<?php endif; ?>

		<button type="button" class="btn btn-white btn-primary top-btn" onclick="printMove()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div><!-- End Row -->
<input type="hidden" id="move_code" name="move_code" value="<?php echo $doc->code; ?>" />
<hr/>
<?php
	$this->load->view('move/move_view_header');
	$this->load->view('move/move_view_detail');
?>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
