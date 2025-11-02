<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/consign_order/style'); ?>
<?php $this->load->view('mobile/consign_order/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
        <div class="list-block <?php echo status_color($rs->status); ?>" onclick="viewDetail('<?php echo $rs->code; ?>')">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no"><?php echo $no; ?></div>
              <div class="width-100">
                <span class="display-block font-size-12">
                  <?php echo $rs->code; ?> &nbsp;&nbsp; - &nbsp;&nbsp; <?php echo status_text($rs->status); ?>
                  <span class="pull-right"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></span>
                </span>
                <span class="display-block font-size-11">Customer : <?php echo $rs->customer_code; ?> | <?php echo $rs->customer_name; ?></span>
                <span class="display-block font-size-11">Warehouse :  <?php echo $rs->warehouse_code; ?> | <?php echo warehouse_name($rs->warehouse_code); ?></span>
                <?php if($rs->status == 'D') : ?>
                  <span class="display-block font-size-11">Canceled By : <?php echo $rs->cancel_user; ?></span>
                <?php elseif($rs->status == 'C') : ?>
                  <span class="display-block font-size-11">Approve By : <?php echo $rs->update_user; ?></span>
                <?php endif; ?>
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
				<span class="pg-icon" onclick="addNew()">
					<i class="fa fa-plus fa-2x"></i><span>Add new</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="resetFilter('all_list')">
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

<script src="<?php echo base_url(); ?>scripts/mobile/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
