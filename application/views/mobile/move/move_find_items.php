<div class="pg-summary pg-top">
  <div class="pg-summary-inner">
    <div class="pg-summary-content">
      <div class="summary-text width-100">
        <span class="float-left font-size-16 width-100 text-center" id="txt-item-code">ค้นหาสินค้า</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="page-wrap" id="items-list">


  </div>
</div>

<script id="items-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <div class="font-size-16 text-center">--- ไม่พบสินค้าในคลังนี้ ---</div>
    {{else}}
    <div class="list-block">
      <div class="list-link" >
        <div class="list-link-inner width-100">
          <div class="col-xs-9 padding-5"><span>{{zone_name}}</span></div>
          <div class="col-xs-3 padding-5 font-size-16 blue text-right">{{qty}}</div>          
        </div>
      </div>
    </div>
    {{/if}}
  {{/each}}
</script>

<div class="control-box" id="control-box">
  <div class="control-box-inner">
    <div class="width-100 hide" id="zone-bc">
      <div class="input-group width-100">
        <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-zone" inputmode="none" placeholder="Barcode Zone" autocomplete="off">
        <i class="ace-icon fa fa-keyboard-o fa-2x control-icon icon-keyboard hide" onclick="hideKeyboard()"></i>
        <i class="ace-icon fa fa-qrcode fa-2x control-icon icon-qr" onclick="showKeyboard()"></i>
      </div>
      <input type="hidden" id="zone-code" value="" />
    </div>
    <div class="width-100 padding-right-5 margin-bottom-10 text-center hide" id="item-qty">
      <button type="button" class="btn btn-default" id="btn-decrese"><i class="fa fa-minus"></i></button>
      <input type="number" class="form-control width-30 input-lg focus text-center" style="margin-left:10px; margin-right:10px; padding-left:10px; padding-right:10px;" id="qty" inputmode="numeric" autocomplete="off" placeholder="QTY" value="1">
      <button type="button" class="btn btn-default" id="btn-increse"><i class="fa fa-plus"></i></button>
    </div>

    <div class="input-group width-100" id="item-bc">
      <input type="text" class="form-control input-lg focus" style="padding-left:15px; padding-right:40px;" id="barcode-item" inputmode="none"  placeholder="Barcode Item" autocomplete="off">
      <i class="ace-icon fa fa-keyboard-o fa-2x control-icon icon-keyboard hide" onclick="hideKeyboard()"></i>
      <i class="ace-icon fa fa-qrcode fa-2x control-icon icon-qr" onclick="showKeyboard()"></i>
    </div>
  </div>
</div>

<div class="width-100 text-center bottom-info hide-text"><?php echo $doc->warehouse_code; ?></div>

<div class="pg-footer">
	<div class="pg-footer-inner">
		<div class="pg-footer-content text-right">
			<div class="footer-menu">
				<span class="pg-icon" onclick="refresh()">
					<i class="fa fa-refresh fa-2x"></i><span>รีเฟรซ</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('summary')">
					<i class="fa fa-tasks fa-2x"></i><span>รายการโอน</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('move_in')">
					<i class="fa fa-sign-in fa-2x"></i><span>ย้ายเข้า</span>
				</span>
			</div>
      <div class="footer-menu">
				<span class="pg-icon" onclick="showMoveTable('move_out')">
					<i class="fa fa-sign-out fa-2x"></i><span>ย้ายออก</span>
				</span>
			</div>
		</div>
 </div>
</div>
