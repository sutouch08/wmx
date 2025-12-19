<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/prepare/style'); ?>
<?php $this->load->view('mobile/prepare/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
      <?php $zName = []; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
        <?php $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code); ?>
        <?php if( ! empty($rs->zone_code) && empty($zName[$rs->zone_code])) : ?>
          <?php $zName[$rs->zone_code] = zone_name($rs->zone_code); ?>
        <?php endif; ?>
  			<?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
  			<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
        <?php $backorder = $rs->is_backorder ? 'has-error' : ''; ?>
        <div class="list-block <?php echo $backorder; ?>" onclick="goPrepare('<?php echo $rs->code; ?>')">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="width-100">
                <span class="display-block font-size-12">
                  <?php echo $rs->code.$cn_text; ?> <?php echo ( ! empty($rs->reference) ? "&nbsp;&nbsp;&nbsp; [ {$rs->reference} ]" : ""); ?>
                  <?php if($rs->is_backorder) : ?>
                    <span class="font-size-11 pull-right">[backorder]</span>
                  <?php endif; ?>
                </span>
                <span class="display-block font-size-11">วันที่ : <?php echo thai_date($rs->date_add, FALSE, '/'); ?></span>
                <span class="display-block font-size-11">ลูกค้า : <?php echo $customer_name; ?></span>
                <?php if( ! empty($rs->channels_name)) : ?>
                  <span class="display-block font-size-11">ช่องทางขาย : <?php echo $rs->channels_name; ?></span>
                <?php endif; ?>
                <?php if( ! empty($rs->zone_code)) : ?>
                  <span class="display-block font-size-11">โซน : <?php echo $zName[$rs->zone_code]; ?></span>
                <?php endif; ?>
                <span class="display-block font-size-11">
                  <span class="float-left width-50">SO: <?php echo $rs->so_no; ?></span>
                  <span class="float-left width-50 text-right">Fulfil:  <?php echo $rs->fulfillment_code; ?></span>
                </span>
                <span class="display-block font-size-11">จำนวน : <?php echo number($rs->qty); ?></span>
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="paginater">
  <div class="paginater-toggle"><i class="fa fa-angle-up fa-lg"></i></div>
	<?php echo $this->pagination->create_links(); ?>
</div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="goTo('mobile/main')">
					<i class="fa fa-home fa-2x"></i><span>หน้าหลัก</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="toggleOrderScanBox()">
					<i class="fa fa-qrcode fa-2x"></i><span>สแกน</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="goTo('mobile/prepare/view_process')">
					<i class="fa fa-tasks fa-2x"></i><span>กำลังจัด</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="resetFilter('prepareList')">
					<i class="fa fa-times fa-2x"></i><span>เคลียร์</span>
				</span>
			</div>

			<div class="footer-menu">
				<span class="pg-icon" onclick="toggleFilter()">
					<i class="fa fa-search fa-2x"></i><span>ค้นหา</span>
				</span>
			</div>
		</div>
 </div>
</div>

<div class="order-scan-box slide-out" id="order-scan-box">
	<div class="width-100">
		<span class="width-100">
			<input type="text" class="form-control input-lg focus"
			style="padding-left:15px; padding-right:40px;" id="barcode-order" inputmode="none" placeholder="Barcode Order" autocomplete="off">
			<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:20px; right:22px; color:grey;"></i>
		</span>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/mobile/prepare/prepare.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
