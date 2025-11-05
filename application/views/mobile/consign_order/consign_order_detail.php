<?php $this->load->view('include/header_mobile'); ?>
<style>
	.page-wrap.listing {
		height: calc(100vh - 170px);
	}
</style>

<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?> [<?php echo status_text($doc->status); ?>]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>

<div class="row">
  <div class="page-wrap listing" id="detail-table">
    <?php $no = 1; ?>
		<?php $totalQty = 0; ?>
		<?php $totalAmount = 0; ?>
    <?php if( ! empty($details)) : ?>
      <?php foreach($details as $rs) : ?>
        <div class="list-block" id="list-block-<?php echo $rs->id; ?>" onclick="toggleActive(<?php echo $rs->id; ?>)">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no" id="no-<?php echo $rs->id; ?>"><?php echo $no; ?></div>
							<input type="checkbox" class="chk hide"
							id="list-<?php echo $rs->id; ?>"
							data-code="<?php echo $rs->product_code; ?>"
							data-name="<?php echo $rs->product_name; ?>"
							value="<?php echo $rs->id; ?>"/>
							<div class="display-inline-block width-100">
								<span class="display-block font-size-12"><?php echo $rs->product_code; ?></span>
								<span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
								<span class="float-left font-size-11 width-20">Price:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 price"
								id="price-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->price, 2); ?>" readonly/>
								<span class="float-left font-size-11 width-20">Discount:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 disc"
								id="disc-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo $rs->discount; ?>" readonly/>
								<span class="float-left font-size-11 width-20">QTY:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 qty"
								id="qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->qty); ?>" readonly/>
								<span class="float-left font-size-11 width-20">Amnt:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 amount"
								id="amount-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->amount, 2); ?>" readonly/>
							</div>
            </div>
          </div>
        </div>
				<?php $totalQty += $rs->qty; ?>
				<?php $totalAmount += $rs->amount; ?>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>" />
</div>

<div class="pg-summary">
	<div class="pg-summary-inner">
		<div class="pg-summary-content">
			<div class="summary-text width-50">
				<span class="float-left font-size-16 width-30">QTY.</span>
				<input type="text"
				class="float-left font-size-16 text-label padding-0 width-70 text-center"
				style="color:white !important;"
				id="total-qty"
				value="<?php echo number($totalQty); ?>" readonly />
			</div>
			<div class="summary-text width-50">
				<span class="float-left font-size-16 width-30">Amnt.</span>
				<input type="text"
				class="float-left font-size-16 text-label padding-0 width-70 text-right"
				style="color:white !important;"
				id="total-amount"
				value="<?php echo number($totalAmount, 2); ?>" readonly />
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('mobile/consign_order/header_panel'); ?>
<?php $this->load->view('cancel_modal'); ?>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="goTo('main')">
					<i class="fa fa-home fa-2x"></i><span>หน้าหลัก</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
      <?php if($this->pm->can_approve && $doc->status == 'A') : ?>
  			<div class="footer-menu">
  				<span class="pg-icon" onclick="doApprove('<?php echo $doc->code; ?>')">
  					<i class="fa fa-check fa-2x"></i><span>อนุมัติ</span>
  				</span>
  			</div>
      <?php endif; ?>
      <?php if($this->pm->can_edit && ($doc->status == 'P' OR $doc->status == 'A')) : ?>
  			<div class="footer-menu">
  				<span class="pg-icon" onclick="goEdit('<?php echo $doc->code; ?>')">
  					<i class="fa fa-pencil fa-2x"></i><span>แก้ไข</span>
  				</span>
  			</div>
      <?php endif; ?>
      <?php if($this->pm->can_delete && $doc->is_exported != 'Y' && $doc->status != 'P') : ?>
  			<div class="footer-menu">
  				<span class="pg-icon" onclick="rollback('<?php echo $doc->code; ?>')">
  					<i class="fa fa-history fa-2x"></i><span>Rollback</span>
  				</span>
  			</div>
      <?php endif; ?>
      <?php if($this->pm->can_delete && $doc->is_exported != 'Y' && $doc->status != 'D') : ?>
  			<div class="footer-menu">
  				<span class="pg-icon" onclick="confirmCancel('<?php echo $doc->code; ?>')">
  					<i class="fa fa-exclamation-triangle fa-2x"></i><span>ยกเลิก</span>
  				</span>
  			</div>
      <?php endif; ?>
      <?php if($doc->status == 'C' && is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGN_INTERFACE'))) : ?>
  			<div class="footer-menu">
  				<span class="pg-icon" onclick="sendToErp('<?php echo $doc->code; ?>')">
  					<i class="fa fa-send fa-2x"></i><span>Export</span>
  				</span>
  			</div>
      <?php endif; ?>
		</div>
 </div>
</div>

<div class="more-menu run-out" id="more-menu">
	<div class="more-menu-close">
    <span class="more-menu-close-icon" onclick="closeMore()">
			<i class="fa fa-times fa-2x"></i>
		</span>
  </div>
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="cancel('<?php echo $doc->code; ?>', '<?php echo $doc->status; ?>')">
			<i class="fa fa-exclamation-triangle fa-2x"></i><span>ยกเลิก</span>
		</span>
	</div>
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="confirmSave()">
			<i class="fa fa-save fa-2x"></i><span>บันทึก</span>
		</span>
	</div>
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="removeRow()">
			<i class="fa fa-trash fa-2x"></i><span>ลบ</span>
		</span>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/mobile/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
