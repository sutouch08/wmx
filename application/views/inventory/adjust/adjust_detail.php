<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status != 2 && ($this->pm->can_edit OR $this->pm->can_delete)) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					Action
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if($this->pm->can_edit && $doc->status < 1) : ?>
						<li class="warning">
							<a href="javascript:edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</a>
						</li>
					<?php endif; ?>
					<?php if($this->pm->can_delete && $doc->status < 2) : ?>
						<li class="danger">
							<a href="javascript:confirmCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if($doc->status == 0 && $doc->approve == 'P' && $this->pm->can_approve) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					Approval
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="success">
						<a href="javascript:approve()"><i class="fa fa-check"></i> อนุมัติ</a>
					</li>
					<li class="danger">
						<a href="javascript:reject()"><i class="fa fa-times"></i> ปฏิเสธ</a>
					</li>
				</ul>
			</div>
		<?php endif; ?>

	</div>
</div>
<hr/>
<?php if($doc->approve == 'R') : ?>
	<?php $this->load->view('reject_watermark'); ?>
<?php endif; ?>
<?php if($doc->status == 2) : ?>
	<?php $this->load->view('cancel_watermark'); ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center e" id="date_add" value="<?php echo thai_date($doc->doc_date); ?>" readonly disabled/>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="width-100" id="reference" value="<?php echo $doc->reference; ?>" disabled/>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_warehouse($doc->warehouse_id); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="width-100 text-center" id="user" value="<?php echo $doc->user; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center" value="<?php echo status_text($doc->status); ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Approval</label>
		<input type="text" class="width-100 text-center" value="<?php echo approval_text($doc->approve); ?>" disabled/>
	</div>
	<div class="col-lg-10 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="warehouse-id" value="<?php echo $doc->warehouse_id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1000px;">
      <thead>
        <tr>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-250 text-center">โซน</th>
          <th class="fix-width-100 text-center">เพิ่ม</th>
          <th class="fix-width-100 text-center">ลด</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? intval($rs->qty) : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>

	<?php if( ! empty($logs)) :?>
		<?php foreach($logs as $log) : ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 log-text text-right">
				<?php echo action_logs_label($log->action); ?> : <?php echo $log->user; ?> @ <?php echo thai_date($log->date_upd, TRUE); ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php $this->load->view('cancel_modal'); ?>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
