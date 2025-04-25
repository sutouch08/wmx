<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>SO No.</label>
		  <input type="text" class="form-control input-sm text-center" value="<?php echo $order->so_no; ?>" disabled />
		</div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
		</div>
    <div class="col-lg-4-harf col-md-5 col-sm-4-harf col-xs-12 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    	<label>ลูกค้า[ออนไลน์]</label>
      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>อ้างอิง</label>
		  <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>ช่องทางขาย</label>
			<input type="text" class="width-100" id="channels" value="<?php echo $this->channels_model->get_name($order->channels_code); ?>" disabled />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>การชำระเงิน</label>
			<input type="text" class="width-100" id="payment" value="<?php echo $this->payment_methods_model->get_name($order->payment_code); ?>" disabled />
    </div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>Pre order</label>
			<input type="text" class="width-100 text-center" value="<?php echo $order->is_pre_order == 1 ? 'Yes' : 'No'; ?>" disabled />
		</div>

		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
			<label>คลัง</label>
			<input type="text" class="width-100" value="<?php echo $order->warehouse_code . " : ". warehouse_name($order->warehouse_code); ?>" disabled />
	  </div>
		<?php if($order->is_backorder == 1 && $order->state < 5) : ?>
			<?php $this->load->view('backorder_watermark'); ?>
		<?php endif; ?>
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
</div>
<hr class="margin-bottom-15 padding-5"/>
