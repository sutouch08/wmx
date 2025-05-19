<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 paddig-top-5">
    <h3 class="title"> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->_SuperAdmin) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ผู้ขาย</label>
			<input type="text" class="form-control input-sm search" name="vender" value="<?php echo $vender; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all" <?php echo is_selected('all', $status); ?>>ทั้งหมด</option>
				<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Partial</option>
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
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
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
		<table class="table table-striped table-hover border-1" style="min-width:1080px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่</th>
					<th class="fix-width-80 middle text-center">สถานะ</th>
					<th class="fix-width-250 middle">ผู้ขาย</th>
					<th class="fix-width-100 middle text-right">จำนวน</th>
					<th class="fix-width-100 middle text-right">ค้างรับ</th>
					<th class="fix-width-100 middle text-center">วันครบกำหนด</th>
					<th class="min-width-100 middle">User</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($po)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($po as $rs) : ?>
            <tr class="font-size-11" id="row-<?php echo $rs->id; ?>" style="background-color:<?php echo po_status_color($rs->status); ?>">
							<td class="middle">
									<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if(($rs->status != 'C' && $rs->status != 'D' && $this->pm->can_delete) OR ($rs->status != 'D' && $this->_SuperAdmin)) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goCancel('<?php echo $rs->code; ?>')"><i class="fa fa-times"></i></button>
								<?php endif; ?>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->doc_date); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo po_status_text($rs->status); ?></td>
							<td class="middle"><?php echo $rs->vender_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->total_qty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->total_open_qty, 2); ?></td>							
							<td class="middle text-center"><?php echo thai_date($rs->due_date); ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="11" class="text-center">---- ไม่พบรายการ ----</td>
					</tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/purchase/po.js?v=<?php echo date('Ymd');?>"></script>

<?php $this->load->view('include/footer'); ?>
