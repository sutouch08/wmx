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
					<th class="fix-width-80 text-right">Price</th>
					<th class="fix-width-80 text-right">Qty</th>
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
						<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center">
								<?php if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') : ?>
									<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
										<a href="Javascript:removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
											<i class="fa fa-times fa-lg red"></i>
										</a>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle">
								<?php $disabled = ($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R') ? "" : "disabled"; ?>
									<input type="number" class="form-control input-xs text-right text-label input-price e"
									id="price-<?php echo $rs->id; ?>"
									data-id="<?php echo $rs->id; ?>"
									data-sku="<?php echo $rs->product_code; ?>"
									data-price="<?php echo $rs->price; ?>"
									data-count="<?php echo $rs->is_count; ?>"
									value="<?php echo round($rs->price, 2); ?>"
									onchange="updateItemPrice(<?php echo $rs->id; ?>)"
									<?php echo $disabled; ?> />
							</td>
							<td class="middle">
								<input type="number" class="form-control input-xs text-right text-label input-qty e"
								id="qty-<?php echo $rs->id; ?>"
								data-id="<?php echo $rs->id; ?>"
								data-sku="<?php echo $rs->product_code; ?>"
								data-count="<?php echo $rs->is_count; ?>"
								data-qty="<?php echo $rs->qty; ?>"
								value="<?php echo round($rs->qty, 2); ?>"
								onchange="updateItem(<?php echo $rs->id; ?>)"
								<?php echo $disabled; ?> />
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-right text-label line-total"
									id="line-total-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
									data-sku="<?php echo $rs->product_code; ?>"
									value="<?php echo number($rs->total_amount, 2); ?>" readonly />
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

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:60vh; padding:0; overflow:auto;" id="modalBody">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>
<!-- order detail template ------>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
