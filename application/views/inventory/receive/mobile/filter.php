<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title">
	    <a class="pull-left margin-left-10" onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>
		<div class="divider-hidden"></div>
		<div class="col-xs-6 padding-5 fi">
			<label>Doc No.</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>Order No.</label>
			<input type="text" class="width-100 search" name="order_no" value="<?php echo $order_no; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>Vendor</label>
			<input type="text" class="width-100 search" name="vendor" value="<?php echo $vendor; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>REF_NO1</label>
			<input type="text" class="width-100 search" name="ref_no1" value="<?php echo $ref_no1; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>REF_NO2</label>
			<input type="text" class="width-100 search" name="ref_no2" value="<?php echo $ref_no2; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>Type</label>
			<select class="width-100 filter" name="order_type">
				<option value="all">All</option>
				<option value="WR" <?php echo is_selected($order_type, "WR"); ?>>WR</option>
				<option value="RT" <?php echo is_selected($order_type, "RT"); ?>>RT</option>
				<option value="RN" <?php echo is_selected($order_type, "RN"); ?>>RN</option>
				<option value="CN" <?php echo is_selected($order_type, "CN"); ?>>CN</option>
				<option value="SM" <?php echo is_selected($order_type, "SM"); ?>>SM</option>
				<option value="WW" <?php echo is_selected($order_type, "WW"); ?>>WW</option>
			</select>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>Doc Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" inputmode="none" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" inputmode="none" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>Order Date</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" inputmode="none" name="order_from_date" id="orderFromDate" value="<?php echo $order_from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" inputmode="none" name="order_to_date" id="orderToDate" value="<?php echo $order_to_date; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>Warehouse</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">All</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-xs-9 padding-5 fi">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-xs-3 padding-5 fi">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>

<?php echo $this->pagination->create_links(); ?>
