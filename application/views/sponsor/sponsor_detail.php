<?php
	$add = $this->pm->can_add;
	$edit = $this->pm->can_edit;
	$delete = $this->pm->can_delete;
	?>
<div class="row">
	<?php if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') : ?>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-8 padding-5 margin-bottom-10">
			<input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="Model Code" autofocus />
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">OK</button>
		</div>
		<div class="divider visible-xs"></div>
		<div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"> &nbsp; </div>
		<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5 margin-bottom-10">
			<input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code">
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
			<input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
			<input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
		</div>
		<div class="divider-hidden"></div>
	<?php endif; ?>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:920px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 text-center"></th>
					<th class="fix-width-40 text-center">#.</th>
					<th class="fix-width-150">Items</th>
					<th class="min-width-250">Description</th>
					<th class="fix-width-80 text-center">Price</th>
					<th class="fix-width-80 text-center">Qty</th>
					<th class="fix-width-100 text-right">Amount</th>
				</tr>
			</thead>
			<tbody id="detail-table">
				<?php   $no = 1;              ?>
				<?php   $total_qty = 0;       ?>
				<?php   $total_discount = 0;  ?>
				<?php   $total_amount = 0;    ?>
				<?php   $order_amount = 0;    ?>
				<?php if(!empty($details)) : ?>
					<?php   foreach($details as $rs) : ?>
						<tr class="font-size-11" id="row_<?php echo $rs->id; ?>">
							<td class="middle text-right">
								<?php if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') : ?>
									<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
										<button type="button" class="btn btn-minier btn-danger" onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')"><i class="fa fa-trash"></i></button>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td class="text-center"><?php echo $no; ?></td>
							<td class=""><?php echo $rs->product_code; ?></td>
							<td class=""><?php echo $rs->product_name; ?></td>
							<td class="text-center">
								<?php $disabled = ($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') ? "" : "disabled"; ?>
									<input type="number" class="width-100 text-right input-price"
									id="price-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
									value="<?php echo round($rs->price, 2); ?>" <?php echo $disabled; ?> />
							</td>
							<td class="text-center">
								<input type="number" class="width-100 text-right input-qty"
								id="qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo round($rs->qty, 2); ?>" <?php echo $disabled; ?> />
							</td>
							<td class="middle text-right">
								<?php echo number($rs->total_amount, 2); ?>
							</td>



						</tr>

						<?php			$total_qty += $rs->qty;	?>
						<?php 		$order_amount += $rs->qty * $rs->price; ?>
						<?php			$total_amount += $rs->total_amount; ?>
						<?php			$no++; ?>
					<?php   endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<!-- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
    <tr class="font-size-12">
    	<td colspan="6" rowspan="4"></td>
      <td style="border-left:solid 1px #CCC;"><b>จำนวนรวม</b></td>
      <td class="text-right"><b>{{ total_qty }}</b></td>
      <td class="text-center"><b>Pcs.</b></td>
    </tr>

    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>มูลค่ารวม</b></td>
      <td class="text-right"><b>{{ order_amount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>

    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>ส่วนลดรวม</b></td>
      <td class="text-right"><b>{{ total_discount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>

    <tr class="font-size-12">
      <td style="border-left:solid 1px #CCC;"><b>สุทธิ</b></td>
      <td class="text-right"><b>{{ net_amount }}</b></td>
      <td class="text-center"><b>THB.</b></td>
    </tr>
	{{else}}
        <tr class="font-size-10" id="row_{{ id }}">
            <td class="middle text-center">{{ no }}</td>
            <td class="middle text-center padding-0">
            	<img src="{{ imageLink }}" width="40px" height="40px"  />
            </td>
            <td class="middle">{{ productCode }}</td>
            <td class="middle">{{ productName }}</td>
						<td class="middle text-center">{{ price }}</td>
            <td class="middle text-center">{{ qty }}</td>
            <td class="middle text-center">{{ discount }}</td>
            <td class="middle text-right">{{ amount }}</td>
            <td class="middle text-right">
            <?php if( ($edit OR $add) && $order->is_approved == 0 ) : ?>
            	<button type="button" class="btn btn-xs btn-danger" onclick="removeDetail({{ id }}, '{{ productCode }}')"><i class="fa fa-trash"></i></button>
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
