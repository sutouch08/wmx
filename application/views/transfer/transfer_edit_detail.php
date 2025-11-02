<div class="row">
  <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-4 padding-5">
    <label>โซนต้นทาง</label>
    <input type="text" class="form-control input-sm text-center" id="from-zone-code" />
  </div>
  <div class="col-lg-2-harf col-md-4 col-sm-3 col-xs-8 padding-5">
    <label class="not-show">โซนต้นทาง</label>
    <input type="text" class="form-control input-sm" id="from-zone-name" readonly />
  </div>
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-9 padding-5">
    <label>สินค้าในโซน</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="not-show">x</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดง</button>
  </div>
  <div class="col-lg-5 col-md-1-harf col-sm-2 col-xs-12 padding-5 text-right">
    <label class="display-block not-show">x</label>
    <button type="button" class="btn btn-xs btn-danger" onclick="removeChecked()"><i class="fa fa-trash"></i> ลบ</button>
  </div>
</div>
<hr class="margin-top-10"/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">
            <label>
              <input type="checkbox" class="ace chk-all" onchange="checkAll($(this))" />
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-50 text-center">#</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-300">สินค้า</th>
          <th class="fix-width-150">ต้นทาง</th>
          <th class="fix-width-80 text-center">จำนวน</th>
        </tr>
      </thead>
      <tbody id="transfer-table">
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
        <?php $total_wms = 0; ?>
        <?php if( ! empty($details)) : ?>
          <?php $zoneName = []; ?>
          <?php foreach($details as $rs) : 	?>
            <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
              <td class="middle text-center">
                <label>
                  <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>" data-item="<?php echo $rs->product_code; ?>" data-zone="<?php echo $rs->from_zone; ?>" />
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
              <td class="middle"><?php echo $rs->from_zone; ?></td>
              <td class="middle">
                <input type="text"
                class="form-conrol input-sm text-label text-center qty"
                id="qty-<?php echo $rs->id; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-item="<?php echo $rs->product_code; ?>"
                data-zone="<?php echo $rs->from_zone; ?>"
                data-qty="<?php echo $rs->qty; ?>" value="<?php echo number($rs->qty); ?>" readonly />
              </td>
            </tr>
            <?php $total_qty += $rs->qty; ?>
            <?php $no++; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="5" class="text-right">รวม</td>
            <td class="text-center">
              <input type="text" class="form-conrol input-xs text-label text-center" id="total" value="<?php echo number($total_qty); ?>" readonly />
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<div class="modal fade" id="item-zone-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:900px; max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #ddd;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title text-center" id="zone-modal-title">Stock In Zone</h4>
      </div>
      <div class="modal-body" id="item-zone-table" style="max-height:60vh; overflow:auto;">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
            <table class="table table-striped border-1" style="min-width:800px;">
              <thead>
                <tr class="font-size-11">
                  <th class="fix-width-50 text-center">#</th>
                  <th class="fix-width-150 text-center">SKU</th>
                  <th class="min-width-300 text-center">Description</th>
                  <th class="fix-width-100 text-center">In Zone</th>
                  <th class="fix-width-100 text-center">Qty</th>
                </tr>
              </thead>
              <tbody id="stock-zone-table">

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-default" onclick="closeModal('item-zone-modal')">Cancel</button>
        <button type="button" class="btn btn-white btn-warning" onclick="clearAll()">Clear All</button>
        <button type="button" class="btn btn-white btn-info" onclick="selectAll()">Select All</button>
        <button type="button" class="btn btn-white btn-primary" onclick="addToTransfer()">Add to transfer</button>
      </div>
    </div>
  </div>
</div>


<script id="rows-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if @last}}
      <tr>
        <td colspan="5" class="text-right">รวม</td>
        <td class="text-center">
          <input type="text" class="form-conrol input-xs text-label text-center" id="total" value="{{totalQty}}" readonly />
        </td>
      </tr>
    {{else}}
    <tr class="font-size-11" id="row-{{id}}">
      <td class="middle text-center">
        <label>
          <input type="checkbox" class="ace chk" value="{{id}}" data-id="{{id}}" data-item="{{product_code}}" data-zone="{{from_zone}}" />
          <span class="lbl"></span>
        </label>
      </td>
      <td class="middle text-center no"></td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
      <td class="middle">{{from_zone}}</td>
      <td class="middle">
        <input type="text"
        class="form-conrol input-sm text-label text-center qty"
        id="qty-{{id}}"
        data-id="{{id}}"
        data-item="{{product_code}}"
        data-zone="{{from_zone}}"
        data-qty="{{qty}}" value="{{qtyLabel}}" readonly/>
      </td>
    </tr>
    {{/if}}
  {{/each}}
</script>

<script id="row-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11" id="row-{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace chk" value="{{id}}" data-id="{{id}}" data-item="{{product_code}}" data-zone="{{from_zone}}" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle">{{product_name}}</td>
    <td class="middle">{{from_zone_name}}</td>
    <td class="middle">
      <input type="text"
      class="form-conrol input-sm text-label text-center qty"
      id="qty-{{id}}"
      data-id="{{id}}"
      data-item="{{product_code}}"
      data-zone="{{from_zone}}"
      data-qty="{{qty}}" value="{{qtyLabel}}" />
    </td>
  </tr>
</script>

<script id="stock-zone-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <tr class="font-size-11"><td colspan="5" class="text-center"> --- No data --- </td></tr>
    {{else}}
      <tr class="font-size-11">
        <td class="middle text-center">{{no}}</td>
        <td class="middle">{{product_code}}</td>
        <td class="middle">{{product_name}}</td>
        <td class="middle text-center">{{qtyLabel}}</td>
        <td class="middle text-cetner">
          <input type="number"
          class="form-control input-sm text-center zone-qty"
          data-item="{{product_code}}" data-name="{{product_name}}"
          data-zone="{{zone_code}}" data-qty="{{qty}}" value="" />
        </td>
      </tr>
    {{/if}}
  {{/each}}
</script>
