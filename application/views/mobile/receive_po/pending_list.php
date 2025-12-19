<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/receive_po/style'); ?>
<?php $this->load->view('mobile/receive_po/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
        <div class="list-block <?php echo status_color($rs->status); ?>" onclick="goProcess('<?php echo $rs->code; ?>')">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no"><?php echo $no; ?></div>
              <div class="width-100">
                <span class="display-block font-size-12"><?php echo $rs->code; ?> &nbsp;&nbsp; - &nbsp;&nbsp; <?php echo receive_status_text($rs->status); ?> <span class="pull-right"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></span>
                <span class="display-block font-size-11">Vender : <?php echo $rs->vender_name; ?></span>
                <?php if( ! empty($rs->po_code)) : ?>
                  <span class="float-left font-size-11 width-50">PO No : <?php echo $rs->po_code; ?></span>
                <?php endif; ?>
                <?php if( ! empty($rs->invoice_code)) : ?>
                  <span class="float-left font-size-11 width-50">Invoice No : <?php echo $rs->invoice_code; ?></span>
                <?php endif; ?>
                <span class="display-block font-size-11">
                  <span class="float-left width-50">Owner : <?php echo $rs->user; ?></span>
                  <span class="float-left width-50">โซน :  <?php echo $rs->zone_code; ?></span>
                </span>
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="width-100 text-center">--- ไม่พบรายการ ---</div>
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
				<span class="pg-icon" onclick="allList()">
					<i class="fa fa-cubes fa-2x"></i><span>ทั้งหมด</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="processList()">
					<i class="fa fa-cube fa-2x"></i><span>กำลังรับ</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="resetFilter('pending_list')">
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

<script src="<?php echo base_url(); ?>scripts/mobile/receive_po/receive_po.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
