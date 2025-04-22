<?php
$cn = get_permission("SOCNDO", get_cookie("uid"), get_cookie("id_profile"));
$canCancleShipped = ($cn->can_add + $cn->can_edit + $cn->can_delete) > 0 ? TRUE : FALSE;
 ?>
<?php if($order->role == "S") : ?>
	<?php 	$paymentLabel = paymentLabel($order->code, paymentExists($order->code), $order->is_paid);	?>
	<?php if(!empty($paymentLabel)) : ?>
		<div class="row">
		  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		  	<?php echo $paymentLabel; ?>
		  </div>
		</div>
		<hr class="padding-5"/>
	<?php endif; ?>
<?php endif; ?>

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
      	<li>
          <a href="#address" aria-expanded="false" aria-controls="address" role="tab" data-toggle="tab">ที่อยู่</a>
        </li>
				<li>
          <a href="#sender" aria-expanded="false" aria-controls="sender" role="tab" data-toggle="tab">ผู้จัดส่ง</a>
        </li>
      <?php if($order->tax_status) : ?>
        <li>
          <a href="#tax" aria-expanded="false" aria-controls="tax" role="tab" data-toggle="tab">ใบกำกับภาษี</a>
        </li>
      <?php endif; ?>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px;">
				<div role="tabpanel" class="tab-pane fade" id="address">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive" style="max-height:250px; overflow:auto;">
              <table class="table table-bordered" style="min-width:900px; margin-bottom:0px; border-collapse:collapse; border:0;">
                <thead>
                  <tr style="background-color:white;">
                    <th colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
                      <p class="pull-right top-p">
                        <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button>
                      </p>
                    </th>
                  </tr>
                  <tr style="font-size:12px; background-color:white;">
                    <th class="fix-width-120">ชื่อเรียก</th>
                    <th class="fix-width-150">ผู้รับ</th>
                    <th class="min-width-250">ที่อยู่</th>
                    <th class="fix-width-150">โทรศัพท์</th>
                    <th class="fix-width-120"></td>
                  </tr>
                </thead>
                <tbody id="adrs">
          <?php if(!empty($addr)) : ?>
          <?php 	foreach($addr as $rs) : ?>
                  <tr style="font-size:12px;" id="<?php echo $rs->id; ?>">
                    <td align="center"><?php echo $rs->alias; ?></td>
                    <td><?php echo $rs->name; ?></td>
                    <td><?php echo $rs->address." ". $rs->sub_district." ".$rs->district." ".$rs->province." ". $rs->postcode; ?></td>
                    <td><?php echo $rs->phone; ?></td>
                    <td align="right">
              <?php if( $rs->id == $order->id_address ) : ?>
                      <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php else : ?>
                      <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>"  onclick="setAddress(<?php echo $rs->id; ?>)">
                        <i class="fa fa-check"></i>
                      </button>
              <?php endif; ?>
											<button type="button" class="btn btn-minier btn-primary" onclick="printOnlineAddress(<?php echo $rs->id; ?>, '<?php echo $order->code; ?>')">
												<i class="fa fa-print"></i>
											</button>
                      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                      <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                  </tr>

          <?php 	endforeach; ?>
          <?php else : ?>
                  <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
          <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- /row-->
      </div>

      <div role="tabpanel" class="tab-pane active" id="state">
				<?php $this->load->view("orders/order_state"); ?>
      </div>

			<div role="tabpanel" class="tab-pane fade" id="sender">
        <div class="row" style="padding:15px;">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3-harf padding-5 text-right">เลือกผู้จัดส่ง :</div>
              <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5 padding-5">
                <select class="form-control input-sm" id="id_sender">
                  <option value="">เลือก</option>
                  <?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
                </select>
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 padding-5">
                <button type="button" class="btn btn-xs btn-success btn-block" onclick="setSender()">บันทึก</button>
              </div>
            </div>
          </div>
          <div class="divider-hidden visible-xs"></div>
          <div class="divider-hidden visible-xs"></div>

          <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-4 col-xs-3-harf padding-5 text-right">Tracking No :</div>
              <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5 padding-5">
                <input type="text" class="form-control input-sm" id="tracking" value="<?php echo $order->shipping_code; ?>">
                <input type="hidden" id="trackingNo" value="<?php echo $order->shipping_code; ?>">
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 padding-5">
                <button type="button" class="btn btn-xs btn-success btn-block" onclick="update_tracking()">บันทึก</button>
              </div>
            </div>
          </div>
				</div>
			</div>

    <?php if($order->tax_status) : ?>
      <div role="tabpanel" class="tab-pane" id="tax" style="padding:15px 15px;">
        <div class="row">
      		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-8 padding-5">
      			<label>ชื่อสำหรับออกใบกำกับภาษี</label>
      			<input type="text" class="width-100" id="name" value="<?php echo $order->name; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
      			<label>Tax ID</label>
      			<input type="text" class="width-100 text-center" id="tax-id" value="<?php echo $order->tax_id; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-4 padding-5">
      			<label>รหัสสาขา</label>
      			<input type="text" class="width-100 text-center" id="branch-code" value="<?php echo $order->branch_code; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      			<label>ชื่อสาขา</label>
      			<input type="text" class="width-100 text-center" id="branch-name" value="<?php echo $order->branch_name; ?>" disabled/>
      		</div>
      		<div class="col-lg-2-harf col-md-2 col-sm-2 col-xs-4 padding-5">
      			<label>เบอร์โทร</label>
      			<input type="text" class="width-100 text-center" id="phone" value="<?php echo $order->phone; ?>" disabled/>
      		</div>
      		<div class="col-lg-4 col-md-7 col-sm-4-harf col-xs-12 padding-5">
      			<label>ที่อยุ่</label>
      			<input type="text" class="width-100" id="address" value="<?php echo $order->address; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
      			<label>ตำบล</label>
      			<input type="text" class="width-100" id="sub-district" value="<?php echo $order->sub_district; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
      			<label>อำเภอ</label>
      			<input type="text" class="width-100" id="district" value="<?php echo $order->district; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
      			<label>จังหวัด</label>
      			<input type="text" class="width-100" id="province" value="<?php echo $order->province; ?>" disabled/>
      		</div>
      		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      			<label>ไปรษณีย์</label>
      			<input type="text" class="width-100" id="postcode" value="<?php echo $order->postcode; ?>" disabled/>
      		</div>
      		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      			<label>Email</label>
      			<input type="text" class="width-100 text-center" value="<?php echo $order->email; ?>" disabled />
      		</div>
      	</div>
      </div>
    <?php endif; ?>

    </div>
  </div>
	</div>
</div>
<hr class="padding-5"/>

<div class="modal fade" id="cancle-shipped-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-9">
            <input type="text" class="form-control input-sm" id="cancle-shipped-reason" value=""/>
          </div>
          <div class="col-sm-3">
            <button type="button" class="btn btn-sm btn-info" onclick="cancle_order_shipped()">ตกลง</button>
          </div>
        </div>

       </div>
      <div class="modal-footer">

      </div>
   </div>
 </div>
</div>


<div class="modal fade" id="tracking-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">Tracking No</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="fix-width-150">Tracking No</th>
                  <th class="fix-width-120">Carton No</th>
                  <th class="fix-width-100">Qty</th>
                </tr>
              </thead>
              <tbody>
          <?php if( ! empty($tracking)) : ?>
            <?php foreach($tracking as $rs) : ?>
              <tr>
                <td><?php echo $rs->tracking_no; ?></td>
                <td><?php echo $rs->carton_code; ?></td>
                <td><?php echo number($rs->qty); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="3" class="text-center">-- No Tracking Number ---</td>
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
function showBacklogs() {
  $('#backlogs-modal').modal('show');
}

function show_tracking() {
  $('#tracking-modal').modal('show');
}

</script>
