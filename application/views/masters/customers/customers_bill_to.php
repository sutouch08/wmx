<?php
$em = empty($bill) ? TRUE : FALSE;

$branch_code = $em ? '0000' : $bill->branch_code;
$branch_name = $em ? 'สำนักงานใหญ่' : $bill->branch_name;
$address = $em ? '' : $bill->address;
$sub_district = $em ? '' : $bill->sub_district;
$district = $em ? '' : $bill->district;
$province = $em ? '' : $bill->province;
$postcode = $em ? '' : $bill->postcode;
$country = $em ? 'TH' : $bill->country;
$phone = $em ? '' : $bill->phone;
?>
<?php $disabled = $view ? 'disabled' : ''; ?>

<div class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสสาขา</label>
    <div class="col-xs-6 col-sm-1 col-1-harf">
      <input type="text" name="branch_code" id="bill_branch_code" class="form-control input-sm b" placeholder="0000" value="<?php echo $branch_code; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ชื่อสาขา</label>
    <div class="col-xs-6 col-sm-3">
      <input type="text" name="branch_name" id="bill_branch_name" class="form-control input-sm b" placeholder="สำนักงานใหญ่" value="<?php echo $branch_name; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ที่อยู่</label>
    <div class="col-xs-12 col-sm-10">
      <input type="text" name="address" id="bill_address" class="form-control input-sm b" placeholder="อาคาร/หมู่ที่/ถนน **ต้องการ" value="<?php echo $address; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">ตำบล/แขวง</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" name="sub_district" id="bill_sub_district" class="form-control input-sm b" placeholder="ตำบล/แขวง **ต้องการ" value="<?php echo $sub_district; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">อำเภอ/เขต</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" name="district" id="bill_district" class="form-control input-sm b" placeholder="อำเภอ/เขต **ต้องการ" value="<?php echo $district; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">จังหวัด</label>
    <div class="col-xs-12 col-sm-4">
      <input type="text" name="province" id="bill_province" class="form-control input-sm b" placeholder="จังหวัด **ต้องการ" value="<?php echo $province; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสไปรษณีย์</label>
    <div class="col-xs-6 col-sm-1 col-1-harf">
      <input type="text" name="postcode" id="bill_postcode" class="form-control input-sm b" placeholder="10110" value="<?php echo $postcode; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">รหัสประเทศ</label>
    <div class="col-xs-6 col-sm-1 col-1">
      <input type="text" name="country" id="bill_country" class="form-control input-sm text-center b" placeholder="TH" value="<?php echo $country; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label no-padding-right">โทรศัพท์</label>
    <div class="col-xs-6 col-sm-3">
      <input type="text" name="phone" id="bill_phone" class="form-control input-sm b" placeholder="000 000 0000" value="<?php echo $phone; ?>" <?php echo $disabled; ?>/>
    </div>
  </div>
  <div class="divider-hidden"></div>
	<?php if($this->pm->can_edit && ! $view) : ?>
	  <div class="form-group">
	    <label class="col-sm-3 control-label no-padding-right"></label>
	    <div class="col-xs-12 col-sm-3">
	      <p class="pull-right">
	        <button type="button" class="btn btn-sm btn-success btn-100" onclick="updateBillTo()"><i class="fa fa-save"></i>&nbsp;&nbsp;Save</button>
	      </p>
	    </div>
	    <div class="help-block col-xs-12 col-sm-reset inline">
	      &nbsp;
	    </div>
	  </div>
	<?php endif; ?>

</div>
