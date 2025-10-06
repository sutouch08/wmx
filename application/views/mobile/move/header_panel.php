<div class="filter-pad move-out" id="header-panel">
  <div class="nav-title nav-title-center">
    <a class="hidden-lg" onclick="toggleHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center"><?php echo $doc->code; ?></div>
  </div>
  <div class="page-wrap">
    <div class="col-xs-12 fi">
  		<label>เลขที่</label>
  		<input type="text" class="form-control text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
      <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  	</div>

  	<div class="col-xs-12 fi">
  		<label>วันที่</label>
  		<input type="text" class="form-control text-center r e" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
  	</div>

    <div class="col-xs-12 fi">
  		<label>อ้างอิง</label>
  		<input type="text" class="form-control focus text-center r e" id="reference"  value="<?php echo $doc->reference; ?>" disabled/>
  	</div>

    <div class="col-xs-12 fi">
  		<label>คลัง</label>
      <select class="form-control r e" id="warehouse" disabled>
        <option value="">ไม่ระบุ</option>
        <?php echo select_common_warehouse($doc->warehouse_code); ?>
      </select>
  	</div>

    <div class="col-xs-12 fi">
  		<label>User</label>
  		<input type="text" class="form-control focus text-center" id="user"  value="<?php echo $doc->user; ?>" disabled/>
  	</div>

  	<div class="col-xs-12 fi">
  		<label>หมายเหตุ</label>
      <textarea class="form-control focus e" id="remark" disabled><?php echo $doc->remark; ?></textarea>
  	</div>

    <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 'P') : ?>
      <div class="col-xs-12">
        <label class="not-show">edit</label>
        <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i>&nbsp;&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"></i class="fa fa-save"></i>&nbsp;&nbsp; Update</button>
      </div>
    <?php endif; ?>
  </div>
</div>
