<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($this->pm->can_edit && $doc->status == 0) : ?>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
		<?php endif; ?>
		<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
			<div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-save icon-on-left"></i>
          บันทึก
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="primary">
            <a href="javascript:save('P')">บันทึกเป็นดราฟท์</a>
          </li>
					<li class="success">
            <a href="javascript:save('C')">บันทึกรับเข้าทันที</a>
          </li>
					<li class="purple">
            <a href="javascript:save('O')">บันทึกรอรับ</a>
          </li>
        </ul>
      </div>
		<?php	endif; ?>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" id="doc-date" value="<?php echo thai_date($doc->date_add); ?>" />
  </div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>ผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm text-center h" name="vender_code" id="vender_code" value="<?php echo $doc->vender_code; ?>" placeholder="รหัสผู้จำหน่าย" />
	</div>

	<div class="col-lg-4-harf col-md-6 col-sm-6 col-xs-8 padding-5">
	 	<label class="not-show">vender</label>
	  <input type="text" class="form-control input-sm h" name="venderName" id="venderName" value="<?php echo $doc->vender_name; ?>" placeholder="ระบุผู้จำหน่าย" />
	</div>

	<?php $c_hide = empty($doc->po_code) ? '' : 'hide'; ?>
	<?php $p_hide = empty($doc->po_code) ? 'hide' : '' ; ?>
	<?php $p_disabled = empty($doc->po_code) ? '' : 'disabled'; ?>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center h" name="poCode" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" <?php echo $p_disabled; ?>/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="not-show">clear</label>
		<button type="button" class="btn btn-xs btn-info btn-block <?php echo $c_hide; ?> h" id="btn-confirm-po" onclick="confirmPo()">ยืนยัน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo $p_hide; ?> h" id="btn-get-po" onclick="getPoDetail()">แสดง</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearPo()">Clear</button>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
		<label>PO Ref.</label>
		<input type="text" class="form-control input-sm text-center" id="po-ref" value="<?php echo $doc->reference; ?>" disabled />
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" />
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 h" id="warehouse" onchange="warehouse_init()">
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm h" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo empty($zone) ? NULL : $zone->code; ?>" />
	</div>
	<div class="col-lg-3-harf col-md-4 col-sm-6 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone h" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="<?php echo empty($zone) ? NULL : $zone->name; ?>" />
	</div>
	<div class="col-lg-12 col-md-6 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm h" name="remark" id="remark" value="<?php echo $doc->remark; ?>"  />
	</div>
</div>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-danger btn-100" onclick="removeChecked()">ลบรายการ</button>
	</div>
	<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
</div>
<hr class=""/>
<div class="row" style="margin-left:-8px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1 padding-0" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-bordered" style="margin-bottom:0px; min-width:940px;">
			<thead>
				<tr class="font-size-12">
					<th class="fix-width-50 text-center">
						<label>
							<input type="checkbox" class="ace" id="check-all" onchange="toggleCheckAll($(this))">
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">ค้างรับ</th>
					<th class="fix-width-100 text-center">จำนวน</th>
				</tr>
			</thead>
			<tbody id="receive-table">
  <?php $no = 1; ?>
	<?php $totalQty = 0; ?>
	<?php $totalAmount = 0; ?>
	<?php if( ! empty($details)) : ?>
		<?php foreach($details as $rs) : ?>
			<?php $uid = $rs->po_id."-".$rs->po_detail_id; ?>
			<tr class="font-size-11" id="row-<?php echo $uid; ?>">
				<td class="middle text-center">
					<label>
						<input type="checkbox" class="ace chk" value="<?php echo $uid; ?>" />
						<span class="lbl"></span>
					</label>
				</td>
				<td class="middle text-center no"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle text-center">
					<input type="text"
						class="form-control input-sm text-center text-label"
						id="backlogs-<?php echo $uid; ?>"
						data-backlogs="<?php echo $rs->backlogs; ?>"
						value="<?php echo number($rs->backlogs); ?>" readonly />
				</td>
				<td class="middle text-center">
					<input type="number"
						class="form-control input-sm text-center receive-qty"
						id="receive-qty-<?php echo $uid; ?>"
						data-uid="<?php echo $uid; ?>"
						data-limit="<?php echo $rs->limit; ?>"
						data-backlogs="<?php echo $rs->backlogs; ?>"
						data-basecode="<?php echo $rs->po_code; ?>"
						data-baseline="<?php echo $rs->po_detail_id; ?>"
						data-polinenum="<?php echo $rs->po_line_num; ?>"
						data-poref="<?php echo $rs->po_ref; ?>"
						data-code="<?php echo $rs->product_code; ?>"
						data-name="<?php echo $rs->product_name; ?>"
						data-unit="<?php echo $rs->unit; ?>"
						value="<?php echo round($rs->qty, 2); ?>"
						onchange="recalAmount(<?php echo $uid; ?>)" />
				</td>
			</tr>
			<?php $no++; ?>
			<?php $totalQty += $rs->qty; ?>
		<?php endforeach; ?>
	<?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-4 col-sm-4 col-xs-6 control-label no-padding-right">User</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm input-large" value="<?php echo $doc->user; ?>" disabled>
        </div>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5">
          <input type="text" class="form-control input-sm text-right" id="total-receive" value="<?php echo number($totalQty); ?>" disabled>
        </div>
      </div>
		</div>
	</div>
</div>



<?php $this->load->view('inventory/receive_po/receive_modal'); ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>

<script id="receive-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11" id="row-{{uid}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{uid}}" />
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{no}}</td>
			<td class="middle">{{pdCode}}</td>
			<td class="middle">{{pdName}}</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-sm text-right text-label"
					id="backlogs-{{uid}}" data-backlogs="{{backlogs}}" value="{{backLogsLabel}}" readonly />
			</td>
			<td class="middle">
				<input type="text"
					class="form-control input-sm text-center receive-qty"
					id="receive-qty-{{uid}}"
					data-uid="{{uid}}"
					data-limit="{{limit}}"
					data-backlogs="{{backlogs}}"
					data-basecode="{{baseCode}}"
					data-baseline="{{baseLine}}"
					data-polinenum="{{poLineNum}}"
					data-poref="{{poRef}}"
					data-code="{{pdCode}}"
					data-name="{{pdName}}"
					data-unit="{{unit}}"
					value="{{qty}}" onchange="recalAmount({{uid}})" />
			</td>
		</tr>
	{{/each}}
</script>

<script id="po-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{product_code}}</td>
		<td class="middle">{{product_name}}</td>
		<td class="middle text-center">{{backlog_label}}</td>
		<td class="middle text-center">{{on_order_label}}</td>
		<td class="middle text-center">{{qty_label}}</td>
		<td class="middle">
			<input type="text"
				class="form-control input-sm text-center po-qty"
				id="po-qty-{{uid}}"
				data-uid="{{uid}}"
				data-code="{{product_code}}"
				data-name="{{product_name}}"
				data-unit="{{unit}}"
				data-basecode="{{po_code}}"
				data-poref="{{po_ref}}"
				data-baseline="{{po_detail_id}}"
				data-polinenum="{{po_line_num}}"
				data-limit="{{limit}}"
				data-backlogs="{{backlog}}"
				data-qty="{{qty}}"
				data-no="{{no}}"
				value="" />
		</td>
    </tr>
  {{/each}}
</script>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>

<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
