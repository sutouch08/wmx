<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 paddig-top-5">
    <h3 class="title"> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>				
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="form-horizontal">
  <div class="row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>เลขที่</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $po->code; ?>" disabled>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" disabled readonly required>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>ผู้ขาย</label>
			<input type="text" class="form-control input-sm text-center edit" name="vender_code" id="vender_code" value="<?php echo $po->vender_code; ?>" disabled required>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
			<label class="not-show">ผู้ขาย</label>
			<input type="text" class="form-control input-sm edit" name="vender_name" id="vender_name" value="<?php echo $po->vender_name; ?>" disabled required>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>กำหนดส่ง</label>
			<input type="text" class="form-control input-sm text-center edit" name="require_date" id="require_date" value="<?php echo thai_date($po->due_date); ?>" disabled readonly required>
		</div>

		<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $po->remark; ?>" disabled>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">
				<i class="fa fa-pencil"></i> แก้ไข
			</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()">
				<i class="fa fa-save"></i> Update
			</button>
		</div>
  </div>

	<input type="hidden" id="code" value="<?php echo $po->code; ?>">
	<input type="hidden" id="id" value="<?php echo $po->id; ?>">
</div>
<hr class="margin-top-15">

<?php if($po->status == 'P') : ?>
<?php $this->load->view('purchase/po/po_control'); ?>
<?php endif; ?>

<?php $this->load->view('purchase/po/po_detail'); ?>


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px; max-width:90vw;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body">
				 <div class="row" style="margin:0 !important;">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:60vh; padding:0; overflow:auto;" id="modalBody">

           </div>
         </div>
			 </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToPo()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/purchase/po.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
