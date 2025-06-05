<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
	    <label>รหัสผู้รับ</label>
	    <input type="text" class="form-control input-sm text-center edit" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" required disabled />
	  </div>
    <div class="col-lg-5-harf col-md-7 col-sm-6 col-xs-12 padding-5">
    	<label>ผู้รับ[สโมสร/ผู้รับการสนับสนุน]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    	<label>ผู้เบิก</label>
      <input type="text" class="form-control input-sm edit" id="user_ref" name="user_ref" value="<?php echo $order->user_ref; ?>" disabled />
    </div>
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ผู้ทำรายการ</label>
		  <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
		</div>

		<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-8 padding-5">
			<label>คลัง</label>
	    <select class="form-control input-sm edit" name="warehouse" id="warehouse" disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
		<input type="hidden" id="is_approved" value="<?php echo $order->is_approved; ?>" />
</div>
<hr class="margin-bottom-15 padding-5"/>
