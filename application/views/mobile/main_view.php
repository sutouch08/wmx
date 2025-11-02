<?php $this->load->view('include/header_mobile'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		<h1>Hello! <?php echo get_cookie('displayName'); ?></h1>
		<h5>Good to see you here</h5>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider"></div>
</div>
<div class="row">
	<div class="menu-box">
		<div class="menu-card" style="border-color: #2196F3; color:#2196F3;" onclick="goTo('mobile/pick_list')">
			<span class="icon-badge text-center"><i class="fa fa-tasks fa-lg"></i><br/></span>
			<span class="menu-text text-left">Pick List</span>
		</div>

		<div class="menu-card" style="border-color: #2196F3; color:#2196F3;" onclick="goTo('mobile/prepare')">
			<span class="icon-badge text-center"><i class="fa fa-tasks fa-lg"></i><br/></span>
			<span class="menu-text text-left">จัดสินค้า</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/dispatch')">
			<span class="icon-badge text-center"><i class="fa fa-truck fa-lg"></i><br/></span>
			<span class="menu-text text-left">Dispatch</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/move')">
			<span class="icon-badge text-center"><i class="fa fa-exchange fa-lg"></i><br/></span>
			<span class="menu-text text-left">Move Stock</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/receive_po')">
			<span class="icon-badge text-center"><i class="fa fa-inbox fa-lg"></i><br/></span>
			<span class="menu-text text-left">รับจากใบสั่งซื้อ</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/receive_product')">
			<span class="icon-badge text-center"><i class="fa fa-inbox fa-lg"></i><br/></span>
			<span class="menu-text text-left">รับสินค้าเข้า</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/return_order')">
			<span class="icon-badge text-center"><i class="fa fa-inbox fa-lg"></i><br/></span>
			<span class="menu-text text-left">ลดหนี้ขาย</span>
		</div>

		<div class="menu-card" style="border-color: #4CAF50; color:#4CAF50;" onclick="goTo('mobile/consign_order')">
			<span class="icon-badge text-center"><i class="fa fa-inbox fa-lg"></i><br/></span>
			<span class="menu-text text-left">ตัดยอดฝากขาย (WM)</span>
		</div>
	</div>
</div>

<div id="backdrop" class="backdrop" onclick="toggleUsermenu()"></div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu <?php echo $tab == 'home' ? 'active' : ''; ?>">
				<span class="pg-icon" onclick="goTo('mobile/main)">
					<i class="fa fa-home fa-2x"></i><span>Home</span>
				</span>
			</div>
			<div class="footer-menu <?php echo $tab == 'pick_list' ? 'active' : ''; ?>">
				<span class="pg-icon" onclick="goTo('mobile/pick_list')">
					<i class="fa fa-tasks fa-2x"></i><span>Pick List</span>
				</span>
			</div>

			<div class="footer-menu <?php echo $tab == 'prepare' ? 'active' : ''; ?>">
				<span class="pg-icon" onclick="goTo('mobile/prepare')">
					<i class="fa fa-tasks fa-2x"></i><span>จัดสินค้า</span>
				</span>
			</div>

			<div class="footer-menu <?php echo $tab == 'dispatch' ? 'active' : ''; ?>">
				<span class="pg-icon" onclick="goTo('mobile/dispatch')">
					<i class="fa fa-truck fa-2x"></i><span>Dispatch</span>
				</span>
			</div>

			<div class="footer-menu">
				<span class="pg-icon" onclick="toggleUsermenu()">
					<i class="fa fa-user fa-2x"></i><span>Account</span>
				</span>
			</div>
		</div>
		<input type="hidden" id="menu" value="hide" />
 </div>
</div>

<?php $this->load->view('include/user-menu'); ?>
<?php $this->load->view('include/footer_mobile'); ?>
