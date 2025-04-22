var click = 0;
var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;


function add() {
	if(click == 0) {
		click = 1;

		let h = {
			'date_add' : $('#date-add').val(),
			'shipped_date' : $('#shipped-date').val(),
			'remark' : $('#remark').val().trim()
		}

		if( ! isDate(h.date_add)){
			click = 0;
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				click = 0;

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						goEdit(ds.code);
					}
					else {
						beep();
						showError(ds.message);
					}
				}
				else {
					beep();
					showError(rs);
				}
			},
			error:function(rs) {
				click = 0;
				beep();
				showError(rs);
			}
		})
	}
}


function editHeader(){
	$('.header-box').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader() {
	let h = {
		'code' : $('#receive_code').val(),
		'date_add' : $('#date-add').val(),
		'shipped_date' : $('#shipped-date').val(),
		'remark' : $('#remark').val().trim()
	}

	if( ! isDate(h.date_add)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	$.ajax({
		url:HOME + 'update_header',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
		},
		success:function(rs){
			if(rs.trim() === 'success'){
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('.header-box').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}
			else {
				beep();
				showError(rs);
			}
		},
		error:function(rs) {
			beep();
			showError(rs);
		}
	})
}


function receiveProduct(no) {
	var qty = isNaN( parseInt( $("#qty").val() ) ) ? 1 : parseInt( $("#qty").val() );
	var bc = $("#barcode");
	var input = $("#receive_"+ no);
	if(input.length == 1 ){
		bc.val('');
		bc.attr('disabled', 'disabled');
		var cqty = input.val() == "" ? 0 : parseInt(input.val());
		qty += cqty;
		input.val(qty);
		$("#qty").val(1);
		sumReceive();
		bc.removeAttr('disabled');
		bc.focus();
	}else{
		swal({
			title: "ข้อผิดพลาด !",
			text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
			type: "error"},
			function(){
				setTimeout( function(){ $("#barcode")	.focus(); }, 1000 );
		});
	}
}


function save() {
	if(click == 0) {
		click = 1;

		clearErrorByClass('c');

		code = $('#receive_code').val();

		//--- อ้างอิง PO Code
		order_code = $.trim($('#order_code').val());

		//--- เลขที่ใบส่งสินค้า
		invoice = $.trim($('#invoice').val());

		//--- zone id
		zone_code = $('#zone_code').val();
		zoneName = $('#zoneName').val();

		//--- approve key
		approver = $('#approver').val();

		//--- นับจำนวนรายการในใบสั่งซื้อ
		count = $(".receive-box").length;

		//--- ใบสั่งซื้อถูกต้องหรือไม่
		if(order_code == '') {
			click = 0;
			$('#order_code').hasError();
			swal('กรุณาระบุใบเบิกแปรสภาพ');
			return false;
		}

		//--- ตรวจสอบใบส่งของ (ต้องระบุ)
		if(invoice.length == 0) {
			click = 0;
			$('#invoice').hasError();
			swal('กรุณาระบุใบส่งสินค้า');
			return false;
		}

		//--- ตรวจสอบโซนรับเข้า
		if(zone_code == '' || zoneName == ''){
			click = 0;
			$('#zone_code').hasError();
			$('#zoneName').hasError();
			swal('กรุณาระบุโซนเพื่อรับเข้า');
			return false;
		}

		//--- มีรายการในใบสั่งซื้อหรือไม่
		if(count = 0) {
			click = 0;
			swal('Error!', 'ไม่พบรายการรับเข้า','error');
			return false;
		}

		var ds = {
			"receive_code" : code,
			"order_code" : order_code,
			"invoice" : invoice,
			"zone_code" : zone_code,
			"approver" : approver
		};

		var items = [];
		$('.receive-box').each(function(index, el) {
			let no = $(this).data('no');
			pdCode = $('#product_'+no).val();
			qty = parseDefault(parseInt($(this).val()), 0);
			price = parseDefault(parseFloat($('#price_'+no).val()), 0.00);
			amount = qty * price;

			if(qty > 0) {
				let item = {
					"product_code" : $('#product_'+no).val(),
					"product_name" : $('#product_name_'+no).val(),
					"price" : price,
					"qty" : qty,
					"amount" : amount
				}

				items.push(item);
			}
		});

		if(items.length == 0) {
			click = 0;
			swal('ไม่พบรายการรับเข้า');
			return false;
		}

		ds.items = items;

		load_in();

		$.ajax({
			url: HOME + 'save',
			type:"POST",
			cache:"false",
			data: {
				"data" : JSON.stringify(ds)
			},
			success: function(rs) {
				load_out();
				click = 0;
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(function(){
							viewDetail(code);
						}, 1200);
					}
					else {
						beep();
						showError(ds.message);
					}
				}
				else {
					beep();
					showError(re);
				}
			},
			error:function(rs) {
				beep();
				showError(rs);
			}
		});
	}
}	//--- end save


function checkLimit(){
	var limit = $("#overLimit").val();
	var over = 0;

	$(".receive-box").each(function(index, element) {
		var no = $(this).data('no');
		var limit = parseDefault(parseFloat($("#limit_"+no).val()), 0);
		var qty = parseDefault(parseFloat($("#receive_"+no).val()), 0);

		if(qty > limit) {
			over++;
		}
	});

	if( over > 0 ) {
		swal({
			title:'Error!',
			text:'ยอดรับเกินยอดค้างรับ กรุณาตรวจสอบ',
			type:'error'
		});
	}
	else {
		save();
	}
}


$("#sKey").keyup(function(e) {
    if( e.keyCode == 13 ){
		doApprove();
	}
});





function getApprove(){
	$("#approveModal").modal("show");
}


