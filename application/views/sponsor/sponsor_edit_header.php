<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="width-100 text-center" id="code" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>วันที่</label>
			<input type="text" class="width-100 text-center h e" id="date-add" value="<?php echo thai_date($order->date_add); ?>" disabled/>
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
	    <label>รหัสผู้รับ</label>
	    <input type="text" class="width-100 text-center h  e" id="customer-code" value="<?php echo $order->customer_code; ?>"  disabled/>
	  </div>
    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12 padding-5">
    	<label>ผู้รับ[สโมสร/ผู้รับการสนับสนุน]</label>
			<input type="text" class="width-100 h e" id="customer-name" value="<?php echo $order->customer_name; ?>"  disabled/>
    </div>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    	<label>ผู้เบิก</label>
      <input type="text" class="width-100 h e" id="user-ref" value="<?php echo $order->customer_ref; ?>"  disabled/>
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>รหัสงบ</label>
			<input type="text" class="width-100 text-center h e" id="budget-code" value="<?php echo $order->budget_code; ?>"  disabled/>
    </div>
		<div class="col-lg-4 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>Description</label>
			<input type="text" class="width-100 h" id="budget-name" value="<?php echo $order->budget_name; ?>" readonly disabled/>
    </div>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
			<label>คลัง</label>
	    <select class="width-100 h e" id="warehouse" disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>
		<?php if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') : ?>
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
				<label class="not-show">edit</label>
				<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">Edit</button>
				<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateOrder()">Update</button>
			</div>
			<?php endif; ?>
		<?php endif; ?>
    <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
		<input type="hidden" id="is-approved" value="<?php echo $order->is_approved; ?>" />
		<input type="hidden" id="budget-id" value="<?php echo $order->budget_id; ?>" />
</div>
<hr class="margin-bottom-15 padding-5"/>
