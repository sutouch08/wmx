<?php
$pm = get_permission('SOREST', get_cookie('uid'), get_cookie('id_profile')); //--- ย้อนสถานะออเดอร์ได้หรือไม่
$px	= get_permission('SORECT', get_cookie('uid'), get_cookie('id_profile')); //--- ย้อนสถานะออเดอร์ที่เปิดบิลแล้วได้หรือไม่
$pc = get_permission('SOREUP', get_cookie('uid'), get_cookie('id_profile')); //--- ปล่อยออเดอร์ที่ยังไม่ชำระเงิน (เงินสด)
$pr = get_permission('SOREPR', get_cookie('uid'), get_cookie('id_profile')); //--- ปล่อยออเดอร์ได้หรือไม่

$canSetPrepare = ($pr->can_add + $pr->can_edit + $pr->can_delete) > 0 ? TRUE : FALSE;
$canChange	= ($pm->can_add + $pm->can_edit + $pm->can_delete) > 0 ? TRUE : FALSE;
$canUnbill	= ($px->can_add + $px->can_edit + $px->can_delete) > 0 ? TRUE : FALSE;
$canSkip = ($pc->can_add + $pc->can_edit + $pc->can_delete) > 0 ? TRUE : FALSE;

 ?>
<div class="row" style="padding:15px;">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 padding-5">
    	<table class="table" style="margin-bottom:0px;">
      <?php if( $this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete ) : ?>
          <tr>
            <td class="width-25 middle text-right" style="border:0px; padding:5px;">สถานะ : </td>
            <td class="width-50" style="border:0px; padding:5px;">
              <select class="form-control input-sm" style="padding-top:0px; padding-bottom:0px;" id="stateList">
                <option value="0">เลือกสถานะ</option>
                <option value="1">รอดำเนินการ</option>
                <option value="3">รอจัดสินค้า</option>
                <option value="5">รอตรวจ</option>
                <option value="7">รอเปิดบิล</option>
                <option value="9">ยกเลิก</option>
              </select>
            </td>
            <td class="width-25" style="border:0px; padding:5px;">
              <button class="btn btn-xs btn-primary btn-block" id="btn-change-state" onclick="changeState()">เปลี่ยนสถานะ</button>
            </td>
          </tr>
       <?php else : ?>
       			<tr>
            	<td class="width-30 text-center" style="border:0px;">สถานะ</td>
              <td class="width-40 text-center" style="border:0px;">พนักงาน</td>
              <td class="width-30 text-center" style="border:0px;">เวลา</td>
            </tr>
       <?php endif; ?>
      </table>
	</div>

  <?php
    $link = "";
    switch($order->state)
    {
      case '9' :
      $link = 'onclick="showReason()"';
      break;
      case '7' :
      $link = 'onclick="view_delivery()"';
      break;
      case '8' :
      $link = 'onclick="view_closed()"';
      break;
      default :
      $line = "";
      break;
    }

    $pointer = empty($link) ? '' : 'pointer';
  ?>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-14 <?php echo $pointer; ?>"
    <?php echo $link; ?>	style="height: 49px; border:solid 2px white; <?php echo state_color($order->state); ?>"	>
		<center>สถานปัจจุบัน</center>
		<center><?php echo get_state_name($order->state); ?></center>
	</div>

    <?php if( !empty($state) ) : ?>
      <?php foreach($state as $rs) : ?>
        <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5 font-size-10" style="height: 49px; border:solid 2px white; white-space: nowrap; overflow: hidden; <?php echo state_color($rs->state); ?>" >
          <center><?php echo get_state_name($rs->state); ?></center>
          <center><?php echo $this->user_model->get_name($rs->update_user); ?></center>
          <center><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></center>
        </div>
      <?php	endforeach; ?>
    <?php endif; ?>
  </div>

<?php $this->load->view('order_cancle_modal'); ?>

<div class="modal fade" id="cancle-reason-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:900px; max-width:95vw;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body" style="border-top:solid 1px #CCC; padding-top:10px;">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
            <table class="table table-bordered">
							<thead>
	            	<tr class="font-size-11">
                  <th class="min-width-200">เหตุผลในการยกเลิก</th>
									<th class="fix-width-200">User</th>
									<th class="fix-width-150">วันที่ยกเลิก</th>
	            	</tr>
							</thead>
							<tbody>
							<?php if( ! empty($cancle_reason)) : ?>
								<?php foreach($cancle_reason as $reason) : ?>
									<tr class="font-size-11">
										<td><?php echo $reason->reason; ?></td>
										<td><?php echo $this->user_model->get_name($reason->user); ?></td>
										<td><?php echo thai_date($reason->cancel_date, TRUE); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr><td colspan="3" class="text-center">--ไม่ระบุเหตุผล--</td></tr>
							<?php endif; ?>
							</tbody>
            </table>
          </div>
        </div>
       </div>
   </div>
 </div>
</div>

<script>
  function view_delivery() {
    let code = $('#order_code').val();
    let target = BASE_URL + 'inventory/delivery_order/view_detail/'+code;

    window.open(target, "_blank", "width=1000, height=800, scrollbars=yes");
  }

  function view_closed() {
    let code = $('#order_code').val();
    let target = BASE_URL + 'inventory/invoice/view_detail/'+code;

    window.open(target, "_blank", "width=1000, height=800, scrollbars=yes");
  }

</script>
