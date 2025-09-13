<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title">
	    <a class="pull-left margin-left-10" onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>
		<div class="divider-hidden"></div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">เลขที่เอกสาร</span>
				<input type="text" class="form-control input-lg" name="code"  value="<?php echo $code; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">ใบสั่งซื้อ</span>
				<input type="text" class="form-control input-lg search" name="po" value="<?php echo $po; ?>" />
			</div>
	  </div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">ใบส่งสินค้า</span>
				<input type="text" class="form-control input-lg search" name="invoice" value="<?php echo $invoice; ?>" />
			</div>
	  </div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">ผู้จำหน่าย</span>
				<input type="text" class="form-control input-lg search" name="vender" value="<?php echo $vender; ?>" />
			</div>
	  </div>

		<?php $show = $tab == 'all' ? '' : 'hide'; ?>
		<div class="col-xs-12 padding-5 fi <?php echo $show; ?>">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">สถานะ</span>
				<select class="form-control input-lg" name="status">
					<option value="all">ทั้งหมด</option>
					<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
					<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
					<option value="R" <?php echo is_selected('R', $status); ?>>On Process</option>
					<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
					<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
				</select>
			</div>
	  </div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">คลัง</span>
				<select class="form-control input-lg" name="warehouse">
					<option value="all">ทั้งหมด</option>
					<?php echo select_warehouse($warehouse); ?>
				</select>
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<div class="input-group width-100">
				<span class="input-group-addon width-30">วันที่</span>
				<input type="text" class="form-control input-lg text-center width-50 from-date" style="border-radius:0 !important;" inputmode="none" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-lg text-center width-50" inputmode="none" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-xs-12 padding-5 fi">
			<button type="submit" class="btn btn-primary btn-block" style="border-radius:10px;"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-xs-12 padding-5 fi">
			<button type="button" class="btn btn-warning btn-block" style="border-radius:10px;" onclick="resetFilter('<?php echo $tab; ?>')"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<?php echo $this->pagination->create_links(); ?>
