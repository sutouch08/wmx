<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-20 <?php echo $tab == 'all' ? 'active' : ''; ?>">
				<span class="width-100" onclick="viewAll()">
					<i class="fa fa-cubes fa-2x"></i><span>View All</span>
				</span>
			</div>
			<div class="footer-menu width-20 <?php echo $tab == 'pending' ? 'active' : ''; ?>">
				<span class="width-100" onclick="viewPending()">
					<i class="fa fa-cube fa-2x"></i><span>Pending</span>
				</span>
			</div>

			<div class="footer-menu width-20 <?php echo $tab == 'process' ? 'active' : ''; ?>">
				<span class="width-100" onclick="viewProcess()">
					<i class="fa fa-cube fa-2x"></i><span>Receiving</span>
				</span>
			</div>

			<div class="footer-menu width-20">
				<span class="width-100" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>Refresh</span>
				</span>
			</div>

			<div class="footer-menu width-20">
				<span class="width-100" onclick="toggleFilter()">
					<i class="fa fa-bars fa-2x"></i><span>Filter</span>
				</span>
			</div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>
