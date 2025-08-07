<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($this->pm->can_delete && $doc->status != 'D' && ($doc->status != 'C' && $doc->DocNum == NULL OR $this->_SuperAdmin)) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="confirmCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
		<?php endif; ?>
		<?php if($doc->status == 'C') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToERP('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send To ERP</button>
		<?php endif; ?>
		<?php if($this->pm->can_edit && ($doc->status == 'P' OR $doc->status == 'A' OR $doc->status == 'R')) : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> Edit</button>
		<?php endif; ?>

		<?php if($doc->status == 'A' && $this->pm->can_approve == 1) : ?>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
					Approval
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="success">
						<a href="javascript:approve()"><i class="fa fa-check"></i> Approve</a>
					</li>
					<li class="danger">
						<a href="javascript:reject()"><i class="fa fa-times"></i> Reject</a>
					</li>
				</ul>
			</div>
		<?php endif; ?>
  </div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center e r" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
	</div>
	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 padding-5">
		<label>คลัง</label>
		<input type="text" class="width-100" id="warehouse-code" value="<?php echo $doc->warehouse_code.' | '.warehouse_name($doc->warehouse_code) ?>" disabled/>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-3-harf col-xs-6 padding-5">
		<label>อ้างถึง</label>
		<input type="text" class="width-100 e" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Owner</label>
		<input type="text" class="width-100 text-center" id="user" value="<?php echo $doc->user; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>สถานะ</label>
		<input type="text" class="width-100 text-center" id="status" value="<?php echo adjust_status_label($doc->status); ?>" disabled />
	</div>
	<div class="col-lg-9 col-md-7 col-sm-7 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ERP No.</label>
		<input type="text" class="width-100" id="doc-num" value="<?php echo $doc->DocNum; ?>" disabled />
	</div>
</div>
<hr class="margin-top-15 margin-bottom-15"/>

<?php if($doc->status == 'D') { $this->load->view('cancle_watermark'); } ?>
<?php if($doc->status == 'R') { $this->load->view('reject_watermark'); } ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:990px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-300">สินค้า</th>
					<th class="fix-width-250">โซน</th>
					<th class="fix-width-100 text-center">เพิ่ม</th>
					<th class="fix-width-100 text-center">ลด</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php if(!empty($details)) : ?>
					<?php   $no = 1;    ?>
					<?php   foreach($details as $rs) : ?>
						<tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle"><?php echo $rs->zone_code; ?></td>
							<td class="middle text-center" style="padding-top:3px; padding-bottom:3px;">
								<input type="number" class="width-100 text-label text-center qty-up"
								id="qty-up-<?php echo $rs->id; ?>"
								data-id="<?php echo $rs->id; ?>"
								data-zone="<?php echo $rs->zone_code; ?>"
								value="<?php echo $rs->qty > 0 ? intval($rs->qty) : 0 ; ?>" readonly />
							</td>
			        <td class="middle text-center" style="padding-top:3px; padding-bottom:3px;">
								<input type="number" class="width-100 text-label text-center qty-down"
								id="qty-down-<?php echo $rs->id; ?>"
								data-id="<?php echo $rs->id; ?>"
								data-zone="<?php echo $rs->zone_code; ?>"
								value="<?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>" readonly />
							</td>
						</tr>
						<?php     $no++; ?>
					<?php   endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <?php if( ! empty($logs)) : ?>
      <p class="log-text">
      <?php foreach($logs as $log) : ?>
        <?php echo "* ".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->user} &nbsp;|&nbsp; ".display_name($log->user)."  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
      <?php endforeach; ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<?php if($doc->status != 'D') { $this->load->view('cancle_modal'); } ?>


<script id="detail-template" type="text/x-handlebars-template">
<tr class="font-size-11 rox" id="row-{{id}}">
  <td class="middle text-center no">{{no}}</td>
  <td class="middle">{{ pdCode }}</td>
  <td class="middle">{{ pdName }}</td>
  <td class="middle text-center">{{ zoneName }}</td>
  <td class="middle text-center" id="qty-up-{{id}}">{{ up }}</td>
  <td class="middle text-center" id="qty-down-{{id}}">{{ down }}</td>
  <td class="middle text-center">
    {{#if valid}}
    <i class="fa fa-times red"></i>
    {{else}}
    <i class="fa fa-check green"></i>
    {{/if}}
  </td>
  <td class="middle text-right">
  <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteDetail({{ id }}, '{{ pdCode }}')">
      <i class="fa fa-trash"></i>
    </button>
  <?php endif; ?>
  </td>
</tr>
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
