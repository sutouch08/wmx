<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/move/move_process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?>&nbsp; [ <?php echo status_text($doc->status); ?> ]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/move/header_panel'); ?>
<?php if($doc->status == 'D') { $this->load->view('cancle_watermark'); } ?>
<?php $totalQty = 0; ?>
<div class="row">
  <div class="page-wrap" style="position:fixed; left:0px; top:85px; height: calc(100vh - 160px);">
    <?php if( ! empty($details)) : ?>
      <?php $no = 1; ?>
      <?php foreach($details as $rs) : ?>
        <div class="list-block dispatch-row">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no"><?php echo $no; ?></div>
							<div class="width-100">
								<span class="display-block font-size-11"><?php echo $rs->product_code; ?></span>
								<span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
                <span class="display-block font-size-11 blue">From : <?php echo $rs->from_zone; ?></span>
								<span class="display-block font-size-11 green">To : <?php echo $rs->to_zone; ?></span>
								<span class="display-block font-size-12">จำนวน : &nbsp;&nbsp; <?php echo number($rs->qty); ?></span>
								<?php if($rs->valid == 1) : ?>
									<span class="valid-icon"><?php echo is_active($rs->valid); ?></span>
								<?php endif; ?>
							</div>
            </div>
          </div>
        </div>
				<?php $totalQty += $rs->qty; ?>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <span class="float-left font-size-16 width-50">รายการโอนย้ายทั้งหมด</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-50 text-right"
        style="color:white !important;"
        id="move-total" value="<?php echo number($totalQty); ?> &nbsp; PCS." readonly />
      </div>
    </div>
  </div>
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
				<span class="pg-icon" onclick="goBack()">
					<i class="fa fa-tasks fa-2x"></i><span>Move List</span>
				</span>
			</div>
			<?php if($doc->status != 'D' && $this->pm->can_delete) : ?>
				<div class="footer-menu">
					<span class="pg-icon" onclick="confirmCancel()">
						<i class="fa fa-exclamation-triangle fa-2x"></i><span>ยกเลิก</span>
					</span>
				</div>
			<?php endif; ?>
			<?php if($doc->status != 'P' && $this->pm->can_delete) : ?>
				<div class="footer-menu">
					<span class="pg-icon" onclick="rollback()">
						<i class="fa fa-history fa-2x"></i><span>ย้อนสถานะ</span>
					</span>
				</div>
			<?php endif; ?>
		</div>
 </div>
</div>

<script src="<?php echo base_url(); ?>scripts/mobile/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/move/move_control.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
