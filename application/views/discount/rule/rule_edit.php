
<?php $this->load->view('include/header'); ?>
<?php $ac = $rule->active == 1 ? 'btn-success' : ''; ?>
<?php $dc = $rule->active == 0 ? 'btn-danger' : ''; ?>
<?php $id = $rule->id; ?>
<?php
  $tab1 = $tab == 'discount' ? 'active in' : '';
  $tab2 = $tab == 'customer' ? 'active in' : '';
  $tab3 = $tab == 'product' ? 'active in' : '';
  $tab4 = $tab == 'channels' ? 'active in' : '';
  $tab5 = $tab == 'payment' ? 'active in' : '';
?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>

<div class="row top-row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h4 class="title"></i><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div>
<hr/>

<div class="row hidden-xs">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" id="txt-policy" value="<?php echo $rule->code; ?>" disabled />
  </div>
  <div class="col-lg-6 col-md-7 col-sm-7 col-xs-8 padding-5">
    <label>ชื่อเงื่อนไข</label>
    <input type="text" class="form-control input-sm" maxlength="150" id="txt-rule-name" value="<?php echo $rule->name; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Active</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm <?php echo $ac; ?> width-50" id="btn-active-rule" onclick="activeRule()">
        <i class="fa fa-check"></i>
      </button>
      <button type="button" class="btn btn-sm <?php echo $dc; ?> width-50" id="btn-dis-rule" onclick="disActiveRule()">
        <i class="fa fa-times"></i>
      </button>
    </div>
  </div>
  <?php if($this->pm->can_add) : ?>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">add</label>
      <button type="button" class="btn btn-xs btn-success btn-block" id="btn-update" onclick="updateRule()">Update</button>
    </div>
  <?php endif; ?>
</div>
<input type="hidden" id="id_rule" value="<?php echo $rule->id; ?>" />
<input type="hidden" id="isActive" value="<?php echo $rule->active; ?>" />

<hr class="hidden-xs"/>

<div class="row hidden-xs">
  <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 padding-top-15 hidden-xs">
    <ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
      <li class="li-block <?php echo $tab1; ?>">
        <a href="#discount" data-toggle="tab" onclick="changeURL('<?php echo $rule->id; ?>','discount')">Discount</a>
      </li>
      <li class="li-block <?php echo $tab2; ?>">
        <a href="#customer" data-toggle="tab" onclick="changeURL('<?php echo $rule->id; ?>','customer')">Customers</a>
      </li>
      <li class="li-block <?php echo $tab3; ?>">
        <a href="#product" data-toggle="tab" onclick="changeURL('<?php echo $rule->id; ?>','product')">Products</a>
      </li>
      <li class="li-block <?php echo $tab4; ?>">
        <a href="#channels" data-toggle="tab" onclick="changeURL('<?php echo $rule->id; ?>','channels')">Channels</a>
      </li>
      <li class="li-block <?php echo $tab5; ?>" onclick="changeURL('<?php echo $rule->id; ?>','payment')">
        <a href="#payment" data-toggle="tab">Payments</a>
      </li>
    </ul>
  </div>

  <div class="col-lg-10-harf col-md-10 col-sm-10 col-xs-12 padding-5" id="content-block" style="border-left:solid 1px #ccc; min-height:600px; max-height:1000px;">
    <div class="tab-content" style="border:0;">
      <div class="tab-pane fade <?php echo $tab1; ?>" id="discount">
        <?php $this->load->view('discount/rule/discount_rule'); ?>
      </div>
      <div class="tab-pane fade <?php echo $tab2; ?>" id="customer">
        <?php $this->load->view('discount/rule/customer_rule'); ?>
      </div>
      <div class="tab-pane fade <?php echo $tab3; ?>" id="product">
        <?php $this->load->view('discount/rule/product_rule'); ?>
      </div>
      <div class="tab-pane fade <?php echo $tab4; ?>" id="channels">
        <?php $this->load->view('discount/rule/channels_rule'); ?>
      </div>
      <div class="tab-pane fade <?php echo $tab5; ?>" id="payment">
        <?php $this->load->view('discount/rule/payment_rule'); ?>
      </div>

    </div>
  </div><!--/ col-sm-9  -->
</div><!--/ row  -->
<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not support mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/channels_tab.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/payment_tab.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/customer_tab.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/product_tab.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/discount_tab.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
