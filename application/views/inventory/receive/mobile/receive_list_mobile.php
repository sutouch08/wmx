<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/receive/mobile/style'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<?php $this->load->view('inventory/receive/mobile/filter'); ?>

<div class="move-list">
	<?php if( ! empty($list)) : ?>
		<?php $no = $this->uri->segment($this->segment) + 1; ?>
		<?php foreach($list as $rs) : ?>
			<div class="move-list-item">
				<div class="col-xs-10 padding-5">
					<p class="move-list-line bold">No :	<?php echo $rs->code; ?> | <?php echo $rs->order_no; ?></p>
					<p class="move-list-line bold">Vendor :	<?php echo $rs->vendor_name; ?></p>
					<p class="move-list-line bold">วันที่ : <?php echo thai_date($rs->date_add, FALSE,'/'); ?> &nbsp;&nbsp;|&nbsp;&nbsp; คลัง : <?php echo $rs->warehouse_code; ?></p>
				</div>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<a class="move-list-link font-size-24" href="javascript:doProcess('<?php echo $rs->code; ?>')"><i class="fa fa-angle-right"></i></a>
				<?php endif; ?>
			</div>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php $this->load->view('inventory/receive/mobile/list_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive/receive.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive/receive_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
