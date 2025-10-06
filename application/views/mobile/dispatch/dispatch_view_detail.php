<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/dispatch/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?>&nbsp; [ <?php echo status_text($doc->status); ?> ]</div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/dispatch/header_panel'); ?>
<div class="row">
  <div class="page-wrap">
    <?php if( ! empty($details)) : ?>
      <?php $channels = get_channels_array(); ?>
      <?php $no = 1; ?>
  		<?php $totalQty = 0; ?>
  		<?php $totalShipped = 0; ?>
      <?php foreach($details as $rs) : ?>
        <?php $channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?>
        <div class="list-block dispatch-row" data-id="<?php echo $rs->id; ?>" id="dispatch-<?php echo $rs->id; ?>">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no" id="no-<?php echo $rs->id; ?>"><?php echo $no; ?></div>
							<div class="display-inline-block width-100">
								<span class="float-left font-size-11 width-50">Order : <?php echo $rs->order_code; ?></span>
								<span class="float-left font-size-11 width-50">Ref : <?php echo $rs->reference; ?></span>
                <span class="float-left font-size-11 width-50">Channels : <?php echo $channels_name; ?></span>
								<span class="float-left font-size-11 width-50">Tracking : <?php echo $rs->tracking_no; ?></span>
								<span class="float-left font-size-11 width-50">กล่อง [ทั้งหมด] : &nbsp;&nbsp; <?php echo number($rs->carton_qty); ?></span>
								<span class="float-left font-size-11 width-50">กล่อง [ยิงแล้ว] : &nbsp;&nbsp;<?php echo number($rs->carton_shipped); ?></span>
							</div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php $this->load->view('mobile/dispatch/view_menu'); ?>

<script>
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
