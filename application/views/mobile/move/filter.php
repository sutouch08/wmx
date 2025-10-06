<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title nav-title-center">
	    <a onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>

		<div class="page-wrap" style="height:calc(100vh - 120px);">
			<div class="col-xs-12 fi">
				<label>เลขที่เอกสาร</label>
				<input type="text" class="form-control" name="code"  value="<?php echo $code; ?>" />
			</div>

			<div class="col-xs-12 fi">
				<label>สถานะ</label>
				<select class="form-control" name="status">
					<option value="all">ทั้งหมด</option>
					<option value="P" <?php echo is_selected('P', $status); ?>>Pending</option>
					<option value="S" <?php echo is_selected('S', $status); ?>>Shipped</option>
					<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
					<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
				</select>
			</div>


			<div class="col-xs-12 fi">
				<label>วันที่</label>
				<div class="input-group width-100">
					<input type="text" class="form-control width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
					<input type="text" class="form-control width-50 text-center" name="from_date" id="toDate" value="<?php echo $to_date; ?>" />
				</div>
			</div>

			<div class="col-xs-12 fi">
				<label>คลัง</label>
				<select class="form-control" name="warehouse" id="warehouse">
					<option value="all">ทั้งหมด</option>
					<?php echo select_common_warehouse($warehouse); ?>
				</select>
			</div>


			<div class="col-xs-12 padding-5 fi">
				<label>พนักงาน</label>
				<select class="form-control" name="user" id="user">
					<option value="all">ทั้งหมด</option>
					<?php echo select_user($user); ?>
				</select>
			</div>

			<div class="col-xs-12 padding-5">
				<label class="not-show">Submit</label>
				<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<script>
	$('#warehouse').select2();	
	$('#user').select2();
</script>
