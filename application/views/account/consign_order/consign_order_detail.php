
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:840px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center"></th>
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
        <tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>">
          <td class="middle">
            <?php if($rs->status == 0 && ($this->pm->can_edit OR $this->pm->can_delete)) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('<?php echo $rs->id; ?>', '<?php echo $rs->product_code; ?>')">
                <i class="fa fa-trash"></i>
              </button>
            <?php endif; ?>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle hide-text"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right">
            <span class="price" id="price-<?php echo $rs->id; ?>"><?php echo number($rs->price,2); ?></span>
            <input type="number" class="form-control input-xs text-center hide input-price" id="input-price-<?php echo $rs->id; ?>" value="<?php echo round($rs->price,2); ?>" />
          </td>
          <td class="middle text-right">
            <span class="disc" id="disc-<?php echo $rs->id; ?>"><?php echo $rs->discount; ?></span>
            <input type="text" class="form-control input-xs text-center hide input-disc" id="input-disc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount; ?>" />
          </td>
          <td class="middle text-right qty" id="qty-<?php echo $rs->id; ?>">
            <?php echo number($rs->qty); ?>
          </td>
          <td class="middle text-right amount" id="amount-<?php echo $rs->id; ?>">
            <?php echo number($rs->amount, 2); ?>
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

<?php if($doc->status == 2) : ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 red">
    <p>ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></p>
    <p>หมายเหตุ : <?php echo $doc->cancle_reason; ?></p>
  </div>
</div>
<?php endif; ?>

<script id="new-row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11 rox" id="row-{{id}}">
    <td class="middle">
      <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
        <i class="fa fa-trash"></i>
      </button>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle text-right">
      <span class="price" id="price-{{id}}">{{price}}</span>
      <input type="number" class="form-control input-xs text-center hide input-price" id="input-price-{{id}}" value="{{price}}" />
    </td>
    <td class="middle text-right">
      <span class="disc" id="disc-{{id}}">{{discount}}</span>
      <input type="text" class="form-control input-xs text-center hide input-disc" id="input-disc-{{id}}" value="{{discount}}" />
    </td>
    <td class="middle text-right qty" id="qty-{{id}}">{{qty}}</td>
    <td class="middle text-right amount" id="amount-{{id}}">{{amount}}</td>
  </tr>
</script>


<script id="row-template" type="text/x-handlebarsTemplate">
  <td class="middle text-center">
    <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('{{id}}', '{{product}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
  <td class="middle text-center no"></td>
  <td class="middle">{{product_code}}</td>
  <td class="middle">{{product_name}}</td>
  <td class="middle text-right price" id="price-{{id}}">{{price}}</td>
  <td class="middle text-right disc" id="disc-{{id}}">{{discount}}</td>
  <td class="middle text-right qty" id="qty-{{id}}">{{qty}}</td>
  <td class="middle text-right amount" id="amount-{{id}}">{{amount}}</td>
</script>

<script id="detail-template" type="text/x-handlebarsTemplate">
{{#each this}}
  {{#if @last}}
  <tr class="font-size-11" id="total-row">
    <td colspan="6" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-center">{{ total_qty }}</td>
    <td id="total-amount" colspan="2" class="middle text-center">{{ total_amount }}</td>
  </tr>
  {{else}}
  <tr class="font-size-11 rox" id="row-{{id}}">
    <td class="middle">
      <button type="button" class="btn btn-minier btn-danger" onclick="deleteRow('{{id}}', '{{product}}')"><i class="fa fa-trash"></i></button>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center padding-5 price" min="0" id="price-{{id}}" value="{{price}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="text" class="form-control input-xs text-center disc" id="disc-{{id}}" value="{{discount}}" onKeyup="recal('{{id}}')" onChange="recal('{{id}}')" />
    </td>
    <td class="middle text-center">
      <input type="number" class="form-control input-xs text-center qty" min="0" id="qty-{{id}}" value="{{qty}}" onKeyup="reCal('{{id}}')" onChange="reCal('{{id}}')" />
    </td>
    <td class="middle text-right amount" id="amount-{{id}}">{{ amount }}</td>
  </tr>
  {{/if}}

{{/each}}
</script>
