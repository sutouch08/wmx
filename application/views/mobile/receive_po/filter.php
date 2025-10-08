<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title nav-title-center">
	    <a onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>

		<div class="page-wrap" style="height:calc(100vh - 120px);">
			<div class="col-xs-6 padding-5 fi">
				<label>เลขที่เอกสาร</label>
				<input type="text" class="form-control" name="code"  value="<?php echo $code; ?>" />
			</div>

			<div class="col-xs-6 padding-5 fi">
				<label>Invoice No.</label>
				<input type="text" class="form-control" name="invoice"  value="<?php echo $invoice; ?>" />
			</div>

			<div class="col-xs-6 padding-5 fi">
				<label>PO No.</label>
				<input type="text" class="form-control" name="po" value="<?php echo $po; ?>" />
			</div>

			<div class="col-xs-6 padding-5 fi">
				<label>Vender</label>
				<input type="text" class="form-control" name="vender"  value="<?php echo $vender; ?>" />
			</div>

			<?php $show = $tab == 'all' ? '' : 'hide'; ?>
			<div class="col-xs-12 padding-5 fi <?php echo $show; ?>">
				<label>สถานะ</label>
				<select class="form-control" name="status">
					<option value="all">ทั้งหมด</option>
					<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
					<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
					<option value="R" <?php echo is_selected('R', $status); ?>>On Process</option>
					<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
					<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>คลัง</label>
				<select class="form-control" name="warehouse" id="warehouse">
					<option value="all">ทั้งหมด</option>
					<?php echo select_sell_warehouse($warehouse); ?>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>User</label>
				<select class="form-control" name="user" id="user">
					<option value="all">ทั้งหมด</option>
					<?php echo select_user($user); ?>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>วันที่</label>
				<div class="input-daterange input-group width-100">
					<input type="text" class="form-control width-50 text-center from-date" name="from_date" id="fromDate" readonly value="<?php echo $from_date; ?>" />
					<input type="text" class="form-control width-50 text-center" name="to_date" id="toDate" readonly value="<?php echo $to_date; ?>" />
				</div>
			</div>

			<div class="col-xs-12 padding-5">
				<label class="not-show">Submit</label>
				<button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<script>
	$('#warehouse').select2();
	$('#user').select2();
</script>
