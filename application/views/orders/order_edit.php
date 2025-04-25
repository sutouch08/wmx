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
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    	<p class="pull-right top-p text-right" >
				<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<button type="button" class="btn btn-xs btn-default top-btn hidden-xs" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($order->status == 0 && $order->is_expired == 0) : ?>
					<button type="button" id="btn-save-order" class="btn btn-xs btn-success btn-100 top-btn" onclick="saveOrder()">บันทึก</button>
				<?php else : ?>
					<button type="button" id="btn-save-order" class="btn btn-xs btn-success btn-100 top-btn hide" onclick="saveOrder()">บันทึก</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>

<?php $this->load->view('orders/order_discount_bar'); ?>
<?php $this->load->view('orders/order_detail'); ?>
<?php //$this->load->view('orders/order_online_modal'); ?>
<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/cancel_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
