<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> Save</button>
		<?php endif; ?>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center e r" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
	</div>
	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 padding-5">
		<label>คลัง</label>
			<select class="width-100 e r" id="warehouse" disabled>
				<option value="">Select</option>
				<?php echo select_warehouse($doc->warehouse_code); ?>
			</select>
	</div>
	<div class="col-lg-5 col-md-4 col-sm-3-harf col-xs-6 padding-5">
		<label>อ้างถึง</label>
		<input type="text" class="width-100 e" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-11 col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<?php if($doc->status == 'P' OR $doc->status == 'A') : ?>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
	<?php endif; ?>
</div>

<?php if($doc->status == 0) : ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>โซน</label>
		<input type="text" class="form-control input-sm c" id="zone-code" value="" autofocus />
	</div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9 padding-5">
    <label class="not-show">โซน</label>
    <input type="text" class="form-control input-sm c" id="zone-name" value="" readonly />
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">change</label>
    <button type="button" class="btn btn-xs btn-yellow btn-block" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
  </div>

	<div class="divider hidden-lg"></div>

  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center c" id="pd-code" value="" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>สต็อก</label>
		<input type="number" class="form-control input-sm text-center" id="stock-qty" value="" readonly />
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>เพิ่ม</label>
    <input type="number" class="form-control input-sm text-center c" id="qty-up" value="" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>ลด</label>
    <input type="number" class="form-control input-sm text-center c" id="qty-down" value="" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add" onclick="add_detail()">เพิ่มรายการ</button>
  </div>
</div>
<?php endif; ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-xs btn-danger btn-100" onclick="confirmRemove()"><i class="fa fa-trash"></i> ลบรายการ</button>
  </div>
	<div class="divider-hidden"></div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1040px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-60 text-center">
						<?php if($doc->status == 'P' OR $doc->status == 'A') : ?>
							<label>
								<input type="checkbox" class="ace chk-all" id="chk-all" onchange="checkAll()"/>
								<span class="lbl"></span>
							</label>
						<?php endif; ?>
					</th>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-200">โซน</th>
          <th class="fix-width-100 text-center">เพิ่ม</th>
          <th class="fix-width-100 text-center">ลด</th>
          <th class="fix-width-50 text-center">สถานะ</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>">
				<td class="middle text-center">
					<?php if($doc->status == 'P' OR $doc->status == 'A') : ?>
						<?php if(($this->pm->can_add OR $this->pm->can_edit)) : ?>
							<label>
								<input type="checkbox" class="ace chk-row" value="<?php echo $rs->id; ?>" />
								<span class="lbl"></span>
							</label>
						<?php endif; ?>
					<?php endif; ?>
				</td>
        <td class="middle text-center no"><?php echo $no; ?></td>
        <td class="middle"><?php echo $rs->product_code; ?></td>
        <td class="middle"><?php echo $rs->product_name; ?></td>
        <td class="middle"><?php echo $rs->zone_code; ?></td>
        <td class="middle text-center">
					<input type="number" class="width-100 text-label text-center qty-up"
					id="qty-up-<?php echo $rs->id; ?>"
					data-id="<?php echo $rs->id; ?>"
					value="<?php echo $rs->qty > 0 ? intval($rs->qty) : 0 ; ?>" readonly />
				</td>
        <td class="middle text-center">
					<input type="number" class="width-100 text-label text-center qty-down"
					id="qty-down-<?php echo $rs->id; ?>"
					data-id="<?php echo $rs->id; ?>"
					value="<?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>" readonly />
				</td>
				<td class="middle text-center">
					<?php echo $rs->line_status == 'D' ? 'Canceled' : ($rs->line_status == 'C' ? 'Closed' : 'Open'); ?>
				</td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<form id="diffForm" method="post" action="<?php echo base_url(); ?>inventory/check_stock_diff/diff_list/<?php echo $doc->code; ?>">
	<input type="hidden" name="adjust_code" value="<?php echo $doc->code; ?>">
</form>

<script id="detail-template" type="text/x-handlebars-template">
	<tr class="font-size-11 rox" id="row-{{id}}">
		<td class="middle text-center">
			<label>
				<input type="checkbox" class="ace chk-row" value="{{id}}" />
				<span class="lbl"></span>
			</label>
		</td>
		<td class="middle text-center no"></td>
		<td class="middle">{{pdCode}}</td>
		<td class="middle">{{pdName}}</td>
		<td class="middle">{{zoneCode}}</td>

		<td class="middle text-center">
			<input type="number" class="width-100 text-label text-center qty-up"
			id="qty-up-{{id}}" data-id="{{id}}" value="{{up}}" readonly />
		</td>
		<td class="middle text-center">
			<input type="number" class="width-100 text-label text-center qty-down"
			id="qty-down-{{id}}" data-id="{{id}}" value="{{down}}" readonly />
		</td>
		<td class="middle text-center">{{line_status}}</td>
	</tr>
</script>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
