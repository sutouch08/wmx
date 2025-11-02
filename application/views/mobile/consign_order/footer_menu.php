<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="goTo('main')">
					<i class="fa fa-home fa-2x"></i><span>หน้าหลัก</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
		<?php if($doc->status == 2) : ?>
			<div class="footer-menu">
				<span class="pg-icon" onclick="rollback()">
					<i class="fa fa-history fa-2x"></i><span>Rollback</span>
				</span>
			</div>
		<?php endif; ?>
		<?php if($doc->status == 0) : ?>
			<div class="footer-menu">
				<span class="pg-icon" onclick="startScan(getItemByBarcode)">
					<i class="fa fa-plus fa-2x"></i><span>เพิ่มรายการ</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="editRow()">
					<i class="fa fa-pencil fa-2x"></i><span>แก้ไข</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="toggleMore()">
					<i class="fa fa-bars fa-2x"></i><span>เพิ่มเติม</span>
				</span>
			</div>
		<?php endif; ?>
		<?php if($doc->status == 1) : ?>
			<div class="footer-menu">
				<span class="pg-icon" onclick="rollback()">
					<i class="fa fa-history fa-2x"></i><span>Rollback</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="cancel('<?php echo $doc->code; ?>', '<?php echo $doc->status; ?>')">
					<i class="fa fa-times fa-2x"></i><span>Cancel</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="sendToErp()">
					<i class="fa fa-send fa-2x"></i><span>Export</span>
				</span>
			</div>
		<?php endif; ?>
		</div>
 </div>
</div>