function click_init(){
	$('.barcode').click(function(){
		var barcode = $.trim($(this).text());
		$('#barcode').val(barcode);
		$('#barcode').focus();
	});
}





$("#approveModal").on('shown.bs.modal', function(){ $("#sKey").focus(); });



function validate_credentials(){
	var s_key = $("#s_key").val();
	var menu 	= $("#validateTab").val();
	var field = $("#validateField").val();
	if( s_key.length != 0 ){
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approverName").val(data.approver);
					closeValidateBox();
					callback();
					return true;
				}else{
					showValidateError(rs);
					return false;
				}
			}
		});
	}else{
		showValidateError('Please enter your secure code');
	}
}


function doApprove(){
	var s_key = $("#sKey").val();
	var menu = 'APOVPO'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = '';

	if( s_key.length > 0 )
	{
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approver").val(data.approver);
					$("#approveModal").modal('hide');
					save();
				}else{
					$('#approvError').text(rs);
					return false;
				}
			}
		});
	}
}





function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});

}


function rollbackStatus() {
	swal({
		title:'ย้อนสถานะ',
		text:'ต้องการยกเลิกการบันทึกหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		closeOnConfirm:true
	}, function() {
		load_in();
		let code = $('#receive_code').val();

		$.ajax({
			url:HOME + 'rollback_status',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				load_out();
				if(rs == 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							goEdit(code);
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						});
					}, 200);
				}
			}
		});
	});
}

function changePo(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		$("#receiveTable").html('');
		$('#btn-change-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#order_code').val('');
		$('#order_code').removeAttr('disabled');
		swal({
			title:'Success',
			text:'ยกเลิกข้อมูลเรียบร้อยแล้ว',
			type:'success',
			timer:1000
		});
		setTimeout(function(){
			$('#order_code').focus();
		}, 1200);
	});
}


function getData() {
	let code = $('#receive_code').val();
	let order_code = $("#order_code").val();

	load_in();

	$.ajax({
		url: HOME + 'get_transform_detail',
		type:"GET",
		cache:"false",
		data:{
			"receive_code" : code,
			"order_code" : order_code
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#template").html();
				var output = $("#receiveTable");
				render(source, data, output);
				$("#order_code").attr('disabled', 'disabled');

				$(".receive-box").keyup(function(e){
    				sumReceive();
				});

				$('.input-price').keyup(function() {
					sumReceive();
				});

				$('#btn-get-po').addClass('hide');
				$('#btn-change-po').removeClass('hide');
				click_init();
				setTimeout(function(){
					$('#invoice').focus();
				},1000);

			}else{
				swal("ข้อผิดพลาด !", rs, "error");
				$("#receiveTable").html('');
			}
		}
	});
}


$("#order_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_transform_code',
	autoFocus: true,
	close:function(){
		var code = $(this).val();
		var arr = code.split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
		}
	}
});




$('#order_code').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			getData();
		}
	}
});





$('#zone_code').autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code',
	autoFocus: true,
	close: function() {
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr.length == 2) {
			$('#zone_code').val(arr[0]);
			$('#zoneName').val(arr[1]);
		}
		else {
			$('#zone_code').val('');
			$('#zoneName').val('');
		}
	}
});



$("#zoneName").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2){
			$('#zone_code').val(arr[0]);
			$('#zoneName').val(arr[1]);
		}else{
			$('#zone_code').val('');
			$('#zoneName').val('');
		}
	}
});


$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});

$('#shipped-date').datepicker({ dateFormat: 'dd-mm-yy'});



function checkBarcode(){
	barcode = $('#barcode').val();

	if($('#'+barcode).length == 1){
		no = $('#'+barcode).val();
		receiveProduct(no);
	}else{
		$('#barcode').val('');
		swal({
			title: "ข้อผิดพลาด !",
			text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
			type: "error"
		},
			function(){
				setTimeout( function(){ $("#barcode")	.focus(); }, 1000 );
			});
	}
}



$("#barcode").keyup(function(e) {
  if( e.keyCode == 13 ){
		checkBarcode();
	}
});


function sumReceive(){
	let totalQty = 0;
	let totalAmount = 0;

	$(".receive-box").each(function(index, element) {
		let no = $(this).data('no');
	 	let limit = parseInt($('#backlog_'+no).val());
		let qty = parseDefault(parseInt($(this).val()), 0);
		let cost = parseDefault(parseFloat($('#price_'+no).val()), 0);
		let amount = qty * cost;

		totalQty += qty;
		totalAmount += amount;

		$('#line-amount-'+no).text(addCommas(amount.toFixed(2)));

		if(qty > limit) {
			$(this).addClass('has-error');
		}
		else {
			$(this).removeClass('has-error');
		}
  });

	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
	$("#total-receive").text(addCommas(totalQty));
}


function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();
  if(code.length == 0){
    $('#addForm').submit();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: HOME + 'is_exists/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            $('#addForm').submit();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}

$('#code').keyup(function(e){
	if(e.keyCode == 13){
		validateOrder();
	}
});



function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}

function acceptConfirm() {
	let code = $('#receive_code').val();
	let note = $.trim($('#accept-note').val());

	if(note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	load_in();

	$.ajax({
		url:HOME + 'accept_confirm',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"accept_remark" : note
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					//--- if export error
					if(ds.ex == 1) {
						swal({
							title:'Infomation',
							text:ds.message,
							type:'info'
						}, function() {
							viewDetail(code);
						})
					}
					else {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							viewDetail(code);
						}, 1200);
					}
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					});
				}
			}
			else
			{
				swal({
					title:'Error!',
					text: rs,
					type:'error',
					html:true
				});
			}
		}
	});
}
