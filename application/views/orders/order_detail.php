<?php	$add = $this->pm->can_add; ?>
<?php $edit = $this->pm->can_edit; ?>
<?php	$delete = $this->pm->can_delete;	?>
<?php   $no = 1; ?>
<?php   $total_qty = 0; ?>
<?php   $total_discount = 0;  ?>
<?php   $total_amount = 0; ?>
<?php   $order_amount = 0; ?>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="border-collapse:inherit; margin-bottom:0px; min-width:1000px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-150">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">ราคา</th>
					<th class="fix-width-100 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">ส่วนลด</th>
					<th class="fix-width-150 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php if( ! empty($details)) : ?>
					<?php   foreach($details as $rs) : ?>
						<?php 	$discount = discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
						<?php 	$discLabel = discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
						<tr class="font-size-11" id="row_<?php echo $rs->id; ?>">
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
							<td class="middle text-center"><?php echo number($rs->qty); ?></td>
							<td class="middle text-center"><?php echo $discLabel; ?></td>
							<td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
						</tr>
						<?php $total_qty += $rs->qty;	?>
						<?php $total_discount += $rs->discount_amount; ?>
						<?php $order_amount += $rs->qty * $rs->price; ?>
						<?php $total_amount += $rs->total_amount; ?>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<?php 	$netAmount = $total_amount - $order->bDiscAmount;	?>
<div class="divider-hidden"></div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-2 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" readonly>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-2 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
      <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
        <textarea id="remark" maxlength="254" rows="3" class="form-control" onchange="updateRemark()" readonly><?php echo str_replace('"', '&quot;',$order->remark); ?></textarea>
      </div>
    </div>

  </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">จำนวน</label>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($total_qty); ?>" readonly>
      </div>
      <label class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-order" value="<?php echo number($order_amount, 2); ?>" readonly>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ส่วนลดรวม</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-disc" value="<?php echo number($total_discount, 2); ?>" readonly />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="net-amount" value="<?php echo number($netAmount, 2); ?>" readonly/>
      </div>
    </div>
  </div> <!-- form horizontal -->
</div>
<!--  End Order Detail ----------------->
<!-- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}

	{{else}}
				<tr class="font-size-10" id="row_{{id}}">
					<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
					<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
					<input type="hidden" id="currentDisc-{{id}}" value="{{discount}}">
					<td class="middle text-center">{{no}}</td>
					<td class="middle text-center">
					<?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add)) : ?>
						<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state != 8)) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="removeDetail({{id}}, '{{productCode}}')">
									<i class="fa fa-trash"></i>
								</button>
						<?php endif; ?>
					<?php endif; ?>
					</td>

					<td class="middle text-center padding-0">
						<img src="{{imageLink}}" width="40px" height="40px"  />
					</td>

					<td class="middle">{{productCode}}</td>

					<td class="middle">{{productName}}</td>

					<td class="middle text-center">
						<?php if( ($allowEditPrice && $order->state < 4) OR ($rs->is_count == 0 && $order->state < 8)  ) : ?>
							<input type="number"
								class="form-control input-sm text-right price-box e {{#if is_count}} hide {{/if}}"
								id="price_{{id}}"
								name="price[{{id}}]"
								value="{{price}}"
								data-price="{{price}}"
								data-count="{{is_count}}"
								onchange="recalItem({{id}}, '{{#if is_count}}N{{else}}Y{{/if}}')"/>
						<?php endif; ?>
						{{#if is_count}}
						<span class="price-label" id="price-label-{{id}}">	{{price}}</span>
						{{/if}}
					</td>

					<td class="middle text-center">
						<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
							<input type="number" class="form-control input-sm text-center line-qty e"
								id="qty_{{id}}"
								data-code="{{productCode}}"
								data-id="{{id}}"
								value="{{qty}}"
								data-qty="{{qty}}"
								data-count="{{is_count}}"
								onchange="updateItem({{id}})"
							/>
						<?php else : ?>
							{{qty}}
						<?php endif; ?>
					</td>

					<td class="middle text-center">
					<?php if( $order->state < 4 ) : ?>
						<input type="text"
							class="form-control input-sm text-center discount-box hide e"
							id="disc_{{id}}"
							name="disc[{{id}}]"
							value="{{discount}}"
							data-disc="{{discount}}"
							onchange="recalItem({{id}})"
							/>
					<?php endif; ?>
					<span class="discount-label" id="disc_label_{{id}}">{{discount}}</span>
					</td>

					<td class="middle text-right">
					<?php if($order->state < 4 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
						<input type="text"
							class="form-control input-sm line-total text-right e"
							id="line_total_{{id}}"
							data-id="{{id}}"
							onkeyup="recalDiscount({{id}})"
							value="{{amount}}"
							data-total="{{amount}}"
							readonly
							 />
					<?php else : ?>
						{{amount}}
					<?php endif; ?>
					</td>
			</tr>
	{{/if}}
{{/each}}
</script>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
