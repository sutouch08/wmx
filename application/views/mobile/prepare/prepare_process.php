<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/prepare/process_style'); ?>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $order->code; ?></div>
	<div class="header-info-icon"><a href="javascript:toggleHeader()"><i class="fa fa-info white"></i></a></div>
</div>
<?php $this->load->view('mobile/prepare/header_panel'); ?>
<?php $this->load->view('mobile/prepare/prepare_control'); ?>
<?php $this->load->view('mobile/prepare/incomplete_list');  ?>
<?php $this->load->view('mobile/prepare/complete_list'); ?>
<?php $this->load->view('mobile/prepare/process_menu'); ?>

<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="warehouse_code" value="<?php echo $order->warehouse_code; ?>" />
<input type="hidden" id="zone_code" />
<input type="hidden" id="finished" value="<?php echo $finished ? 1 : 0; ?>" />

<script id="incomplete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 incomplete-item" id="incomplete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap b-click " id="b-click-{{id}}">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{stock_in_zone}}</div>
    </div>
    <span class="badge-qty" id="badge-qty-{{id}}">{{balance}}</span>
  </div>
</script>

<script id="complete-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 complete-item" id="complete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap">{{barcode}}</div>
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span class="width-30" id="order-qty-{{id}}">{{qty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="width-30" id="prepared-qty-{{id}}">{{prepared}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span class="width-30" id="balance-qty-{{id}}">{{balance}}</span></div>
      </div>
      <div class="margin-bottom-3 pre-wrap">Location : {{{from_zone}}}</div>
    </div>
    <button type="button" class="btn btn-mini btn-danger"
      style="position:absolute; top:5px; right:5px; border-radius:4px !important;"
      onclick="removeBuffer('{{order_code}}', '{{product_code}}', '{{id}}')">
    <i class="fa fa-trash"></i>
  </button>
  </div>
</script>

<script src="<?php echo base_url(); ?>scripts/mobile/prepare/prepare.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/prepare/prepare_process.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer_mobile'); ?>
