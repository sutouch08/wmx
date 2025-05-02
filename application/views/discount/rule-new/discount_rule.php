<?php
$p_disabled = $rule->type === 'D' ? '' : 'disabled';
$n_disabled = $rule->type === 'N' ? '' : 'disabled';
$f_disabled = $rule->type === 'F' ? '' : 'disabled';
$visible_free = $rule->type == 'F' ? '' : 'hide';
?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 padding-5">
		<h4 class="title">Discount Conditions</h4>
	</div>
	<div class="divider margin-top-5"></div>
	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control left-label">
			<label>
				<input type="radio" class="ace disc-type" name="discType" value="N" onchange="toggleDiscType('N')" <?php echo is_checked('N', $rule->type); ?>>
				<span class="lbl">&nbsp;&nbsp; Net price</span>
			</label>
		</span>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon font-size-12">Sell Price</span>
			<input type="number" class="form-control input-sm text-center price-input e" id="net-price" value="<?php echo $rule->price; ?>" <?php echo $n_disabled; ?>/>
		</div>
	</div>
	<div class="divider"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control left-label">
			<label>
				<input type="radio" class="ace disc-type" name="discType" value="D" onchange="toggleDiscType('D')" <?php echo is_checked('D', $rule->type); ?> >
				<span class="lbl">&nbsp;&nbsp; Discount (%)</span>
			</label>
		</span>
	</div>
	<div class="col-lg-10 col-md-9 col-sm-9" style="padding-left:0;">
		<div class="col-lg-2-harf col-md-3 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-12">Step 1</span>
				<input type="number" class="form-control input-sm text-center disc-input e" id="disc1" value="<?php echo $rule->disc1; ?>"  <?php echo $p_disabled; ?>/>
			</div>
		</div>
		<div class="col-lg-2-harf col-md-3 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-12">Step 2</span>
				<input type="number" class="form-control input-sm text-center disc-input e" id="disc2" value="<?php echo $rule->disc2; ?>" <?php echo $p_disabled; ?>/>
			</div>
		</div>
		<div class="col-lg-2-harf col-md-3 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon font-size-12">Step 3</span>
				<input type="number" class="form-control input-sm text-center disc-input e" id="disc3" value="<?php echo $rule->disc3; ?>" <?php echo $p_disabled; ?>/>
			</div>
		</div>
	</div>
	<div class="divider"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control left-label">
			<label>
				<input type="radio" class="ace disc-type" name="discType" value="F" onchange="toggleDiscType('F')" <?php echo is_checked('F', $rule->type); ?>>
				<span class="lbl">&nbsp;&nbsp; ของแถม</span>
			</label>
		</span>
	</div>
	<div class="col-lg-10 col-md-9 col-sm-9">
		<div class="row">
			<div class="col-lg-2 col-md-3 col-sm-3 padding-5">
				<div class="input-group width-100">
					<span class="input-group-addon font-size-12">Free</span>
					<input type="number" class="form-control input-sm text-center free e" id="free-qty" value="<?php echo $rule->freeQty; ?>"  <?php echo $f_disabled; ?>/>
				</div>
			</div>
			<div class="col-lg-10 col-md-10 col-sm-10 padding-5">
				<span class="form-control text-label"> จากรายการต่อไปนี้	</span>
			</div>
			<div class="divider-hidden"></div>

			<div class="col-lg-5 col-md-5 col-sm-5 padding-5">
				<input type="text" class="form-control input-sm free" id="free-item-box" placeholder="รหัส/ชื่อสินค้า" <?php echo $f_disabled; ?> />
				<input type="hidden" id="free-id" data-code="" data-name="">
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
				<button type="button" class="btn btn-xs btn-primary btn-block free" onclick="addItemToList()" <?php echo $f_disabled; ?>><i class="fa fa-plus"></i> Add</button>
			</div>
			<div class="divider-hidden"></div>
			<div class="col-lg-12 col-md-12 col-sm-12 padding-5 table-responsive" style="max-height:300px; overflow:auto;">
				<table class="table table-striped border-1">
					<thead>
						<tr class="font-size-11">
							<th class="fix-width-40"></th>
							<th class="fix-width-150">SKU Code</th>
							<th class="min-width-250">Description</th>
							<th class="fix-width-60 text-center">
								<button type="button" class="btn btn-minier btn-danger btn-block" onclick="removeFreeItem()">Delete</button>
							</th>
						</tr>
					</thead>
					<tbody id="freeItemList">
						<?php if(!empty($free_items)) : ?>
							<?php foreach($free_items as $item) : ?>
								<tr id="free-row-<?php echo $item->product_id; ?>" class="free-row font-size-11">
									<td class="middle text-center">
										<label>
											<input type="checkbox" class="ace del-chk free-chk"
											id="free-item-<?php echo $item->product_id; ?>"
											data-code="<?php echo $item->product_code; ?>"
											data-name="<?php echo $item->product_name; ?>"
											value="<?php echo $item->product_id; ?>">
											<span class="lbl"></span>
										</label>
									</td>
									<td class="middle"><?php echo $item->product_code; ?></td>
									<td class="middle" colspan="2"><?php echo $item->product_name; ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="divider-hidden"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control text-label text-right">ใช้ร่วมกันได้หรือไม่</span>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5 margin-top-5">
		<label style="padding-top:5px;">
			<input name="can_sell" class="ace ace-switch ace-switch-7 free" type="checkbox" id="can-group" value="1" <?php echo is_checked($rule->canGroup,1); ?>  <?php echo $f_disabled; ?>/>
			<span class="lbl"></span>
		</label>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5 margin-top-5">
		<span class="red">** สามารถใช้ร่วมกับโปรโมชั่นส่วนเลดอื่นได้หรือไม่</span>
	</div>
	<div class="divider-hidden"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control text-label text-right">ใช้ซ้ำได้หรือไม่</span>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5 margin-top-5">
		<label style="padding-top:5px;">
			<input name="can_sell" class="ace ace-switch ace-switch-7 free" type="checkbox" id="can-repeat" value="1" <?php echo is_checked($rule->canRepeat,1); ?> <?php echo $f_disabled; ?> />
			<span class="lbl"></span>
		</label>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5 margin-top-5">
		<span class="red">** สำหรับของแถม เช่น ซื้อ 2 แถม 1 ถ้าถ้าซื้อ 6 ชิ้น จะได้แถม 3 ชิ้น</span>
	</div>

	<div class="divider"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control text-label text-right">จำนวนขั้นต่ำ</span>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2">
		<input type="number" class="form-control input-sm text-center e" id="min-qty" value="<?php echo $rule->minQty; ?>" />
	</div>
	<div class="divider-hidden"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control text-label text-right">มูลค่าขั้นต่ำ</span>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2">
		<input type="number" class="form-control input-sm text-center e" id="min-amount" value="<?php echo $rule->minAmount;?>" />
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5 margin-top-5">
		<span class="red">** กรณีของแถม จะคำนวนราคาหลังส่วนลด</span>
	</div>
	<div class="divider-hidden"></div>

	<div class="col-lg-2 col-md-3 col-sm-3">
		<span class="form-control text-label text-right">ลำดับความสำคัญ</span>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2">
		<select class="form-control input-sm" name="priority" id="priority">
			<option value="1" <?php echo is_selected('1', $rule->priority); ?>>1</option>
			<option value="2" <?php echo is_selected('2', $rule->priority); ?>>2</option>
			<option value="3" <?php echo is_selected('3', $rule->priority); ?>>3</option>
			<option value="4" <?php echo is_selected('4', $rule->priority); ?>>4</option>
			<option value="5" <?php echo is_selected('5', $rule->priority); ?>>5</option>
			<option value="6" <?php echo is_selected('6', $rule->priority); ?>>6</option>
			<option value="7" <?php echo is_selected('7', $rule->priority); ?>>7</option>
			<option value="8" <?php echo is_selected('8', $rule->priority); ?>>8</option>
			<option value="9" <?php echo is_selected('8', $rule->priority); ?>>9</option>
			<option value="10" <?php echo is_selected('10', $rule->priority); ?>>10</option>
		</select>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5 margin-top-5">
		<span class="red">** กรณีเงื่อนไขส่วนลดตรงกันมากกว่า 1 เงื่อนไข เงื่อนไขจะถูกเลือกตามลำดับความสำคัญ 1 - 10 โดยค่ามากหมายถึงสำคัญมาก</span>
	</div>

	<div class="divider"></div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="col-lg-2 col-md-3 col-sm-3">&nbsp;</div>
	<div class="col-sm-3">
		<button type="button" class="btn btn-sm btn-success btn-block" onclick="saveDiscount()"><i class="fa fa-save"></i> บันทึก</button>
	</div>
</div>


<script type="text/x-handlebarsTemplate" id="freeItemTemplate">
	<tr id="free-row-{{id}}" class="free-row font-size-11">
		<td class="middle text-center">
			<label>
				<input type="checkbox" class="ace del-chk free-chk" data-code="{{code}}" data-name="{{name}}" value="{{id}}">
				<span class="lbl"></span>
			</label>
		</td>
		<td class="middle">	{{code}}</td>
		<td class="middle" colspan="2">{{name}}</td>
	</tr>
</script>
