<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title">รายการรอเปิดบิล</h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr/>


<?php
  if( $order->state == 7)
  {
    $this->load->view('inventory/delivery_order/bill_confirm_detail');
  }
  else if( $order->state == 8)
  {
    $this->load->view('inventory/delivery_order/bill_closed_detail');
  }
  else
  {
    $this->load->view('inventory/delivery_order/invalid_state');
  }
?>

<script>
  function shipOrderTiktok(reference) {
    load_in();

    $.ajax({
      url:BASE_URL + 'inventory/qc/ship_order_tiktok/'+reference,
      type:'POST',
      cache:false,
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            window.open(ds.data.fileUrl, "_blank");
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
