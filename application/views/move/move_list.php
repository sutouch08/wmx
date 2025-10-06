<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_common_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>พนักงาน</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm" name="status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-group">
				<input type="text" class="form-control input-sm width-50 from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<p  class="pull-right top-p">
			ว่างๆ = ปกติ, &nbsp;
			<span class="blue">NC</span> = ยังไม่บันทึก, &nbsp;
			<span class="orange">WC</span> = รอการยืนยัน, &nbsp;
			<span class="red">CN</span> = ยกเลิก, &nbsp;
			<span class="dark">EXP</span> = หมดอายุ &nbsp;
		</p>
		<table class="table table-striped table-hover border-1" style="min-width:750px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-60 middle">สถานะ</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="min-width-200 middle">คลัง</th>
					<th class="fix-width-150 middle">User</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($list)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $whs = []; ?>
          <?php foreach($list as $rs) : ?>
						<?php $whsName = empty($whs[$rs->warehouse_code]) ? $this->warehouse_model->get_name($rs->warehouse_code) : $whs[$rs->warehouse_code]; ?>
						<?php if(empty($whs[$rs->warehouse_code])) { $whs[$rs->warehouse_code] = $whsName; } ?>
            <tr class="font-size-11 <?php echo status_color($rs->status); ?>" id="row-<?php echo $rs->id; ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($rs->status == 'P' && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($rs->status != 'D' && $this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="confirmCancel('<?php echo $rs->code; ?>')"><i class="fa fa-times"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo status_text($rs->status); ?></td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $whsName; ?></td>
              <td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
				<?php else : ?>
					<tr class="font-size-11"><td colspan="8" class="text-center">-- No Data --</td></tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
