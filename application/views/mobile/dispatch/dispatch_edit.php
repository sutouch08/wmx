<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/dispatch/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?>&nbsp; [ <?php echo status_text($doc->status); ?> ]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/dispatch/header_panel'); ?>
<?php $this->load->view('mobile/dispatch/dispatch_control'); ?>
<?php $this->load->view('mobile/dispatch/dispatch_details'); ?>
<?php $this->load->view('mobile/dispatch/process_menu'); ?>

<script>
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
