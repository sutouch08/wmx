<?php $this->load->view('include/header'); ?>
<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 margin-top-5">
			<h3 class="title"><?php echo $this->title; ?></h3>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status == 'P' && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-white btn-success top-btn" onclick="confirmSave()"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
			<?php endif; ?>
		</div>
	</div><!-- End Row -->
<hr/>
<?php
	$this->load->view('transfer/transfer_edit_header');
	$this->load->view('transfer/transfer_edit_detail');
?>


<?php else : ?>
<?php $this->load->view('deny_page'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
