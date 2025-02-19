<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status < 1) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
	</div>
</div>
<hr/>
<?php $statusLabel = $doc->status == 0 ? 'Pending' : ($doc->status == -1 ? 'Draft' : 'Invalid'); ?>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center e" id="date_add" value="<?php echo thai_date($doc->doc_date); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Posting date</label>
		<input type="text" class="width-100 text-center e" id="post-date" value="<?php echo empty($doc->posting_date) ? NULL : thai_date($doc->posting_date); ?>" disabled />
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="width-100" id="reference" value="<?php echo $doc->reference; ?>" disabled/>
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_warehouse($doc->warehouse_id); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="width-100 text-center" id="user" value="<?php echo $doc->user; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center" value="<?php echo $statusLabel; ?>" disabled/>
	</div>
	<div class="col-lg-8 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<?php if($doc->status < 1) : ?>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
	<?php endif; ?>
	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="warehouse-id" value="<?php echo $doc->warehouse_id; ?>" />
</div>

<?php if($doc->status < 1) : ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>โซน</label>
		<input type="text" class="width-100 text-center e" id="zone" value="" autofocus />
	</div>
  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9 padding-5">
    <label class="not-show">โซน</label>
    <input type="text" class="width-100" id="zone-name" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">change</label>
    <button type="button" class="btn btn-xs btn-yellow btn-block hide" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
    <button type="button" class="btn btn-xs btn-info btn-block" id="btn-set-zone" onclick="set_zone()">ตกลง</button>
  </div>

	<div class="divider hidden-lg"></div>

  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-code" value="" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>สต็อก</label>
		<input type="number" class="form-control input-sm text-center" id="stock-qty" value="" disabled />
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>เพิ่ม</label>
    <input type="number" class="form-control input-sm text-center" id="qty-up" value="" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>ลด</label>
    <input type="number" class="form-control input-sm text-center" id="qty-down" value="" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add" onclick="add_detail()" disabled>เพิ่มรายการ</button>
  </div>

	<input type="hidden" id="zone-code" value="" />
	<input type="hidden" id="zone-id" value="" />
	<input type="hidden" id="pd-id" value="" />
</div>
<?php endif; ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1000px;">
      <thead>
        <tr>
          <th class="fix-width-50 text-center">ลำดับ</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-250 text-center">โซน</th>
          <th class="fix-width-100 text-center">เพิ่ม</th>
          <th class="fix-width-100 text-center">ลด</th>
          <th class="fix-width-50 text-center"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? intval($rs->qty) : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
        <td class="middle text-right">
        <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status < 1 && $rs->valid == 0) : ?>
          <button type="button" class="btn btn-minier btn-danger" onclick="deleteDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
            <i class="fa fa-trash"></i>
          </button>
        <?php endif; ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script id="detail-template" type="text/x-handlebars-template">
	<tr class="font-size-12 rox" id="row-{{id}}">
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{ product_code }}</td>
		<td class="middle">{{ product_name }}</td>
		<td class="middle text-center">{{ zone_name }}</td>
		<td class="middle text-center" id="qty-up-{{id}}">{{ up }}</td>
		<td class="middle text-center" id="qty-down-{{id}}">{{ down }}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-minier btn-danger" onclick="deleteDetail({{ id }}, '{{ product_code }}')">
				<i class="fa fa-trash"></i>
			</button>
		</td>
	</tr>
</script>


<script type="text/javascript">
	// $('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
