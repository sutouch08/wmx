<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title">
	    <a class="pull-left margin-left-10" onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>
		<div class="divider-hidden"></div>

		<div class="col-xs-6 padding-5 fi">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>ใบสั่งซื้อ</label>
	    <input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
	  </div>

		<div class="col-xs-6 padding-5 fi">
			<label>ใบส่งสินค้า</label>
	    <input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
	  </div>

		<div class="col-xs-6 padding-5 fi">
	    <label>ผู้จำหน่าย</label>
			<input type="text" class="form-control input-sm search" name="vender" value="<?php echo $vender; ?>" />
	  </div>

		<div class="col-xs-6 padding-5 fi">
	    <label>User</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
	  </div>

		<div class="col-xs-6 padding-5 fi">
	    <label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<label>วันที่</label>
			<div class="input-group width-100">
				<input type="text" class="form-control input-sm text-center width-50 from-date" inputmode="none" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm text-center width-50" inputmode="none" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi" style="position:absolute; bottom:80px; left:0px;">
			<div class="col-xs-6 padding-5 fi">
				<label class="display-block not-show">buton</label>
				<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
			</div>
			<div class="col-xs-6 padding-5 fi">
				<label class="display-block not-show">buton</label>
				<button type="button" class="btn btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<script>
	$('#user').select2();
	$('#warehouse').select2();
</script>
<?php echo $this->pagination->create_links(); ?>
