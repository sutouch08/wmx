<?php
echo $this->printer->doc_header();
$currency = getConfig('CURRENTCY');
?>
<?php if(!$id_rule) : ?>
<?php    $sc .= "ERROR"; ?>
<?php else : ?>
<div class="container">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <tr class="">
        <td class="width-15 middle text-right"><strong>รหัสกฎ</strong></td>
        <td class="width-20 middle"><?php echo $rule->code; ?></td>
        <td class="width-15 middle text-right"><strong>ชื่อกฏ</strong></td>
        <td class="width-50 middle" ><?php echo $rule->name; ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>รหัสนโยบาย</strong></td>
        <td class="middle"><?php echo empty($policy) ? '' : $policy->code; ?></td>
        <td class="middle text-right"><strong>ชื่อนโยบาย</strong></td>
        <td class="middle" ><?php echo empty($policy) ? '' : $policy->name; ?></td>
      </tr>
      <tr class="">
        <td class="middle text-right"><strong>วันที่เพิ่ม</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_add); ?></td>
        <td class="middle text-right"><strong>พนักงาน</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->user); ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>วันที่ปรับปรุง</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_upd); ?></td>
        <td class="middle text-right"><strong>พนักงาน</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->update_user); ?></td>
      </tr>

    <?php if($rule->type == 'D') : ?>
      <tr class="">
        <td class="middle text-right"><strong>ส่วนลด</strong></td>
        <td class="middle"><?php echo $rule->disc1.'%'; ?></td>
        <td class="middle text-right"><strong>Net Price</strong></td>
        <td class="middle">-</td>
      </tr>
    <?php endif; ?>
    <?php if($rule->type == 'N') : ?>
      <tr class="">
        <td class="middle text-right"><strong>ส่วนลด</strong></td>
        <td class="middle">-</td>
        <td class="middle text-right"><strong>Net Price</strong></td>
        <td class="middle"><?php echo number($rule->price , 2); ?></td>
      </tr>
    <?php endif; ?>
    <?php if($rule->type == 'F') : ?>
      <tr class="">
        <td class="middle text-right"><strong>Get Free</strong></td>
        <td class="middle text-center">Yes</td>
        <td class="middle">จำนวน</td>
        <td class="middle"><?php echo number($rule->freeQty); ?></td>
      </tr>
      <?php $qs = $this->discount_rule_model->getFreeProductRule($id_rule); ?>
      <tr class="">
        <td class="middle text-right"><strong>ของแถม</strong></td>
        <td colspan="3">
        <?php if( ! empty($qs)) : ?>
          <?php $i = 1; ?>
          <?php foreach($qs as $rs) : ?>
            <?php echo $i == 1 ? $rs->code : ', '.$rs->code; ?>
            <?php $i++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        </td>
      </tr>
    <?php endif; ?>
      <tr>
        <td class="middle text-right"><strong>จำนวนขั้นต่ำ</strong></td>
        <td class="middle"><?php echo ($rule->minQty > 0 ? number($rule->minQty) : 'No'); ?></td>
        <td class="middle text-right"><strong>มูลค่าขั้นต่ำ</strong></td>
        <td class="middle"><?php echo ($rule->minAmount > 0 ? number($rule->minAmount, 2).' '.$currency : 'No'); ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>รวมยอดได้</strong></td>
        <td class="middle"><?php echo $rule->canGroup == 1 ? 'Yes' : 'No'; ?></td>
      </tr>

      <tr>
        <td colspan="4" class="text-center"><strong>ลูกค้า</strong></td>
      </tr>
      <?php if($rule->all_customer == 1) : ?>
      <tr class="">
        <td class="middle text-right"><strong>ลูกค้า</strong></td>
        <td colspan="3"><?php echo 'ทั้งหมด'; ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rule->all_customer == 0) : ?>
      <?php   $ds = $this->discount_rule_model->getCustomerRuleList($id_rule); ?>
      <?php   if( ! empty($ds)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>รายชื่อลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($ds as $rs) : ?>
          <?php echo $i == 1 ? $rs->code.' : '.$rs->name : ', '.$rs->code.' : '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php   $qs = $this->discount_rule_model->getCustomerGroupRule($id_rule); ?>
      <?php   if( ! empty($qs)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>กลุ่มลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php   $qs = $this->discount_rule_model->getCustomerTypeRule($id_rule); ?>
      <?php   if( ! empty($qs)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ชนิดลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php   $qs = $this->discount_rule_model->getCustomerKindRule($id_rule); ?>
      <?php   if( ! empty($qs)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ประเภทลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php   $qs = $this->discount_rule_model->getCustomerAreaRule($id_rule); ?>
      <?php   if( ! empty($qs)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>เขตลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php   $qs = $this->discount_rule_model->getCustomerClassRule($id_rule); ?>
      <?php   if( ! empty($qs)) : ?>
        <tr class="">
          <td class="middle text-right"><strong>เกรดลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php endif; ?>
      <tr>
        <td colspan="4" class="text-center"><strong>สินค้า</strong></td>
      </tr>
      <?php if($rule->all_product == 1) : ?>
      <tr class="">
        <td class="middle text-right"><strong>สิ้นค้าทั้งหมด</strong></td>
        <td colspan="3"><?php echo 'Yes'; ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rule->all_product == 0) : ?>
        <?php   $qs = $this->discount_rule_model->getProductRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>รหัสสินค้า</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->code : ', '.$rs->code; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductModelRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>รุ่นสินค้า</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->code : ', '.$rs->code; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductMainGroupRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Main Group</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductGroupRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Sub Group</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductSegmentRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Segment</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductClassRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Class</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductFamilyRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Family</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductTypeRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Type</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductKindRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Kind</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductGenderRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Gender</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductSportTypeRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Sport Type</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductCollectionRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Club/Collection</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductBrandRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Brand</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php   $qs = $this->discount_rule_model->getProductYearRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="">
            <td class="middle text-right"><strong>Year</strong></td>
            <td class="middle" colspan="3">
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->year : ', '.$rs->year; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php endif; ?>


    <tr>
      <td colspan="4" class="text-center"><strong>ช่องทางการขายและการชำระเงิน</strong></td>
    </tr>
    <tr class="">
      <td class="middle text-right"><strong>ช่องทางขาย</strong></td>
      <td colspan="3">
        <?php if($rule->all_channels == 1) : ?>
            ทั้งหมด
        <?php else : ?>
          <?php $qs = $this->discount_rule_model->getChannelsRule($id_rule); ?>
          <?php if( ! empty($qs)) : ?>
            <?php $i = 1; ?>
            <?php foreach($qs as $rs) : ?>
              <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
            <?php endforeach; ?>
          <?php endif; ?>

        <?php endif; ?>
      </td>
    </tr>
    <tr class="">
      <td class="middle text-right"><strong>การชำระเงิน</strong></td>
      <td colspan="3">
        <?php if($rule->all_payment == 1) : ?>
            ทั้งหมด
        <?php else : ?>
          <?php $qs = $this->discount_rule_model->getPaymentRule($id_rule); ?>
          <?php if( ! empty($qs)) : ?>
            <?php $i = 1; ?>
            <?php foreach($qs as $rs) : ?>
              <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>

    </table>
  </div>
</div>
</div>
<?php endif; ?>
<?php
echo $this->printer->doc_footer();
 ?>
