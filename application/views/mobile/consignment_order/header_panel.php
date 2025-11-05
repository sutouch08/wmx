<div class="filter-pad move-out" id="header-panel">
  <div class="nav-title nav-title-center">
  	<a onclick="toggleHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center"><?php echo $doc->code; ?> [<?php echo status_text($doc->status); ?>]</div>
  </div>
  <div class="page-wrap">
    <div class="row">
      <div class="col-xs-12 fi">
        <label>Document No.</label>
        <input type="text" class="form-control" id="code" value="<?php echo $doc->code; ?>" readonly disabled/>
      </div>
      <div class="col-xs-12 fi">
        <label>Document date</label>
        <input type="text" class="form-control edit r" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
      </div>
      <div class="col-xs-12 fi">
        <label>Posting date</label>
        <input type="text" class="form-control edit r" id="posting-date" value="<?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?>" readonly disabled/>
      </div>
      <div class="col-xs-12 fi">
        <label>GP (%)</label>
        <input type="text" class="form-control edit" id="gp" value="<?php echo $doc->gp; ?>"  disabled/>
      </div>
      <div class="col-xs-12 fi">
        <label>รหัสลูกค้า</label>
        <input type="text" class="form-control edit r" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
      </div>
      <div class="col-xs-12 fi">
        <label>ชื่อลูกค้า</label>
        <input type="text" class="form-control edit r" id="customer-name" value="<?php echo $doc->customer_name; ?>" readonly disabled/>
      </div>
      <div class="col-xs-12 fi">
        <label>คลัง</label>
        <select class="form-control edit r" id="warehouse" onchange="updateCustomer()" disabled>
          <option value="">เลือก</option>
          <?php echo select_consignment_warehouse($doc->warehouse_code); ?>
        </select>
      </div>

      <div class="col-xs-12 fi">
        <label>Remark</label>
        <textarea class="form-control edit" id="remark" disabled><?php echo $doc->remark; ?></textarea>
      </div>

      <?php if($doc->status == 'P' OR $doc->status == 'A') : ?>
        <div class="col-xs-12 fi">
          <label class="not-show">edit</label>
          <button type="button" class="btn btn-sm btn-warning btn-block" id="btn-edit" onclick="getEdit()"></i class="fa fa-pencil"></i> แก้ไข</button>
          <button type="button" class="btn btn-sm btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
