<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:900px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-50 text-center">
            <label>
              <input type="checkbox" class="ace" id="chk-all" onchange="chkAll($(this))">
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-150 text-center">รหัสสินค้า</th>
          <th class="min-width-250 text-center">ชื่อสินค้า</th>
          <th class="fix-width-80 text-center">หน่วยนับ</th>
          <th class="fix-width-100 text-center">ราคา</th>
          <th class="fix-width-100 text-center">จำนวน</th>
          <th class="fix-width-100 text-center">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="detail-table">
      <?php if( ! empty($details)) : ?>
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
        <?php $total_amount = 0; ?>
        <?php foreach($details as $rs) : ?>
          <?php $active = ($po->status == 'P' && $rs->line_status == 'O') ? '' : 'disabled'; ?>
        <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center">
            <label>
              <input type="checkbox" class="ace del-chk" value="<?php echo $rs->id; ?>" data-product="<?php echo $rs->product_code; ?>">
              <span class="lbl"></span>
            </label>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-center"><?php echo $rs->unit_code; ?></td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right price"
            data-id="<?php echo $rs->id; ?>" id="price-<?php echo $rs->id; ?>"
            value="<?php echo $rs->price; ?>"
            onchange="updateDetail(<?php echo $rs->id; ?>)" <?php echo $active; ?> />
          </td>
          <td class="middle text-right">
            <input type="number" class="form-control input-sm text-right qty"
            data-id="<?php echo $rs->id; ?>" data-open="<?php echo $rs->open_qty; ?>"
            id="qty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>"
            onchange="updateDetail(<?php echo $rs->id; ?>)" data-valid="1" <?php echo $active; ?> />
          </td>
          <td class="middle text-right">
            <input type="text" class="form-control input-sm text-right text-label amount"
            id="amount-<?php echo $rs->id; ?>" value="<?php echo number($rs->line_total, 2); ?>" readonly />
          </td>
        </tr>
          <?php $no++; ?>
          <?php $total_qty += $rs->qty; ?>
          <?php $total_amount += $rs->line_total; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="6" class="text-right">รวม</td>
          <td class="text-right" id="total-qty"><?php echo number($total_qty, 2); ?></td>
          <td class="text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
        </tr>
      <?php else : ?>
        <tr>
          <td colspan="8" class="text-center">--- ไม่พบรายการ ---</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11" id="row-{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace del-chk" value="{{id}}" data-product="{{product_code}}">
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle text-center">{{unit_code}}</td>
    <td class="middle">
      <input type="number" class="form-control input-sm text-right price"
      data-id="{{id}}" id="price-{{id}}"
      value="{{price}}"
      onchange="updateDetail({{id}})" />
    </td>
    <td class="middle text-right">
      <input type="number" class="form-control input-sm text-right qty"
      data-id="{{id}}" data-open="{{open_qty}}"
      id="qty-{{id}}" value="{{qty}}"
      onchange="updateDetail({{id}})" data-valid="1" />
    </td>
    <td class="middle text-right">
      <input type="text" class="form-control input-sm text-right text-label amount"
      id="amount-{{id}}" value="{{line_total}}" readonly />
    </td>
  </tr>
</script>
