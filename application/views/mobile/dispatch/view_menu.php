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
			<?php if($doc->status == 'C') : ?>
			<div class="footer-menu">
				<span class="pg-icon" onclick="goBack()">
					<i class="fa fa-tasks fa-2x"></i><span>Dispatch</span>
				</span>
			</div>
				<?php if( ! $this->agent->is_mobile()) : ?>
					<div class="footer-menu">
						<span class="pg-icon" onclick="printDispatch()">
							<i class="fa fa-print fa-2x"></i><span>พิมพ์</span>
						</span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if($doc->status == 'P' OR $doc->status == 'S') : ?>
			<div class="footer-menu">
				<span class="pg-icon" onclick="goEdit('<?php echo $doc->code; ?>')">
					<i class="fa fa-pencil fa-2x"></i><span>แก้ไข</span>
				</span>
			</div>
			<div class="footer-menu">
				<span class="pg-icon" onclick="cancelDispatch('<?php echo $doc->code; ?>')">
					<i class="fa fa-times fa-2x"></i><span>ยกเลิก</span>
				</span>
			</div>
			<?php endif; ?>

			<?php if($doc->status == 'S') : ?>
				<div class="footer-menu">
					<span class="pg-icon" onclick="closeDispatch('<?php echo $doc->code; ?>')">
						<i class="fa fa-check fa-2x"></i><span>ปิดการจัดส่ง</span>
					</span>
				</div>
			<?php endif; ?>
		</div>
 </div>
</div>
