<?php
echo $this->printer->doc_header();
$currency = getConfig('CURRENTCY');
?>
<?php if(!$id_rule) : ?>
<?php    $sc .= "ERROR"; ?>
<?php else : ?>
<div class="container">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <table class="table table-striped table-bordered margin-top-30">
      <tr class="">
        <td colspan="2" class="text-center"><strong>Discount Rule Summary</strong></td>
      </tr>
      <tr class="font-size-11">
        <td class="fix-width-150 middle text-right"><strong>Rule Code</strong></td>
        <td class="middle"><?php echo $rule->code; ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="text-right"><strong>Description</strong></td>
        <td class="" ><?php echo $rule->name; ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Promotion Code</strong></td>
        <td class="middle"><?php echo empty($policy) ? '' : $policy->code; ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Description</strong></td>
        <td class="middle" ><?php echo empty($policy) ? '' : $policy->name; ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Created date</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_add); ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Created by</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->user); ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Last update</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_upd); ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Updated by</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->update_user); ?></td>
      </tr>

    <?php if($rule->type == 'D') : ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Discount</strong></td>
        <td class="middle"><?php echo $rule->disc1.'%'; ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Net Price</strong></td>
        <td class="middle">-</td>
      </tr>
    <?php endif; ?>
    <?php if($rule->type == 'N') : ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Discont</strong></td>
        <td class="middle">-</td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Net Price</strong></td>
        <td class="middle"><?php echo number($rule->price , 2); ?></td>
      </tr>
    <?php endif; ?>
    <?php if($rule->type == 'F') : ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Get Free</strong></td>
        <td class="middle">Yes</td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right">Free Qty</td>
        <td class="middle"><?php echo number($rule->freeQty); ?></td>
      </tr>
      <?php $qs = $this->discount_rule_model->getFreeProductRule($id_rule); ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Free SKU</strong></td>
        <td >
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
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Min.Qty</strong></td>
        <td class="middle"><?php echo ($rule->minQty > 0 ? number($rule->minQty) : 'No'); ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Min. Amount</strong></td>
        <td class="middle"><?php echo ($rule->minAmount > 0 ? number($rule->minAmount, 2).' '.$currency : 'No'); ?></td>
      </tr>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Combinable</strong></td>
        <td class="middle"><?php echo $rule->canGroup == 1 ? 'Yes' : 'No'; ?></td>
      </tr>

      <tr class="font-size-11">
        <td colspan="2" class="text-center"><strong>Customers</strong></td>
      </tr>
      <?php if($rule->all_customer == 1) : ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>Customers</strong></td>
        <td><?php echo 'ทั้งหมด'; ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rule->all_customer == 0) : ?>
      <?php   $ds = $this->discount_rule_model->getCustomerRuleList($id_rule); ?>
      <?php   if( ! empty($ds)) : ?>
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Individual</strong></td>
          <td class="middle" >
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
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Customer Group</strong></td>
          <td class="middle" >
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
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Customer Type</strong></td>
          <td class="middle" >
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
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Customer Category</strong></td>
          <td class="middle" >
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
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Customer Area</strong></td>
          <td class="middle" >
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
        <tr class="font-size-11">
          <td class="middle text-right"><strong>Customer Grade</strong></td>
          <td class="middle" >
          <?php $i = 1; ?>
        <?php   foreach($qs as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <?php endif; ?>
      <tr class="font-size-11">
        <td colspan="2" class="text-center"><strong>Products</strong></td>
      </tr>
      <?php if($rule->all_product == 1) : ?>
      <tr class="font-size-11">
        <td class="middle text-right"><strong>All</strong></td>
        <td ><?php echo 'Yes'; ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rule->all_product == 0) : ?>
        <?php   $qs = $this->discount_rule_model->getProductRule($id_rule); ?>
        <?php   if( ! empty($qs)) : ?>
          <tr class="font-size-11">
            <td class="middle text-right"><strong>SKU</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Model</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Main Group</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Sub Group</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Segment</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Class</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Family</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Type</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Kind</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Gender</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Sport Type</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Club/Collection</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Brand</strong></td>
            <td class="middle" >
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
          <tr class="font-size-11">
            <td class="middle text-right"><strong>Year</strong></td>
            <td class="middle" >
              <?php $i = 1; ?>
              <?php   foreach($qs as $rs) : ?>
                <?php echo $i == 1 ? $rs->year : ', '.$rs->year; ?>
                <?php $i++; ?>
              <?php endforeach; ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php endif; ?>


    <tr class="font-size-11">
      <td colspan="2" class="text-center"><strong>Sales and payments channels</strong></td>
    </tr>
    <tr class="font-size-11">
      <td class="middle text-right"><strong>Sales chanels</strong></td>
      <td>
        <?php if($rule->all_channels == 1) : ?>
            All
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
    <tr class="font-size-11">
      <td class="middle text-right"><strong>Payments channels</strong></td>
      <td>
        <?php if($rule->all_payment == 1) : ?>
            All
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
