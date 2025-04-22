<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/cancle/style'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $order_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>รหัสสินค้า</label>
			<input type="text" class="form-control input-sm search" name="pd_code" value="<?php echo $pd_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>โซน</label>
			<input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" />
		</div>

		<div class="col-lg-3-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
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
		<table class="table table-striped border-1 no-border-xs">
			<tr class="font-size-11">
				<th class="fix-width-50 text-center hidden-xs">#</th>
				<th class="fix-width-100 text-center hidden-xs">วันที่</th>
				<th class="fix-width-100 hidden-xs">เลขที่เอกสาร</th>
				<th class="min-width-150 hidden-xs">สินค้า</th>
				<th class="fix-width-80 text-center hidden-xs">จำนวน</th>
				<th class="fix-width-100 text-center hidden-xs">สถานะ</th>
				<th class="fix-width-150 hidden-xs">โซน</th>
				<th class="fix-width-80 hidden-xs"></th>
				<th class="width-100 hide"></th>
			</tr>
			<tbody>
				<?php if( !empty($data)) : ?>
					<?php $no = $this->uri->segment(4) + 1; ?>
					<?php foreach($data as $rs) : ?>
						<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
							<td class="text-center no hidden-xs"><?php echo $no; ?></td>
							<td class="text-center hidden-xs"><?php echo thai_date($rs->date_upd); ?></td>
							<td class="hidden-xs"><?php echo $rs->order_code; ?></td>
							<td class="hidden-xs"><?php echo $rs->product_code; ?></td>
							<td class="text-center hidden-xs"><?php echo number($rs->qty); ?></td>
							<td class="text-center hidden-xs"><?php echo $rs->state_name; ?></td>
							<td class=" hidden-xs"> <?php echo $rs->zone_code; ?></td>
							<td class=" hidden-xs">
								<?php if($this->pm->can_edit) : ?>
								<button type="button"
									class="btn btn-minier btn-primary"
									onclick="moveBack(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_name; ?>')">
									<i class="fa fa-reply"></i></button>
								<?php endif; ?>
								<?php if($this->pm->can_delete) : ?>
									<button type="button"
										class="btn btn-minier btn-danger"
										onclick="removeCancel(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_name; ?>')">
										<i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
							<td class="visible-xs" style="border:0px; padding:3px; font-size:14px;">
								<div class="col-xs-12" style="border:solid 1px #ccc; border-radius:5px; box-shadow:0px 1px 2px #f3ecec; padding:5px;">
									<div class="width-100" style="padding: 3px 3px 3px 10px;">
										<div class="listing width-50 margin-bottom-3 font-size-11 pre-wrap"><b>วันที่ : </b><?php echo thai_date($rs->date_upd,); ?></div>
										<div class="listing width-50 margin-bottom-3 font-size-11 pre-wrap"><b>เลขที่ : </b><?php echo $rs->order_code; ?></div>
										<div class="listing width-100 margin-bottom-3 font-size-11 pre-wrap"><b>สินค้า : </b><?php echo $rs->product_code; ?></div>
										<div class="listing width-50 margin-bottom-3 font-size-11 pre-wrap"><b>โซน : </b> <?php echo $rs->zone_code; ?></div>
										<div class="listing width-50 margin-bottom-3 font-size-11 pre-wrap"><b>จำนวน : </b> <?php echo number($rs->qty); ?></div>
										<div class="listing width-50 margin-bottom-3 font-size-11 pre-wrap"><b>สถานะ : </b><?php echo $rs->state_name; ?></div>
										<div class="listing width-50 margin-top-15 text-right">
											<?php if($this->pm->can_edit) : ?>
												<button type="button"
												class="btn btn-minier btn-primary btn-50"
												onclick="moveBack(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_name; ?>')">
												<i class="fa fa-reply"></i>&nbsp; ย้ายกลับ</button>
											<?php endif; ?>
											<?php if($this->_SuperAdmin) : ?>
												<button type="button"
												class="btn btn-minier btn-danger"
												onclick="removeCancel(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_name; ?>')">
												<i class="fa fa-trash"></i></button>
											<?php endif; ?>
										</div>
									</div>
								</div>

							</td>
						</tr>
						<?php  $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="8" class="text-center">--- ไม่พบข้อมูล ---</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/cancle/cancle.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
