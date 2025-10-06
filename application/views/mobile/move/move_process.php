<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/move/move_process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?>&nbsp; [ <?php echo status_text($doc->status); ?> ]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>

<!-- ใช้ tab เป็นตัดกำหนดว่า ยิงบาร์โค้ดแล้วจะทำ function อะไร -->
<!-- tab == 'move_out' => addToTemp(),  tab == 'move_in' => moveToZone() -->
<input type="hidden" id="tab" value="<?php echo $tab; ?>" />

<?php
$this->load->view('mobile/move/header_panel');

if($tab == 'summary')
{
	$this->load->view('mobile/move/move_summary');
}

if($tab == 'move_out')
{
	$this->load->view('mobile/move/move_out');
}

if($tab == 'move_in')
{
	$this->load->view('mobile/move/move_in');
}

?>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
