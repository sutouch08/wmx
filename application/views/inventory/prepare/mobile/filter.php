<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
    <div class="nav-title">
	    <a class="pull-left margin-left-10" onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>
		<div class="divider-hidden"></div>
		<!-- <div class="col-xs-12 padding-5 text-center">
			<h4 class="title">ตัวกรอง</h4>
		</div> -->

		<div class="col-xs-6 padding-5 fi">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>MKP No.</label>
			<input type="text" class="width-100 search" name="reference"  value="<?php echo $reference; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>SO No.</label>
			<input type="text" class="width-100 search" name="so_no" value="<?php echo $so_no; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>Fulfillment No.</label>
			<input type="text" class="width-100 search" name="fulfillment_code"  value="<?php echo $fulfillment_code; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>ลูกค้า</label>
			<input type="text" class="width-100 search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>ช่องทางขาย</label>
			<select class="width-100 filter" name="channels" id="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>Online</label>
			<select class="width-100 filter" name="is_online">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected($is_online, '1'); ?>>Online</option>
				<option value="0" <?php echo is_selected($is_online, '0'); ?>>Offline</option>
			</select>
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>รูปแบบ</label>
			<select class="width-100 filter" name="role" id="role">
	      <option value="all">ทั้งหมด</option>
	      <?php echo select_order_role($role); ?>
	    </select>
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_sell_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-xs-6 padding-5 fi">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" readonly value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" readonly value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 fi">
			<label class="display-block not-show">&nbsp;</label>
			<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5 fi">
			<label class="display-block not-show">&nbsp;</label>
			<button type="button" class="btn btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<?php echo $this->pagination->create_links(); ?>
