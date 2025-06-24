<?php if($this->pm->can_edit && ! $view) : ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"><i class="fa fa-plus"></i>&nbsp; เพิ่มใหม่</button>
  </div>
</div>
<div class="divider-hidden"></div>
<?php endif; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered border-1" style="min-width:750px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-100"></th>
          <th class="fix-width-100 text-center">ชื่อเรียก</th>
          <th class="fix-width-150">ผู้รับ</th>
          <th class="min-width-300">ที่อยู่</th>
          <th class="fix-width-100">เบอร์โทร</th>
        </tr>
      </thead>
      <tbody id="adrs">
        <?php if(!empty($addr)) : ?>
          <?php 	foreach($addr as $rs) : ?>
            <tr class="font-size-11" id="<?php echo $rs->id; ?>">
              <td class="middle">
                <?php if($this->pm->can_edit && ! $view) : ?>
                  <button type="button" class="btn btn-minier btn-warning" onClick="editAddress(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                  <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
              <td class="middle text-center"><?php echo $rs->name; ?></td>
              <td><?php echo $rs->consignee; ?></td>
              <td><?php echo $rs->address.' '. $rs->sub_district.' '.$rs->district.' '.$rs->province.' '. $rs->postcode; ?></td>
              <td><?php echo $rs->phone; ?></td>
            </tr>
          <?php 	endforeach; ?>
        <?php else : ?>
          <tr class="font-size-11"><td colspan="5" class="text-center">ไม่พบที่อยู่</td></tr>
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
        <form id="addAddressForm"	>
          <input type="hidden" id="id_address" />
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success" onClick="saveShipTo()" ><i class="fa fa-save"></i> บันทึก</button>
      </div>
    </div>
  </div>
</div>


<script id="addressTemplate" type="text/x-handlebars-template">
  <tr class="font-size-11" id="{{id}}">
    <td class="middle">
      {{#if default}}
        <button type="button" class="btn btn-minier btn-success btn-address" id="btn-{{id}}" onClick="setDefault({{id}})"><i class="fa fa-check"></i></button>
      {{else}}
        <button type="button" class="btn btn-minier btn-address" id="btn-{{id}}" onClick="setDefault({{id}})"><i class="fa fa-check"></i></button>
      {{/if}}
      <button type="button" class="btn btn-minier btn-warning" onClick="editAddress({{id}})"><i class="fa fa-pencil"></i></button>
      <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress({{id}})"><i class="fa fa-trash"></i></button>
    </td>
    <td class="middle text-center">{{alias}}</td>
    <td>{{name}}</td>
    <td>{{address}}</td>
    <td>{{phone}}</td>
  </tr>
</script>


<script id="addressTableTemplate" type="text/x-handlebars-template">
  {{#each this}}
    <tr class="font-size-11" id="{{id}}">
      <td class="middle">
        <button type="button" class="btn btn-minier btn-warning" onClick="editAddress({{id}})"><i class="fa fa-pencil"></i></button>
        <button type="button" class="btn btn-minier btn-danger" onClick="removeAddress({{id}})"><i class="fa fa-trash"></i></button>
      </td>
      <td class="middle text-center">{{name}}</td>
      <td>{{consignee}}</td>
      <td>{{address}}</td>
      <td>{{phone}}</td>
    </tr>
  {{/each}}
</script>
