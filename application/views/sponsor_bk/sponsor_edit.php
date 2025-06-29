<?php $this->load->view('include/header'); ?>
<?php $isAdmin = (get_cookie('id_profile') == -987654321 ? TRUE : FALSE); ?>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5 text-right">
    	<p class="pull-right" style="margin-bottom:1px;">

				<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<button type="button" class="btn btn-sm btn-default top-btn" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit) && $order->is_approved == 0) : ?>
				<button type="button" class="btn btn-sm btn-yellow top-btn" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
				<?php endif; ?>
				<?php if($order->status == 0) : ?>
					<button type="button" class="btn btn-sm btn-success top-btn" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
				<?php if($order->state == 1 && $order->is_approved == 0 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
						<button type="button" class="btn btn-sm btn-success top-btn" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
				<?php endif; ?>
				<?php if($order->state == 1 && $order->is_approved == 1 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
						<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unapprove()"><i class="fa fa-refresh"></i> ไม่อนุมัติ</button>
				<?php endif; ?>
      </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<?php $this->load->view('sponsor/sponsor_edit_header'); ?>
<?php if(empty($approve_view)) : ?>
<?php //$this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_address_panel'); ?>
<?php $this->load->view('sponsor/sponsor_discount_bar'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<?php else : ?>
	<input type="hidden" id="id_sender" value="<?php echo $order->id_sender; ?>"/>
	<input type="hidden" id="id_address" value="<?php echo $order->id_address; ?>"/>
<?php endif; ?>
<?php $this->load->view('sponsor/sponsor_detail'); ?>

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


<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/cancel_order.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
