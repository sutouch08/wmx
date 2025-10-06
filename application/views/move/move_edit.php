<?php $this->load->view('include/header'); ?>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
			<h3 class="title"><?php echo $this->title; ?></h3>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
			<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status == 'P' && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<?php if($barcode) : ?>
					<button type="button" class="btn btn-white btn-primary top-btn" onclick="goUseKeyboard()">คีย์มือ</button>
				<?php else : ?>
					<button type="button" class="btn btn-white btn-primary top-btn" onclick="goUseBarcode()">ใช้บาร์โค้ด</button>
				<?php endif; ?>
				<button type="button" class="btn btn-white btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>
		</div>
	</div>
<hr/>
<?php	$this->load->view('move/move_edit_header'); ?>
<?php $this->load->view('move/move_detail'); ?>

	<script id="moveTableTemplate" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
		<tr>
			<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
		</tr>
		{{else}}
			{{#if @last}}
				<tr>
					<td colspan="5" class="text-right"><strong>รวม</strong></td>
					<td class="middle text-center" id="total">{{ total }}</td>
					<td></td>
				</tr>
			{{else}}
			<tr class="font-size-12" id="row-{{id}}">
				<td class="middle text-center no">{{ no }}</td>
				<td class="middle">{{ barcode }}</td>
				<td class="middle">{{ products }}</td>
				<td class="middle">{{ from_zone }}</td>
				<td class="middle">{{{ to_zone }}}</td>
				<td class="middle text-center qty">{{ qty }}</td>
				<td class="middle text-center">{{{ btn_delete }}}</td>
			</tr>
			{{/if}}
		{{/if}}
	{{/each}}
	</script>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
