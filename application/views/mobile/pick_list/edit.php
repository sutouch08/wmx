<?php $this->load->view('include/header_mobile'); ?>
<style>
	.page-wrap.listing {
		height: calc(100vh - 170px);
	}
</style>
<div class="nav-title nav-title-center">
	<a onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
	<div class="font-size-18 text-center"><?php echo $doc->code; ?> [<?php echo status_text($doc->status); ?>]</div>
	<div class="header-info-icon"><a href="javascript:showHeaderInfo()"><i class="fa fa-info white"></i></a></div>
</div>
<div class="divider-hidden"></div>
<div class="row">
  <div class="page-wrap listing" id="detail-table">
		<?php $statusColor = $doc->status == 1 ? 'closed' : ($doc->status == 2 ? 'canceled' : 'draft'); ?>
    <?php $no = 1; ?>
		<?php $totalQty = 0; ?>
		<?php $totalAmount = 0; ?>
    <?php if( ! empty($details)) : ?>
      <?php foreach($details as $rs) : ?>
        <div class="list-block" id="list-block-<?php echo $rs->id; ?>" onclick="toggleActive(<?php echo $rs->id; ?>)">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no  <?php echo $statusColor; ?>" id="no-<?php echo $rs->id; ?>"><?php echo $no; ?></div>
							<input type="checkbox" class="chk hide"
							id="list-<?php echo $rs->id; ?>"
							data-code="<?php echo $rs->product_code; ?>"
							data-name="<?php echo $rs->product_name; ?>"
							value="<?php echo $rs->id; ?>"/>
							<div class="display-inline-block width-100">
								<span class="display-block font-size-12"><?php echo $rs->product_code; ?></span>
								<span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
								<span class="float-left font-size-11 width-20">Price:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 price"
								id="price-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->price, 2); ?>" readonly/>
								<span class="float-left font-size-11 width-20">OnHand:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 stock"
								id="stock-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="" readonly/>
								<span class="float-left font-size-11 width-20">QTY:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 qty"
								id="qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->qty); ?>" readonly/>
								<span class="float-left font-size-11 width-20">Amnt:</span>
								<input type="text" class="float-left font-size-11 text-label padding-0 width-30 amount"
								id="amount-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo number($rs->amount, 2); ?>" readonly/>
							</div>
            </div>
          </div>
        </div>
				<?php $totalQty += $rs->qty; ?>
				<?php $totalAmount += $rs->amount; ?>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>" />
</div>

<div class="pg-summary">
	<div class="pg-summary-inner">
		<div class="pg-summary-content">
			<div class="summary-text width-50">
				<span class="float-left font-size-16 width-30">QTY.</span>
				<input type="text"
				class="float-left font-size-16 text-label padding-0 width-70 text-center"
				style="color:white !important;"
				id="total-qty"
				value="<?php echo number($totalQty); ?>" readonly />
			</div>
			<div class="summary-text width-50">
				<span class="float-left font-size-16 width-30">Amnt.</span>
				<input type="text"
				class="float-left font-size-16 text-label padding-0 width-70 text-right"
				style="color:white !important;"
				id="total-amount"
				value="<?php echo number($totalAmount, 2); ?>" readonly />
			</div>
		</div>
	</div>
</div>

<script id="item-template" type="text/x-handlebarsTemplate">
	<div class="list-block" id="list-block-{{id}}" onclick="toggleActive({{id}})">
		<div class="list-link" >
			<div class="list-link-inner width-100">
				<div class="margin-right-10 no" id="no-{{id}}"></div>
				<input type="checkbox" class="chk hide"
				id="list-{{id}}"
				data-code="{{product_code}}"
				data-name="{{product_name}}"
				value="{{id}}"/>

				<div class="display-inline-block width-100">
					<span class="display-block font-size-12">{{product_code}}</span>
					<span class="display-block font-size-11">{{product_name}}</span>
					<span class="float-left font-size-11 width-20">Price:</span>
					<input type="text" class="float-left font-size-11 text-label padding-0 width-30 price"
					id="price-{{id}}" data-id="{{id}}" value="{{price}}" readonly/>
					<span class="float-left font-size-11 width-20">OnHand:</span>
					<input type="text" class="float-left font-size-11 text-label padding-0 width-30 stock"
					id="stock-{{id}}" data-id="{{id}}" value="{{stock}}" readonly/>
					<span class="float-left font-size-11 width-20">QTY:</span>
					<input type="text" class="float-left font-size-11 text-label padding-0 width-30 qty"
					id="qty-{{id}}" data-id="{{id}}" value="{{qty}}" readonly/>
					<span class="float-left font-size-11 width-20">Amnt:</span>
					<input type="text" class="float-left font-size-11 text-label padding-0 width-30 amount"
					id="amount-{{id}}" data-id="{{id}}" value="{{amount}}" readonly/>
				</div>
			</div>
		</div>
	</div>
</script>

<?php $this->load->view('mobile/pick_list/header_panel'); ?>
<?php $this->load->view('mobile/pick_list/pick_control'); ?>
<?php $this->load->view('mobile/pick_list/pick_details'); ?>
<?php //$this->load->view('consignment/item_panel'); ?>
<?php //$this->load->view('consignment/footer_menu'); ?>
<?php //$this->load->view('include/barcode_reader'); ?>
<?php //$this->load->view('cancel_modal'); ?>

<div class="more-menu run-out" id="more-menu">
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="cancel('<?php echo $doc->code; ?>', '<?php echo $doc->status; ?>')">
			<i class="fa fa-times fa-2x"></i><span>ยกเลิก</span>
		</span>
	</div>
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="removeRow()">
			<i class="fa fa-trash fa-2x"></i><span>ลบ</span>
		</span>
	</div>
	<div class="footer-menu display-block">
		<span class="pg-icon" onclick="save()">
			<i class="fa fa-save fa-2x"></i><span>บันทึก</span>
		</span>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer_mobile'); ?>
