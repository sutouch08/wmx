<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
			<!-- <button type="button" class="btn btn-sm btn-primary" onclick="getDiffList()"><i class="fa fa-archive"></i> ยอดต่าง</button> -->
			<button type="button" class="btn btn-sm btn-success" onclick="saveAdjust()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>อ้างถึง</label>
		<input type="text" class="form-control input-sm edit" id="reference" value="<?php echo $doc->reference; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-5-harf col-sm-4-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<?php if($doc->status == 0) : ?>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
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
    <p class="pull-right top-p">
      <span style="margin-right:30px;"><i class="fa fa-check green"></i> = ปรับยอดแล้ว</span>
      <span><i class="fa fa-times red"></i> = ยังไม่ปรับยอด</span>
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1040px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-200">โซน</th>
          <th class="fix-width-100 text-center">เพิ่ม</th>
          <th class="fix-width-100 text-center">ลด</th>
          <th class="fix-width-50 text-center">สถานะ</th>
          <th class="fix-width-50 text-right"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? intval($rs->qty) : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
        <td class="middle text-center">
          <?php echo is_active($rs->valid); ?>
        </td>
        <td class="middle text-right">
        <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
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

<form id="diffForm" method="post" action="<?php echo base_url(); ?>inventory/check_stock_diff/diff_list/<?php echo $doc->code; ?>">
	<input type="hidden" name="adjust_code" value="<?php echo $doc->code; ?>">
</form>

<script id="detail-template" type="text/x-handlebars-template">
<tr class="font-size-11 rox" id="row-{{id}}">
  <td class="middle text-center no">{{no}}</td>
  <td class="middle">{{ pdCode }}</td>
  <td class="middle">{{ pdName }}</td>
  <td class="middle">{{ zoneName }}</td>
  <td class="middle text-center" id="qty-up-{{id}}">{{ up }}</td>
  <td class="middle text-center" id="qty-down-{{id}}">{{ down }}</td>
  <td class="middle text-center">
    {{#if valid}}
    <i class="fa fa-times red"></i>
    {{else}}
    <i class="fa fa-check green"></i>
    {{/if}}
  </td>
  <td class="middle text-right">
  <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
    <button type="button" class="btn btn-minier btn-danger" onclick="deleteDetail({{ id }}, '{{ pdCode }}')">
      <i class="fa fa-trash"></i>
    </button>
  <?php endif; ?>
  </td>
</tr>
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
