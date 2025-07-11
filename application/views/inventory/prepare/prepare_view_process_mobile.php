<?php $this->load->view('include/header'); ?>
<?php $this->load->view('inventory/prepare/style'); ?>
<div class="row">
	<div class="col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
</div><!-- End Row -->
<hr class=""/>
<?php $this->load->view('inventory/prepare/mobile/filter'); ?>
<div class="move-list">
	<?php if( ! empty($orders)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php $whName = []; ?>
		<?php foreach($orders as $rs) : ?>
			<?php $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code); ?>
			<?php if( empty($whName[$rs->warehouse_code])) : ?>
				<?php $whName[$rs->warehouse_code] = warehouse_name($rs->warehouse_code); ?>
			<?php endif; ?>
			<?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
			<?php $cn_text = $rs->is_cancled == 1 ? '<span class="badge badge-danger font-size-10 margin-left-5">ยกเลิก</span>' : ''; ?>
			<div class="move-list-item">
				<div class="col-xs-5-harf padding-5">
					<p class="move-list-line bold"><span class="font-size-14"><?php echo $rs->code . $cn_text; ?></span></p>
					<p class="move-list-line bold">SO No : <?php echo $rs->so_no; ?></p>
					<p class="move-list-line bold">MKP No : <?php echo $rs->reference; ?></p>
					<p class="move-list-line bold">คลัง : <?php echo $rs->warehouse_code; ?></p>
				</div>
				<div class="col-xs-4-harf padding-5">
					<p class="move-list-line bold"><span class="font-size-14"><?php echo $rs->channels_name; ?></span></p>
					<p class="move-list-line bold">วันที่ : <?php echo thai_date($rs->date_add, FALSE,'/'); ?></p>
					<p class="move-list-line bold">Fulfil No : <?php echo $rs->fulfillment_code; ?></p>
					<p class="move-list-line bold">จำนวน : <?php echo number($rs->qty); ?></p>
				</div>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<a class="move-list-link font-size-24" href="javascript:goPrepare('<?php echo $rs->code; ?>', 'mobile')"><i class="fa fa-angle-right"></i></a>
				<?php endif; ?>
			</div>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu width-20">
				<span class="width-100" onclick="refresh()">
					<i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goToBuffer()">
					<i class="fa fa-history fa-2x white"></i><span class="fon-size-12">Buffer</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="goBack()">
					<i class="fa fa-server fa-2x white"></i><span class="fon-size-12">รอจัด</span>
				</span>
			</div>
			<div class="footer-menu width-20">
				<span class="width-100" onclick="toggleFilter()">
					<i class="fa fa-search fa-2x white"></i><span class="fon-size-12">ตัวกรอง</span>
				</span>
			</div>
			<div class="footer-menu width-20">
        <span class="width-100" onclick="toggleExtraMenu()">
          <i class="fa fa-qrcode fa-2x white"></i><span class="fon-size-12">Order</span>
        </span>
      </div>
		</div>
		<input type="hidden" id="filter" value="hide" />
 </div>
</div>

<div class="extra-menu slide-out visible-xs" id="extra-menu">
	<div class="width-100">
		<span class="width-100">
			<input type="text" class="form-control input-lg focus"
			style="padding-left:15px; padding-right:40px;" id="barcode-order" inputmode="none" placeholder="Barcode Order" autocomplete="off">
			<i class="ace-icon fa fa-qrcode fa-2x" style="position:absolute; top:20px; right:22px; color:grey;"></i>
		</span>
	</div>
	<input type="hidden" id="extra" value="hide" />
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<?php $this->load->view('include/footer'); ?>
