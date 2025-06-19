<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ใบสั่งซื้อ</label>
			<input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ใบส่งสินค้า</label>
			<input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ผู้จำหน่าย</label>
			<input type="text" class="form-control input-sm search" name="vender" value="<?php echo $vender; ?>" />
		</div>

		<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>User</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-3-harf col-md-4 col-sm-6 col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select name="status" class="form-control input-sm" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
				<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
				<option value="R" <?php echo is_selected('R', $status); ?>>On Process</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1240px;">
			<thead>
				<tr>
					<th class="fix-width-120"></th>
					<th class="fix-width-60 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-60 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle">คลัง</th>
					<th class="fix-width-100 middle">ใบสั่งซื้อ</th>
					<th class="fix-width-150 middle">ใบส่งสินค้า</th>
					<th class="fix-width-250 middle">ผู้จำหน่าย</th>
					<th class="fix-width-100 middle text-center">จำนวน</th>
					<th class="min-width-100 middle">User</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>" style="font-size:11px; background-color: <?php echo receive_status_color($rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($this->pm->can_delete && $rs->status != 'D') : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
								<?php if($this->pm->can_edit && ($rs->status == 'O' OR $rs->status == 'R')) : ?>
									<button type="button" class="btn btn-minier btn-purple" onclick="goProcess('<?php echo $rs->code; ?>')">รับเข้า</button>
								<?php endif; ?>
								<?php if($this->pm->can_edit && $rs->status == 'P') : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo receive_status_text($rs->status); ?></td>
							<td class="middle"><?php echo $rs->warehouse_code; ?></td>
							<td class="middle"><?php echo $rs->po_code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->vender_name; ?></td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script>
	$('#user').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
