<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>

<div class="form-horizontal">
	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="code" id="code" class="width-100 r" value="" onkeyup="validCode(this)" autofocus required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline grey e" id="code-error">Allow only [a-z, A-Z, 0-9, "-", "_" ]</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="name" id="name" maxlength="150" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">บาร์โค้ด</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="barcode" id="barcode"  maxlength="100" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="barcode-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รุ่น</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="style" id="model" maxlength="50" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="model-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สี</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="color" id="color" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="color-error"></div>
	</div>


	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ไซส์</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="size" id="size" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="size-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ราคาทุน</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="number" step="any" inputmode="numeric" name="cost" id="cost" class="width-100 e" value=""  />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="cost-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ราคาขาย</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="number" step="any" inputmode="numeric" name="price" id="price" class="width-100 e" value=""  />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="price-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">หน่วยนับ</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="unit" id="unit" maxlength="10" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="unit-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ยี่ห้อ</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="brand" id="brand" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="brand-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มหลัก</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="main_group" id="main-group" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="main-group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มสินค้า</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="group" id="group" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">หมวดหมู่</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="category" id="category" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="category-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ประเภท</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="kind" id="kind" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชนิด</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="type" id="type" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="type-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">คอเล็คชั่น</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="text" name="collection" id="collection" maxlength="20" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="collection-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ปี</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<input type="number" name="year" id="year" maxlength="4" class="width-100 e" value="" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red e" id="year-error"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">&nbsp;</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="padding-top:5px;">
				<input name="count_stock" class="ace" type="checkbox" id="count-stock" value="1" checked />
				<span class="lbl">&nbsp;&nbsp; นับสต็อก</span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">&nbsp;</label>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<label style="padding-top:5px;">
				<input name="active" class="ace" type="checkbox" id="active" value="1" checked />
				<span class="lbl">&nbsp;&nbsp; Active</span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="divider-hidden"></div>
	<div class="form-group">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
			<button type="button" class="btn btn-sm btn-success btn-100 hidden-xs" onclick="add()">Add</button>
			<button type="button" class="btn btn-sm btn-success btn-block visible-xs" onclick="add()"><i class="fa fa-save"></i> Add</button>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
