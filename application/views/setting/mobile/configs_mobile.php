<?php $this->load->view('include/header'); ?>
<?php $this->load->view('setting/mobile/style'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>

<div class="row">
	<div class="setting-menu">
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('general')">General <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
		<?php if($cando) : ?>
			<div class="menu-block">
				<a class="menu-link" href="javascript:goSetting('system')">System <i class="fa fa-angle-right pull-right font-size-20"></i></a>
			</div>
		<?php endif; ?>
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('inventory')">Inventory <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('order')">Orders <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
		<!-- <div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('document')">Documents <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div> -->
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('wmx')">WMS API <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('wrx')">WRX API <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
		<div class="menu-block">
			<a class="menu-link" href="javascript:goSetting('porlor')">PORLOR API <i class="fa fa-angle-right pull-right font-size-20"></i></a>
		</div>
	</div>
</div><!--/ row  -->

<div class="setting-panel move-out" id="general">
	<?php $this->load->view('setting/mobile/setting_general_mobile'); ?>
</div>
<div class="setting-panel move-out" id="system">
	<?php $this->load->view('setting/mobile/setting_system_mobile'); ?>
</div>
<div class="setting-panel move-out" id="inventory">
	<?php $this->load->view('setting/mobile/setting_inventory_mobile'); ?>
</div>
<div class="setting-panel move-out" id="order">
	<?php $this->load->view('setting/mobile/setting_order_mobile'); ?>
</div>
<!-- <div class="setting-panel move-out" id="document">
	<?php //$this->load->view('setting/mobile/setting_document_mobile'); ?>
</div> -->
<div class="setting-panel move-out" id="wrx">
	<?php $this->load->view('setting/mobile/setting_wrx_api_mobile'); ?>
</div>
<div class="setting-panel move-out" id="wmx">
	<?php $this->load->view('setting/mobile/setting_wmx_api_mobile'); ?>
</div>
<div class="setting-panel move-out" id="porlor">
	<?php $this->load->view('setting/mobile/setting_porlor_api_mobile'); ?>
</div>

<script src="<?php echo base_url(); ?>scripts/setting/setting_mobile.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
