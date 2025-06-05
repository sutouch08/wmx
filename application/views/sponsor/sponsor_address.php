<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-3 padding-5 padding-top-5 text-right">เลือกผู้จัดส่ง</div>
  <div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 padding-5">
    <select class="width-100" id="sender">
      <option value="">เลือก</option>
      <?php echo select_common_sender($order->customer_code, $order->id_sender); //--- sender helper?>
    </select>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="setSender()">บันทึก</button>
  </div>

  <div class="divider"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <table class="table table-bordered">
      <thead>
        <tr style="background-color:white;">
          <th colspan="6" align="center">ที่อยู่สำหรับจัดส่ง
            <button type="button" class="btn btn-info btn-minier pull-right" onClick="newAddress()"><i class="fa fa-plus"></i></button>
          </th>
        </tr>
        <tr class="font-size-11">
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
        <tr class="font-size-11" id="<?php echo $rs->id; ?>">
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

<script>
  $('#sender').select2();
</script>
