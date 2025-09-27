<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="filter-pad move-out" id="filter-pad">
		<div class="nav-title nav-title-center">
	    <a onclick="closeFilter()"><i class="fa fa-angle-left fa-2x"></i></a>
	    <div class="font-size-18 text-center">ตัวกรอง</div>
	  </div>

		<div class="page-wrap" style="height:calc(100vh - 120px);">
			<div class="col-xs-6 padding-5 fi">
				<label>เลขที่เอกสาร</label>
				<input type="text" class="form-control search" name="code"  value="<?php echo $code; ?>" />
			</div>

			<div class="col-xs-6 padding-5 fi">
				<label>สถานะ</label>
				<select class="form-control" name="status">
					<option value="all">ทั้งหมด</option>
					<option value="R" <?php echo is_selected('R', $status); ?>>Release</option>
					<option value="Y" <?php echo is_selected('Y', $status); ?>>Picking</option>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>วันที่</label>
				<div class="input-group width-100">
					<input type="text" class="form-control width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
					<input type="text" class="form-control width-50 text-center" name="from_date" id="toDate" value="<?php echo $to_date; ?>" />
				</div>
			</div>

			<div class="col-xs-12 padding-5 fi hide">
				<label>คลัง</label>
				<select class="form-control" name="warehouse" id="warehouse">
					<option value="all">ทั้งหมด</option>
					<?php echo select_common_warehouse($warehouse); ?>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>โซน</label>
				<select class="form-control" name="zone" id="zone">
					<option value="all">ทั้งหมด</option>
					<?php echo select_pickface_zone($zone); ?>
				</select>
			</div>

			<div class="col-xs-12 padding-5 fi">
				<label>ช่องทางขาย</label>
				<select class="form-control" name="channels" id="channels">
					<option value="all">ทั้งหมด</option>
					<?php echo select_channels($channels); ?>
				</select>
			</div>

			<div class="col-xs-8 padding-5 fi">
				<label>พนักงาน</label>
				<select class="form-control" name="user" id="user">
					<option value="all">ทั้งหมด</option>
					<?php echo select_user($user); ?>
				</select>
			</div>

			<?php $perpage = get_rows();?>

			<div class="col-xs-4 padding-5 fi">
				<label>รายการต่อหน้า</label>
				<select class="form-control" id="set_rows" onchange="setRows()">
					<option value="20" <?php echo is_selected('20', $perpage); ?>>20</option>
					<option value="50" <?php echo is_selected('50', $perpage); ?>>50</option>
					<option value="100" <?php echo is_selected('100', $perpage); ?>>100</option>
					<option value="200" <?php echo is_selected('200', $perpage); ?>>200</option>
					<option value="300" <?php echo is_selected('300', $perpage); ?>>300</option>
				</select>
			</div>

			<div class="col-xs-9 padding-5 fi">
				<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
			</div>
			<div class="col-xs-3 padding-5 fi">
				<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<script>
	$('#warehouse').select2();
	$('#zone').select2();
	$('#channels').select2();
	$('#user').select2();
</script>
