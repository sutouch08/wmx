
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1080px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">
            <label>
              <input type="checkbox" class="ace" id="chk-all" onchange="checkAll()" />
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100">bacode</th>
          <th class="fix-width-200">Item Code</th>
          <th class="min-width-250">Description</th>
          <th class="fix-width-100 text-right">Price</th>
          <th class="fix-width-100 text-right">Discount</th>
          <th class="fix-width-100 text-right">Qty</th>
          <th class="fix-width-100 text-right">Amount</th>
          <th class="fix-width-40"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $totalQty = 0; ?>
<?php  $totalAmount = 0; ?>
<?php  foreach($details as $rs) : ?>
        <tr class="font-size-11 rox" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center">
            <label>
              <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
              <span class="lbl"></span>
            </label>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->barcode; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle">
            <input type="text" class="width-100 text-label padding-0 font-size-11" style="height:auto;" value="<?php echo $rs->product_name; ?>" readonly/>
          </td>
          <td class="middle">
              <input type="text"
              class="form-control input-xs font-size-11 text-right input-price"
              id="price-<?php echo $rs->id; ?>"
              data-id="<?php echo $rs->id; ?>"
              value="<?php echo number($rs->price, 2); ?>" />
          </td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs font-size-11 text-right input-disc"
            id="disc-<?php echo $rs->id; ?>"
            data-id="<?php echo $rs->id; ?>"
            value="<?php echo $rs->discount; ?>" />
          </td>
          <td class="middle">
            <input type="text"
            class="form-control input-xs font-size-11 text-right input-qty"
            id="qty-<?php echo $rs->id; ?>"
            data-id="<?php echo $rs->id; ?>"
            value="<?php echo number($rs->qty); ?>" />
          </td>
          <td class="middle text-right">
            <input type="text"
            class="form-control input-xs font-size-11 text-label text-right amount"
            id="amount-<?php echo $rs->id; ?>"
            data-id="<?php echo $rs->id; ?>"
            value="<?php echo number($rs->amount, 2); ?>" readonly />
          </td>
          <td class="middle text-center">
            <?php if($rs->status == 0 && ($this->pm->can_edit OR $this->pm->can_delete)) : ?>
              <a class="red" href="#" onclick="deleteRow('<?php echo $rs->id; ?>', '<?php echo $rs->product_code; ?>')"><i class="fa fa-trash fa-lg"></i></a>
            <?php endif; ?>
          </td>
        </tr>

<?php  $no++; ?>
<?php  $totalQty += $rs->qty; ?>
<?php  $totalAmount += $rs->amount; ?>
<?php endforeach; ?>
      <tr id="total-row">
        <td colspan="7" class="middle text-right"><strong>รวม</strong></td>
        <td id="total-qty" class="middle text-right"><?php echo number($totalQty); ?></td>
        <td id="total-amount" class="middle text-right"><?php echo number($totalAmount,2); ?></td>
        <td></td>
      </tr>

<?php else : ?>
  <tr id="total-row">
    <td colspan="7" class="middle text-right"><strong>รวม</strong></td>
    <td id="total-qty" class="middle text-right">0</td>
    <td id="total-amount" class="middle text-right">0</td>
    <td></td>
  </tr>
<?php endif; ?>

      </tbody>
    </table>
  </div>
</div>


<script id="new-row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11 rox" id="row-{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace chk" value="{{id}}" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle text-center">{{barcode}}</td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">
      <input type="text" class="width-100 text-label padding-0 font-size-11" style="height:auto;" value="{{product_name}}" readonly/>
    </td>
    <td class="middle">
        <input type="text"
        class="form-control input-xs font-size-11 text-right input-price"
        id="price-{{id}}"
        data-id="{{id}}"
        value="{{price}}" />
    </td>
    <td class="middle">
      <input type="text"
      class="form-control input-xs font-size-11 text-right input-disc"
      id="disc-{{id}}"
      data-id="{{id}}"
      value="{{discount}}" />
    </td>
    <td class="middle">
      <input type="text"
      class="form-control input-xs font-size-11 text-right input-qty"
      id="qty-{{id}}"
      data-id="{{id}}"
      value="{{qty}}" />
    </td>
    <td class="middle text-right">
      <input type="text"
      class="form-control input-xs font-size-11 text-label text-right amount"
      id="amount-{{id}}"
      data-id="{{id}}"
      value="{{amount}}" readonly />
    </td>
    <td class="middle text-center">
      <a href="#" class="red" onClick="deleteRow('{{id}}', '{{product_code}}')"><i class="fa fa-trash fa-lg"></i></a>
    </td>
  </tr>
</script>
