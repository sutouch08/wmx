
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:840px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">
            <label>
              <input type="checkbox" class="ace" id="chk-all" />
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-50 text-center">ลำดับ</th>
          <th class="fix-width-150">รหัสสินค้า</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="detail-table">
        <?php  $no = 1; ?>
        <?php  $totalQty = 0; ?>
        <?php  $totalAmount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
          <td class="middle text-center">
            <?php if($rs->status == 'O' && ($this->pm->can_edit OR $this->pm->can_delete)) : ?>
              <label>
                <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
                <span class="lbl"></span>
              </label>
            <?php endif; ?>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle hide-text"><?php echo $rs->product_name; ?></td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs text-right font-size-11 input-price e"
            id="price-<?php echo $rs->id; ?>" data-prev="<?php echo number($rs->price, 2); ?>" data-id="<?php echo $rs->id; ?>"
            onchange="updateRowPrice(<?php echo $rs->id; ?>)"
            value="<?php echo number($rs->price,2); ?>" />
          </td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs text-right font-size-11 input-disc e"
            id="disc-<?php echo $rs->id; ?>" data-prev="<?php echo $rs->discount; ?>" data-id="<?php echo $rs->id; ?>"
            onchange="updateRowDisc(<?php echo $rs->id; ?>)"
            value="<?php echo $rs->discount; ?>" />
          </td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs text-right font-size-11 qty e"
            id="qty-<?php echo $rs->id; ?>" data-prev="<?php echo number($rs->qty); ?>" data-id="<?php echo $rs->id; ?>"
            onchange="updateRowQty(<?php echo $rs->id; ?>)"
            value="<?php echo number($rs->qty); ?>" />
          </td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs text-right font-size-11 amount"
            id="amount-<?php echo $rs->id; ?>"
            value="<?php echo number($rs->amount, 2); ?>" readonly />
          </td>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>
<?php endif; ?>
      <tr class="font-size-11" id="total-row">
        <td colspan="6" class="middle text-right"><strong>รวม</strong></td>
        <td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
        <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>

<?php if($doc->status == 'D') : ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 red">
    <p>ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></p>
    <p>หมายเหตุ : <?php echo $doc->cancle_reason; ?></p>
  </div>
</div>
<?php endif; ?>

<script id="new-row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11 rox" id="row-{{id}}" data-id="{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace chk" value="{{id}}" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right font-size-11 input-price e" id="price-{{id}}" data-prev="{{price}}" data-id="{{id}}" onchange="updateRowPrice({{id}})" value="{{price}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right font-size-11 input-disc e" id="disc-{{id}}" data-prev="{{discount}}" data-id="{{id}}" onchange="updateRowDisc({{id}})" value="{{discount}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right font-size-11 qty e" id="qty-{{id}}" data-prev="{{qty}}" data-id="{{id}}" onchange="updateRowQty({{id}})" value="{{qty}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-xs text-right font-size-11 amount" id="amount-{{id}}" value="{{amount}}" readonly />
    </td>
  </tr>
</script>


<script id="row-template" type="text/x-handlebarsTemplate">
  <td class="middle text-center">
    <label>
      <input type="checkbox" class="ace chk" value="{{id}}" />
      <span class="lbl"></span>
    </label>
  </td>
  <td class="middle text-center no"></td>
  <td class="middle">{{product_code}}</td>
  <td class="middle">{{product_name}}</td>
  <td class="middle">
    <input type="text" class="form-control input-xs text-right font-size-11 input-price e" id="price-{{id}}" data-prev="{{price}}" data-id="{{id}}" onchange="updateRowPrice({{id}})" value="{{price}}" />
  </td>
  <td class="middle">
    <input type="text" class="form-control input-xs text-right font-size-11 input-disc e" id="disc-{{id}}" data-prev="{{discount}}" data-id="{{id}}" onchange="updateRowDisc({{id}})" value="{{discount}}" />
  </td>
  <td class="middle">
    <input type="text" class="form-control input-xs text-right font-size-11 qty e" id="qty-{{id}}" data-prev="{{qty}}" data-id="{{id}}" onchange="updateRowQty({{id}})" value="{{qty}}" />
  </td>
  <td class="middle">
    <input type="text" class="form-control input-xs text-right font-size-11 amount" id="amount-{{id}}" value="{{amount}}" readonly />
  </td>
</script>
