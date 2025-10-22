<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				<i class="ace-icon fa fa-flash icon-on-left"></i>
				Actions
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="info">
					<a href="javascript:getSample()"><i class="fa fa-download"></i>&nbsp; Template file</a>
				</li>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && ($doc->status == 'P' OR $doc->status == 'A')) : ?>
					<li class="primary">
						<a href="javascript:getUploadFile()"><i class="fa fa-upload"></i>&nbsp; Import Excel</a>
					</li>
					<li class="success">
						<a href="javascript:confirmSave('S')"><i class="fa fa-save"></i>&nbsp; Save</a>
					</li>
					<?php if($this->pm->can_approve) : ?>
						<li class="success">
							<a href="javascript:confirmSave('A')"><i class="fa fa-save"></i>&nbsp; Save And Approve</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if($this->pm->can_delete && ($doc->status == 'P' OR $doc->status == 'A')) : ?>
					<li class="danger">
						<a href="javascript:cancel()"><i class="fa fa-times"></i>&nbsp; Cancel</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit r" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Posting date</label>
    <input type="text" class="form-control input-sm text-center edit r" id="posting-date" value="<?php echo thai_date($doc->shipped_date); ?>" readonly disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit r" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled/>
	</div>
  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-8 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm edit r" id="customer-name" value="<?php echo $doc->customer_name; ?>" readonly disabled />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>GP (%)</label>
		<input type="number" class="form-control input-sm text-center edit r" id="gp" value="<?php echo $doc->gp; ?>" disabled />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-9 padding-5">
    <label>คลังฝากขายแท้</label>
		<select class="width-100 edit r" id="warehouse" onchange="updateCustomer()" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_consign_warehouse($doc->warehouse_code); ?>
		</select>
  </div>
  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
  </div>

	<?php if($this->pm->can_edit) : ?>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
			<label class="not-show">Submit</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"></i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
	<?php endif; ?>
</div>
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="auz" id="auz" value="0">
<hr class="margin-top-15">
<?php $this->load->view('account/consign_order/consign_order_control'); ?>
<?php $this->load->view('account/consign_order/consign_order_detail'); ?>


<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:600px; max-width:95vw;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">Import File</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="input-group width-100">
							<input type="text" class="form-control" id="show-file-name" placeholder="กรุณาเลือกไฟล์ Excel" readonly />
							<span class="input-group-btn">
								<button type="button" class="btn btn-white btn-default"  onclick="getFile()">เลือกไฟล์</button>
							</span>
						</div>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
			 </div>
			<div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('upload-modal')">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-primary btn-100" onclick="uploadfile()">นำเข้า</button>
			</div>
	 </div>
 </div>
</div>

<script id="warehouse-template" type="text/x-handlebarsTemplate">
	<option value="">เลือก</option>
	{{#each this}}
		<option value="{{code}}" {{selected}}>{{code}} | {{name}}</option>
	{{/each}}
</script>
<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
