<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
		<?php if($order->status == 'P') : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<?php $this->load->view('sponsor/sponsor_edit_header'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="tabable">
			<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#content" aria-expanded="true" aria-controls="content" role="tab" data-toggle="tab">Contents</a>
        </li>
      	<li>
          <a href="#address" aria-expanded="false" aria-controls="logistic" role="tab" data-toggle="tab">Logistics</a>
        </li>
      </ul>

			<div class="tab-content">
				<div role="tabpanel" class="tab-pane fade active in" id="content">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:350px; overflow:auto;">
							<?php $this->load->view('sponsor/sponsor_detail'); ?>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:350px; overflow:auto;">
            	<?php $this->load->view('sponsor/sponsor_address'); ?>
          </div>
        </div><!-- /row-->
      </div>
			</div>
		</div><!-- tabable -->
	</div>
</div>
<hr class="margin-bottom-15">
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<div class="form-horizontal">
			<div class="form-group margin-bottom-5">
				<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">User</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<input type="text" class="width-100" value="<?php echo $order->user; ?>" disabled />
				</div>
			</div>

			<div class="form-group margin-bottom-5">
				<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Remark</div>
				<div class="col-lg-10 col-md-6 col-sm-6 col-xs-12">
					<textarea class="width-100" id="remark" rows="3" onchange="updateRemark()"><?php echo $order->remark; ?></textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group margin-bottom-5">
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">Total Qty</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
					<input type="text" class="width-100" id="total-qty" value="<?php echo number($order->total_qty, 2); ?>" disabled />
				</div>
			</div>

			<div class="form-group margin-bottom-5">
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">Total Amount</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
					<input type="text" class="width-100" id="total-amount" value="<?php echo number($order->total_amount, 2); ?>" disabled />
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="id_sender" value="<?php echo $order->id_sender; ?>"/>
<input type="hidden" id="id_address" value="<?php echo $order->id_address; ?>"/>


<?php if(!empty($approve_logs)) : ?>
	<div class="row">
		<?php foreach($approve_logs as $logs) : ?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  text-right padding-5">
			<?php if($logs->approve == 1) : ?>
			  <span class="green">
					อนุมัติโดย :
					<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
				</span>
			<?php else : ?>
				<span class="red">
				ยกเลิกโดย :
				<?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?>
			  </span>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/cancel_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
