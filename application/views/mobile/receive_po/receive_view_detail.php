<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/receive_po/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="leave()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?> &nbsp; [<?php echo receive_status_text($doc->status); ?>]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/receive_po/header_panel'); ?>
<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-45 text-right"
        style="color:white !important;"
        id="all-qty" value="<?php echo number($totalReceive); ?>" readonly />
        <span class="float-left font-size-16 text-center width-10">/</span>
        <input type="text"
        class="float-left font-size-16 text-label padding-0 width-45 text-left"
        style="color:white !important;"
        id="total-qty" value="<?php echo number($allQty); ?>" readonly />
      </div>
    </div>
  </div>
</div>

<?php $controlHeight = 190; ?>

<?php if(($doc->status == 'R' OR $doc->status == 'O') && $finished) : ?>
	<?php $controlHeight = 250; ?>
	<div id="control-box">
	  <div class="control-box-inner">
	    <div class="width-100 padding-top-15>" style="height:50px;" id="close-bar">
	      <button type="button" class="btn btn-lg btn-white btn-success btn-block" onclick="finishReceive()"><i class="fa fa-check-square-o"></i> รับเสร็จแล้ว</button>
	    </div>
	  </div>
	</div>
<?php endif; ?>
<div class="width-100 text-center bottom-info hide-text" style="border-top:solid 1px #ddd;" id="zone-name"><?php echo $zone->code." | ".$zone->name; ?></div>

<div class="row">
  <div class="page-wrap" id="incomplete-box" style="height: calc(100vh - <?php echo $controlHeight; ?>px);">
    <?php  if(!empty($incomplete)) : ?>
      <?php   foreach($incomplete as $rs) : ?>
        <div class="list-block receive-item unvalid">
          <div class="list-link">
            <div class="list-link-inner width-100">
              <div class="width-100">
                <span class="display-block font-size-11 b-click"><?php echo $rs->barcode; ?></span>
                <span class="display-block font-size-11">SKU : <?php echo $rs->product_code; ?></span>
                <span class="display-block font-size-11">Description : <?php echo $rs->product_name; ?></span>
								<span class="display-block font-size-14">QTY : <?php echo number($rs->receive_qty); ?> / <?php echo number($rs->qty); ?></span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php  if(!empty($complete)) : ?>
      <?php   foreach($complete as $rs) : ?>
        <div class="list-block receive-item valid">
          <div class="list-link">
            <div class="list-link-inner width-100">
              <div class="width-100">
                <span class="display-block font-size-11">SKU : <?php echo $rs->product_code; ?></span>
                <span class="display-block font-size-11">Description : <?php echo $rs->product_name; ?></span>
								<span class="display-block font-size-14">QTY : <?php echo number($rs->receive_qty); ?> / <?php echo number($rs->qty); ?></span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="doRefresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="allList()">
					<i class="fa fa-tasks fa-2x"></i><span>Listing</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="pendingList()">
					<i class="fa fa-tasks fa-2x"></i><span>Pending</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="leave('process')">
					<i class="fa fa-tasks fa-2x"></i><span>Receiveing</span>
				</span>
			</div>
			<?php if($doc->status == 'O' OR $doc->status == 'R') : ?>
				<div class="footer-menu">
					<span class="pg-icon" onclick="goProcess('<?php echo $doc->code; ?>')">
						<i class="fa fa-external-link fa-2x"></i><span>รับเข้า</span>
					</span>
				</div>
			<?php endif; ?>
		</div>
 </div>
</div>


<script src="<?php echo base_url(); ?>scripts/mobile/receive_po/receive_po.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/receive_po/receive_po_process.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
