<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status != 2) : ?>
			<?php if($doc->status != 1 && $this->pm->can_delete OR $this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-xs btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>', <?php echo $doc->status; ?>)">
					<i class="fa fa-trash"></i> ยกเลิก
				</button>
			<?php endif; ?>
		<?php endif; ?>
		<?php if($doc->is_expire == 0 && $doc->status == 3 && $this->pm->can_approve) : ?>
			<button type="button" class="btn btn-xs btn-primary top-btn" onclick="pullBack('<?php echo $doc->code; ?>')">ย้อนสถานะกลับมาแก้ไข</button>
		<?php endif; ?>
		<?php if($doc->is_expire == 0 && $doc->status == -1 && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> &nbsp; แก้ไข</button>
		<?php endif; ?>		
		<button type="button" class="btn btn-xs btn-primary top-btn hidden-xs" onclick="printTransfer()"><i class="fa fa-print"></i> ใบโอน</button>
	</div>
</div><!-- End Row -->
<input type="hidden" id="transfer_code" name="transfer_code" value="<?php echo $doc->code; ?>" />
<hr/>
<?php
	if($doc->is_expire == 1 OR $doc->status == 2)
	{
		if($doc->status == 2)
		{
			$this->load->view('cancle_watermark');
		}
		else
		{
			$this->load->view('expire_watermark');
		}
	}
	else
	{
		if($doc->status == 3)
		{
			$this->load->view('on_process_watermark');
		}

		if($doc->status == 0 && $doc->is_approve == 3)
		{
			$this->load->view('reject_watermark');
		}
	}

	$this->load->view('transfer/transfer_view_header');
	$this->load->view('transfer/transfer_view_detail');
	$this->load->view('cancle_modal');
?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
