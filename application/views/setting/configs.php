<?php $this->load->view('include/header'); ?>
<style>
	.li-block {
		border-bottom: solid 1px #ccc;
		background-color: #f5f5f5;
	}

</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:0px;" />

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 padding-5 padding-right-0" style="padding-right:0; height:600px; overflow:auto;">
		<?php
		$tab1 = $tab == 'general' ? 'active in' : '';
		$tab2 = $tab == 'system' ? 'active in' : '';
		$tab3 = $tab == 'inventory' ? 'active in' : '';
		$tab4 = $tab == 'order' ? 'active in' : '';
		$tab5 = $tab == 'wmx' ? 'active in' : '';
		$tab6 = $tab == 'wrx' ? 'active in' : '';
		$tab7 = $tab == 'porlor' ? 'active in' : '';
		$tab8 = $tab == 'spx' ? 'active in' : '';
		?>
		<ul id="myTab1" class="setting-tabs" style="margin-left:0;">
			<li class="li-block <?php echo $tab1; ?>" onclick="changeURL('general')"><a href="#general" data-toggle="tab">ทั่วไป</a></li>
			<li class="li-block <?php echo $tab2; ?>" onclick="changeURL('system')"><a href="#system" data-toggle="tab">ระบบ</a></li>
			<li class="li-block <?php echo $tab3; ?>" onclick="changeURL('inventory')"><a href="#inventory" data-toggle="tab">คลังสินค้า</a></li>
			<li class="li-block <?php echo $tab4; ?>" onclick="changeURL('order')"><a href="#order" data-toggle="tab">ออเดอร์</a></li>
			<li class="li-block <?php echo $tab5; ?>" onclick="changeURL('wmx')"><a href="#wmx" data-toggle="tab">WMS API</a></li>
			<li class="li-block <?php echo $tab6; ?>" onclick="changeURL('wrx')"><a href="#wrx" data-toggle="tab">WRX API</a></li>
			<li class="li-block <?php echo $tab7; ?>" onclick="changeURL('porlor')"><a href="#porlor" data-toggle="tab">PORLOR API</a></li>
			<li class="li-block <?php echo $tab8; ?>" onclick="changeURL('spx')"><a href="#spx" data-toggle="tab">SPX API</a></li>
		</ul>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 border-1" style="padding-top:15px; border-top:0px !important; height:600px; overflow:auto;">
		<div class="tab-content" style="border:0px;">
			<div class="tab-pane fade <?php echo $tab1; ?>" id="general">
				<?php $this->load->view('setting/setting_general'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab2; ?>" id="system">
				<?php	$this->load->view('setting/setting_system'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab3; ?>" id="inventory">
				<?php $this->load->view('setting/setting_inventory'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab4; ?>" id="order">
				<?php $this->load->view('setting/setting_order'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab5; ?>" id="wmx">
				<?php $this->load->view('setting/setting_wmx_api'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab6; ?>" id="wrx">
				<?php $this->load->view('setting/setting_wrx_api'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab7; ?>" id="porlor">
				<?php $this->load->view('setting/setting_porlor_api'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab8; ?>" id="spx">
				<?php //$this->load->view('setting/setting_spx_api'); ?>
			</div>
		</div><!--/ tab-content-->
	</div><!--/ col-sm-9  -->
</div><!--/ row  -->


<script src="<?php echo base_url(); ?>scripts/setting/setting.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
