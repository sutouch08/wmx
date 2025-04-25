<?php $hide = "not-show"; ?>
<div class="row">
	<div class="col-lg-2 col-lg-offset-10 col-md-3 col-md-offset-9 col-sm-3 col-sm-offset-9 col-xs-6 col-xs-offset-6 padding-5">
		<div class="input-group">
			<span class="input-group-addon" style="font-size:11px; line-height:0;">COD</span>
			<input type="number" class="form-control input-sm input-mini text-center" id="cod-amount" name="cod-amount" value="<?php echo $order->cod_amount; ?>" readonly/>
		</div>
	</div>
</div>
<hr/>

<?php $this->load->view('validate_credentials'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/order_discount.js?v=<?php echo date('Ymd'); ?>"></script>
