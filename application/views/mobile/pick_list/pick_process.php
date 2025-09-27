<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/pick_list/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?> [<?php echo status_text($doc->status); ?>]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/pick_list/header_panel'); ?>
<?php $this->load->view('mobile/pick_list/pick_control');?>
<?php $this->load->view('mobile/pick_list/incomplete_list'); ?>
<?php $this->load->view('mobile/pick_list/complete_list'); ?>
<?php $this->load->view('mobile/pick_list/transection_list'); ?>
<?php $this->load->view('mobile/pick_list/process_menu'); ?>


<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_process.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer_mobile'); ?>
