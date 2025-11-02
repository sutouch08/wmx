<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				<i class="ace-icon fa fa-list icon-on-left"></i>ตัวเลือก
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="info">
					<a href="javascript:printTransfer('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> &nbsp; พิมพ์ใบส่งของ</a>
				</li>
        <?php if($doc->status != 'D') : ?>
    			<?php if($doc->status != 'C' && $this->pm->can_delete OR $this->_SuperAdmin) : ?>
            <li class="danger">
    					<a href="javascript:goCancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> &nbsp; ยกเลิก</a>
    				</li>
    			<?php endif; ?>
          <?php if($doc->status == 'C' && $doc->is_export == 0 && is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_TR_INTERFACE'))) : ?>
            <li class="success">
    					<a href="javascript:sendToERP('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> &nbsp; Send To ERP</a>
    				</li>
          <?php endif; ?>
    		<?php endif; ?>
        <?php if($doc->status == 'P' && $this->pm->can_edit) : ?>
          <li class="warning">
            <a href="javascript:edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> &nbsp; แก้ไข</a>
          </li>
        <?php endif; ?>
			</ul>
		</div>
	</div>
</div><!-- End Row -->
<hr/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-4 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center h" id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-4 col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-4 col-xs-4 padding-5">
    <label>Posting date</label>
    <input type="text" class="form-control input-sm text-center edit h" name="date" id="posting-date" value="<?php echo thai_date($doc->shipped_date); ?>" readonly disabled />
  </div>

  <div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-12 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm" id="fromWhs" value="<?php echo $doc->from_warehouse.' | '.warehouse_name($doc->from_warehouse); ?>" disabled />
    <input type="hidden" id="from-warehouse" value="<?php echo $doc->from_warehouse; ?>" />
	</div>

  <div class="col-lg-4-harf col-md-3-harf col-sm-8 col-xs-12 padding-5">
    <label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm" id="toWhs" value="<?php echo $doc->to_warehouse.' | '.warehouse_name($doc->to_warehouse); ?>" disabled />
    <input type="hidden" id="to-warehouse" value="<?php echo $doc->to_warehouse; ?>" />
  </div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit h" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>


  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label>Status</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo trStatusText($doc->status); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label>ERP No.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled />
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:750px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-200">รหัส</th>
          <th class="min-width-300">สินค้า</th>
          <th class="fix-width-150">ต้นทาง</th>
          <th class="fix-width-100 text-center">จำนวน</th>
        </tr>
      </thead>
      <tbody id="transfer-table">
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
        <?php $total_wms = 0; ?>
        <?php if( ! empty($details)) : ?>
          <?php $zoneName = []; ?>
          <?php foreach($details as $rs) : 	?>
            <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
              <td class="middle"><?php echo $rs->from_zone; ?></td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
            </tr>
            <?php $total_qty += $rs->qty; ?>
            <?php $no++; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="4" class="text-right">รวม</td>
            <td class="text-center"><?php echo number($total_qty); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<?php if($doc->status != 'D') : ?>
	<?php $this->load->view('cancel_modal'); ?>
<?php endif; ?>

<?php if($doc->status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-">
			<p class="log-text">
				ยกเลิกโดย : <?php echo $doc->cancel_user; ?> @ <?php echo thai_date($doc->date_upd, TRUE); ?><br/>
				หมายเหตุ : <?php echo $doc->cancel_reason; ?>
			</p>
		</div>
	</div>
<?php endif; ?>


<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
