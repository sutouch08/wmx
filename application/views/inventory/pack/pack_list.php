<?php $this->load->view('include/header'); ?>
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
		<table class="table table-striped border-1" style="min-width:800px;">
			<tr class="font-size-11">
				<th class="fix-width-40 text-center">#</th>
				<th class="fix-width-150 text-center">วันที่</th>
				<th class="fix-width-100 text-center">เลขที่เอกสาร</th>
				<th class="min-width-150 text-center">สินค้า</th>
				<th class="fix-width-100 text-center">จำนวน</th>
				<th class="fix-width-100 text-center">กล่อง</th>
				<th class="fix-width-100 text-center">สถานะ</th>
				<th class="fix-width-50"></th>
			</tr>
			<tbody>
				<?php if( !empty($data)) : ?>
					<?php $no = $this->uri->segment(4) + 1; ?>
					<?php foreach($data as $rs) : ?>
						<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
							<td class="text-center no"><?php echo $no; ?></td>
							<td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
							<td class="text-center"><?php echo $rs->order_code; ?></td>
							<td><?php echo $rs->product_code; ?></td>
							<td class="text-center"><?php echo number($rs->qty); ?></td>
							<td class="text-center">
								<?php echo (empty($rs->box_id) ? "" : "กล่องที่ ".$rs->box_no); ?>
							</td>
							<td class="text-center"><?php echo $rs->state_name; ?></td>
							<td class="text-right">
								<?php if($this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger"
									onclick="deletePack(<?php echo $rs->id;?>, '<?php echo $rs->order_code; ?>', '<?php echo $rs->product_code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
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
<script src="<?php echo base_url(); ?>scripts/inventory/pack/pack.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
