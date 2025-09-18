<?php $this->load->view('include/header'); ?>
<?php $isAdmin = $this->_SuperAdmin; ?>
<style>
	.form-group {
		margin-bottom: 5px;
	}

	.line-total {
		border:none !important;
		background-color: transparent !important;
	}

	@media (max-width:767px) {
		#total-qty {
			margin-bottom: 5px;
		}
	}
</style>
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" style="margin-top:6px;"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-warning btn-white btn-100 top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; กลับ</button>
		<?php if($order->state != 9 && ($this->pm->can_delete OR $this->_SuperAdmin)) : ?>
			<button type="button" class="btn btn-xs btn-danger btn-white btn-100 top-btn" onclick="cancelOrder()"><i class="fa fa-times"></i> &nbsp; ยกเลิก</button>
		<?php endif; ?>
		<?php if($order->status == 0 && $order->is_expired == 0) : ?>
			<button type="button" id="btn-save-order" class="btn btn-xs btn-success btn-white btn-100 top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> &nbsp; บันทึก</button>
		<?php else : ?>
			<button type="button" id="btn-save-order" class="btn btn-xs btn-success btn-white btn-100 top-btn hide" onclick="saveOrder()"><i class="fa fa-save"></i> &nbsp; บันทึก</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr/>
<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_detail'); ?>

<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/cancel_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
