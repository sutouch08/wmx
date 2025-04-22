<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-sm btn-primary btn-top" onclick="printMove()"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div><!-- End Row -->
<input type="hidden" id="move_code" name="move_code" value="<?php echo $doc->code; ?>" />
<hr/>
<?php
	$this->load->view('move/move_view_header');
	$this->load->view('move/move_view_detail');
	$this->load->view('accept_modal');
?>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
