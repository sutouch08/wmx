
<style>
	@media(min-width:768px) {
		#rc-div {
			margin-bottom:-30px;
		}
	}
</style>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <div class="tabable">
      <div class="col-lg-8 col-lg-offset-4 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-12 padding-5 bottom-btn" id="rc-div" style="z-index:1;">
        <?php if($order->is_backorder == 1) : ?>
          <button type="button" class="btn btn-xs btn-default pull-right margin-left-5" onclick="showBacklogs()">Back order logs</button>
        <?php endif; ?>
      </div>

      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#state" aria-expanded="true" aria-controls="state" role="tab" data-toggle="tab">สถานะ</a>
        </li>
        <li class="">
          <a href="#ship-to" aria-expanded="false" aria-controls="ship-to" role="tab" data-toggle="tab">ที่อยู่จัดส่ง</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">
        <div role="tabpanel" class="tab-pane active" id="state">
          <?php $this->load->view("orders/order_state"); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="ship-to" style="padding:15px 20px;">
          <div class="row">
            <div class="col-lg-3 col-md-5 col-sm-5 col-xs-5 padding-5">
              <label>ผู้จัดส่ง</label>
              <select class="width-100" id="id_sender">
                <option value="">เลือก</option>
                <?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
              </select>
            </div>
            <div class="col-lg-1 col-md-3 col-sm-3 col-xs-3 padding-5">
              <label class="display-block not-show">btn</label>
              <button type="button" class="btn btn-xs btn-success btn-100" onclick="setSender()">บันทึก</button>
            </div>
            <div class="divider-hidden visible-xs"></div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 hidden-xs">&nbsp;</div>
            <div class="col-lg-2 col-md-5 col-sm-5 col-xs-5 padding-5">
              <label>Tracking No.</label>
              <input type="text" class="width-100" id="tracking" value="<?php echo $order->shipping_code; ?>">
              <input type="hidden" id="trackingNo" value="<?php echo $order->shipping_code; ?>">
            </div>
            <div class="col-lg-1 col-md-3 col-sm-3 col-xs-3 padding-5">
              <label class="display-block not-show">btn</label>
              <button type="button" class="btn btn-xs btn-success btn-100" onclick="update_tracking()">บันทึก</button>
            </div>
          </div>

          <hr class="margin-top-15 margin-bottom-15"/>

          <div class="row">
            <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 padding-5">
              <label>ชื่อผู้รับ</label>
              <input type="text" class="width-100 r" id="name" value="<?php echo $ship_to->name; ?>" />
              <input type="hidden" id="id_address" value="<?php echo $ship_to->id; ?>" />
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
              <label>เบอร์โทร</label>
              <input type="text" class="width-100 text-center r" id="phone" value="<?php echo $ship_to->phone; ?>" />
            </div>
            <div class="col-lg-6 col-md-7 col-sm-4-harf col-xs-12 padding-5">
              <label>ที่อยุ่</label>
              <input type="text" class="width-100 r" id="address" value="<?php echo $ship_to->address; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
              <label>ตำบล</label>
              <input type="text" class="width-100 r" id="sub_district" value="<?php echo $ship_to->sub_district; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
              <label>อำเภอ</label>
              <input type="text" class="width-100 r" id="district" value="<?php echo $ship_to->district; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
              <label>จังหวัด</label>
              <input type="text" class="width-100 r" id="province" value="<?php echo $ship_to->province; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>ไปรษณีย์</label>
              <input type="text" class="width-100 r" id="postcode" value="<?php echo $ship_to->postcode; ?>" />
            </div>
            <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label class="display-block not-show">btn</label>
              <button class="btn btn-xs btn-primary btn-block" onclick="updateShpToAddress()">บันทึก</button>
            </div>
          </div>
        </div>
      </div><!-- tabcontent-->
    </div><!-- tabable-->
  </div><!-- col-lg-12 -->
</div><!-- rows -->

<hr class="padding-5"/>

<div class="modal fade" id="backlogs-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="min-width:500px; max-width:95vw;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">Back Order Details</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="min-width-200">Item</th>
                  <th class="fix-width-100 text-center">Order Qty</th>
                  <th class="fix-width-100 text-center">Available</th>
                </tr>
              </thead>
              <tbody>
          <?php if( ! empty($backlogs)) : ?>
            <?php foreach($backlogs as $rs) : ?>
              <tr>
                <td><?php echo $rs->product_code; ?></td>
                <td class="text-center"><?php echo number($rs->order_qty); ?></td>
                <td class="text-center"><?php echo number($rs->available_qty); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="3" class="text-center">-- No Items ---</td>
            </tr>
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
  $('#id_sender').select2();

function showBacklogs() {
  $('#backlogs-modal').modal('show');
}

function updateShpToAddress() {
  clearErrorByClass('r');

  let h = {
    'order_code' : $('#order_code').val(),
    'name' : $('#name').val().trim(),
    'address' : $('#address').val().trim(),
    'sub_district' : $('#sub_district').val().trim(),
    'district' : $('#district').val().trim(),
    'province' : $('#province').val().trim(),
    'postcode' : $('#postcode').val().trim(),
    'phone' : $('#phone').val().trim()
  }

  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if(h.address.length == 0) {
    $('#address').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'save_address',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        })
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}

</script>
