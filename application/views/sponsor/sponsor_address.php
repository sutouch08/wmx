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
            <button type="button" class="btn btn-info btn-minier pull-right" onClick="addNewAddress()"><i class="fa fa-plus"></i></button>
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
          <td align="center"><?php echo $rs->name; ?></td>
          <td><?php echo $rs->consignee; ?></td>
          <td><?php echo $rs->address." ". $rs->sub_district." ".$rs->district." ".$rs->province." ". $rs->postcode; ?></td>
          <td><?php echo $rs->phone; ?></td>
          <td class="text-center">
    <?php if( $rs->id == $order->id_address ) : ?>
            <button type="button" class="btn btn-minier btn-success btn-address" id="btn-<?php echo $rs->id; ?>" onclick="setAddress(<?php echo $rs->id; ?>)">
              <i class="fa fa-check"></i>
            </button>
    <?php else : ?>
            <button type="button" class="btn btn-minier btn-address" id="btn-<?php echo $rs->id; ?>"  onclick="setAddress(<?php echo $rs->id; ?>)">
              <i class="fa fa-check"></i>
            </button>
    <?php endif; ?>            
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


<!--  Add New Address Modal  --------->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:90vw;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #ccc;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title-site text-center" >เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="address-id" />
        <div class="row">
          <div class="col-sm-12 col-xs-12">
            <label class="input-label">ผู้รับ</label>
            <input type="text" class="form-control input-sm e" name="Fname" id="Fname"  maxlength="100" placeholder="ชื่อผู้รับ (จำเป็น)" />
          </div>
          <div class="col-sm-12 col-xs-12">
            <label class="input-label">ที่อยู่</label>
            <input type="text" class="form-control input-sm e" name="address" id="address1" maxlength="200" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
          </div>

          <div class="col-sm-6 col-xs-12">
            <label class="input-label">ตำบล/แขวง</label>
            <input type="text" class="form-control input-sm e" name="sub_district" maxlength="100" id="sub_district" placeholder="ตำบล" />
          </div>
          <div class="col-sm-6 col-xs-12">
            <label class="input-label">อำเภอ/เขต</label>
            <input type="text" class="form-control input-sm e" name="district" maxlength="100" id="district" placeholder="อำเภอ (จำเป็น)" />
          </div>
          <div class="col-sm-6 col-xs-12">
            <label class="input-label">จังหวัด</label>
            <input type="text" class="form-control input-sm e" name="province" maxlength="100" id="province" placeholder="จังหวัด (จำเป็น)" />
          </div>
          <div class="col-sm-6 col-xs-12">
            <label class="input-label">รหัสไปรษณีย์</label>
            <input type="text" class="form-control input-sm e" name="postcode" id="postcode" maxlength="12" placeholder="รหัสไปรษณีย์" />
          </div>
          <div class="col-sm-6 col-xs-12">
            <label class="input-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control input-sm" name="phone" id="phone" maxlength="32" placeholder="000 000 0000" />
          </div>
          <div class="col-sm-6 col-xs-12">
            <label class="input-label">ชื่อเรียก</label>
            <input type="text" class="form-control input-sm e" name="alias" id="alias" maxlength="50" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveShipTo()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>

<script id="addressTemplate" type="text/x-handlebars-template">
  <td class="middle text-center">{{name}}</td>
  <td>{{consignee}}</td>
  <td>{{address}}</td>
  <td>{{phone}}</td>
  <td class="middle text-center">
    <button type="button" class="btn btn-minier btn-address" id="btn-{{id}}" onClick="setAddress({{id}})"><i class="fa fa-check"></i></button>
    <button type="button" class="btn btn-minier btn-warning" onClick="editAddress({{id}})"><i class="fa fa-pencil"></i></button>
    <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress({{id}})"><i class="fa fa-trash"></i></button>
  </td>
</script>


<script id="addressTableTemplate" type="text/x-handlebars-template">
  {{#each this}}
    <tr class="font-size-11" id="{{id}}">
      <td class="middle text-center">{{name}}</td>
      <td>{{consignee}}</td>
      <td>{{address}}</td>
      <td>{{phone}}</td>
      <td class="middle text-center">
        <button type="button" class="btn btn-minier btn-address" id="btn-{{id}}" onClick="setAddress({{id}})"><i class="fa fa-check"></i></button>
        <button type="button" class="btn btn-minier btn-warning" onClick="editAddress({{id}})"><i class="fa fa-pencil"></i></button>
        <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress({{id}})"><i class="fa fa-trash"></i></button>
      </td>
    </tr>
  {{/each}}
</script>

<script>
  $('#sender').select2();
</script>
