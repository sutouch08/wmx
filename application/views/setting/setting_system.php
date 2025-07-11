
<?php
    $open     = $CLOSE_SYSTEM == 0 ? 'btn-success' : '';
    $close    = $CLOSE_SYSTEM == 1 ? 'btn-danger' : '';
    $freze    = $CLOSE_SYSTEM == 2 ? 'btn-warning' : '';
    $uat_on = $IS_UAT == 1 ? 'btn-primary' : '';
    $uat_off = $IS_UAT == 0 ? 'btn-success' : '';

?>

  <form id="systemForm">
    <div class="row">
  	<?php if( $cando === TRUE ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
    	<div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
      <div class="col-sm-9">
      	<div class="btn-group input-xlarge">
        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:33%;" id="btn-open" onClick="openSystem()">เปิด</button>
          <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:33%;" id="btn-close" onClick="closeSystem()">ปิด</button>
          <button type="button" class="btn btn-sm <?php echo $freze; ?>" style="width:34%" id="btn-freze" onclick="frezeSystem()">ดูอย่างเดียว</button>
        </div>
        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
      	<input type="hidden" name="CLOSE_SYSTEM" id="closed" value="<?php echo $CLOSE_SYSTEM; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">Environment</span></div>
      <div class="col-sm-9">
        <div class="btn-group input-medium">
          <button type="button" class="btn btn-sm <?php echo $uat_on; ?>" style="width:50%;" id="btn-uat-on" onClick="toggleUat(1)">UAT</button>
          <button type="button" class="btn btn-sm <?php echo $uat_off; ?>" style="width:50%;" id="btn-uat-off" onClick="toggleUat(0)">PROD</button>
        </div>
        <span class="help-block">เปิดการป้อนเลขที่เอกสารด้วยมือ หากปิดระบบจะรับเลขที่เอกสารให้อัตโนมัติ</span>
        <input type="hidden" name="IS_UAT" id="is-uat" value="<?php echo $IS_UAT; ?>" />
      </div>
      <div class="divider-hidden"></div>
    <?php endif; ?>

		<div class="col-sm-3"><span class="form-control left-label">วันที่ในการบันทึกขายตัดสต็อก</span></div>
    <div class="col-sm-9">
      <div class="btn-group input-medium">
        <select class="form-control input-sm input-medium" name="ORDER_SOLD_DATE">
					<option value="B" <?php echo is_selected("B", $ORDER_SOLD_DATE); ?>>วันที่เปิดบิล</option>
					<option value="D" <?php echo is_selected("D", $ORDER_SOLD_DATE); ?>>วันที่เอกสาร</option>
				</select>
      </div>
      <span class="help-block">กำหนดประเภทวันที่ที่ใช้ในการบันทึกขายและตัดสต็อกในระบบ SAP</span>
    </div>
    <div class="divider-hidden"></div>

      <div class="col-sm-9 col-sm-offset-3">
        <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
      	<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('systemForm')"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/row-->
  </form>
