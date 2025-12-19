<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/move/move_process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?>&nbsp; [ <?php echo status_text($doc->status); ?> ]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>

<!-- ใช้ tab เป็นตัดกำหนดว่า ยิงบาร์โค้ดแล้วจะทำ function อะไร -->
<!-- tab == 'move_out' => addToTemp(),  tab == 'move_in' => moveToZone(), tab === 'items' => getItemZone() -->
<input type="hidden" id="tab" value="<?php echo $tab; ?>" />

<?php
$this->load->view('mobile/move/header_panel');

switch ($tab) {
	case 'summary':
		$this->load->view('mobile/move/move_summary');
	break;
	case 'move_out':
		$this->load->view('mobile/move/move_out');
		break;
	case 'move_in':
		$this->load->view('mobile/move/move_in');
		break;
	case 'items':
		$this->load->view('mobile/move/move_find_items');
		break;
	default:
		$this->load->view('mobile/move/move_summary');
		break;
}

?>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_control.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
