window.addEventListener('load', () => {
	invoice_init();
});


function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function deleteChecked(){
	load_in();

	setTimeout(function(){
		$('.chk:checked').each(function(){
			var id = $(this).data('id');
			var no = $(this).val();
			removeRow(no, id);
		})

		reIndex();
		recalTotal();
		load_out();
	}, 200)

}


function unsave(){
	var code = $('#return_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิกการบันทึก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function() {
			load_in();
			setTimeout(() => {
				$.ajax({
					url:HOME + 'unsave/'+code,
					type:'POST',
					cache:false,
					success:function(rs) {
						load_out();
						if(rs.trim() === 'success') {
							swal({
								title:'Success',
								text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
								type:'success',
								time:1000
							});

							setTimeout(function(){
								goEdit(code);
							}, 1200);
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
				});
			}, 100)
	});
}

var click = 0;

function save(save_type) {
	if(click == 0) {
		click = 1;
		clearErrorByClass('input-qty');

		var error = 0;
		let code = $('#return_code').val();
		let rows = [];

		$('.input-qty').each(function() {
			let el = $(this);

			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty > 0) {
				let sold = parseDefault(parseFloat(el.data('sold')), 0);

				if(qty <= sold) {
					let row = {
						'no' : el.data('no'),
						'product_code' : el.data('pdcode'),
						'product_name' : el.data('pdname'),
						'order_code' : el.data('order'),
						'sold_qty' : el.data('sold'),
						'qty' : el.val(),
						'price' : el.data('price'),
						'discount_percent' : el.data('discount')
					}

					rows.push(row);
				}
				else {
					el.hasError();
					error++;
				}
			}
		});

		if(error > 0) {
			click = 0;
			beep();
			swal({
				title:'ข้อผิดพลาด',
				text:'กรุณาแก้ไขข้อผิดพลาด',
				type:'warning'
			});

			return false;
		}

		if(rows.length == 0) {
			beep();
			swal({
				title:'ข้อผิดพลาด',
				text:'ไม่พบจำนวนในการรับคืน',
				type:'warning'
			});

			click = 0;
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'add_details/' + code +'/'+save_type,
			type:'POST',
			cache:false,
			data: {
				'data' : JSON.stringify(rows)
			},
			success:function(rs) {
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

						setTimeout(() => {
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


function saveAsDraft() {
	let error = 0;
	clearErrorByClass('input-qty');

	swal({
		title:'บันทึกชั่วคราว',
		text:"การบันทึกชั่วคราว จะบันทึกเฉพาะข้อมูลการรับไว้เท่านั้น ยังไม่มีผลกับสต็อก <br/>ต้องการดำเนินการต่อหรือไม่ ?",
		type:'info',
		html:true,
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		closeOnConfirm:true
	}, function() {
		setTimeout(() => {
			let error = 0;
			let h = {
				'code' : $('#code').val().trim(),
				'rows' : []
			}

			$('.input-qty').each(function() {
				let el = $(this);
				let qty = parseDefault(parseFloat(el.data('sold')), 0);
				let receive_qty = parseDefault(parseFloat(el.val()), 0);

				if(qty < receive_qty || receive_qty < 0) {
					el.hasError();
					error++;
				}
				else {
					h.rows.push({
						'id' : el.data('id'),
						'receive_qty' : receive_qty
					});
				}
			});

			if(error > 0) {
				beep();
				swal("พบรายการที่ไม่ถูกต้อง");
				return false;
			}

			if(h.rows.length == 0) {
				beep();
				swal("ไม่พบรายการรับเข้า");
				return false;
			}

			load_in();

			$.ajax({
				url:HOME + 'save_as_draft',
				type:'POST',
				cache:false,
				data:{
					data:JSON.stringify(h)
				},
				success:function(rs) {
					load_out();

					if(rs.trim() === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							viewDetail(h.code);
						}, 1200)
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
		}, 100)
	})
}


function saveReturn() {
	if(click == 0) {
		click = 1;
		let error = 0;
		clearErrorByClass('input-qty');

		let h = {
			'code' : $('#code').val().trim(),
			'rows' : []
		}

		$('.input-qty').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.data('sold')), 0);
			let receive_qty = parseDefault(parseFloat(el.val()), 0);

			if(receive_qty <= 0 || receive_qty != qty) {
				el.hasError();
				error++;
			}
			else {
				h.rows.push({
					'id' : el.data('id'),
					'product_code' : el.data('pdcode'),
					'receive_qty' : receive_qty
				});
			}
		});

		if(error > 0) {
			click = 0;
			beep();
			swal("พบรายการที่ไม่ถูกต้อง");
			return false;
		}

		if(h.rows.length == 0) {
			click = 0;
			beep();
			swal("ไม่พบรายการรับเข้า");
			return false;
		}
		
		load_in();

		$.ajax({
			url:HOME + 'save_return',
			type:'POST',
			cache:false,
			data:{
				data:JSON.stringify(h)
			},
			success:function(rs) {
				click = 0;
				load_out();
				if(rs.trim() === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						viewDetail(h.code);
					}, 1200)
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


function approve(){
	var code = $('#return_code').val();

	swal({
		title:'Approval',
		text:'ต้องการอนุมัติ '+code+' หรือไม่ ?',
		showCancelButton:true,
		confirmButtonColor:'#8bc34a',
		confirmButtonText:'อนุมัติ',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:true
	}, () => {
		load_in();
		setTimeout(() => {
			$.ajax({
				url:HOME + 'approve/'+code,
				type:'GET',
				cache:false,
				success:function(rs) {
					load_out();

					if(rs.trim() === 'success') {
						swal({
							title:'Approved',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							refresh();
						}, 1200);
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
			});

		}, 100);
	});
}


function unapprove() {
	var code = $('#return_code').val();
	swal({
		title:'Warning',
		text:'ต้องการยกเลิกการอนุมัติ '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#DD6B55',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, () => {
		load_in();

		$.ajax({
			url: HOME + 'unapprove/'+code,
			type:'GET',
			cache:false,
			success : function(rs) {
				load_out();
				if(rs === 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error',
							text:rs,
							type:'error'
						}, () => {
							window.location.reload();
						});
					}, 200);
				}
			}
		});
	});
}


function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	$('.edit').removeClass('has-error');

	let code = $('#return_code').val();
	let date_add = $('#date-add').val();
	let invoice = $('#invoice').val();
	let customer_code = $('#customer_code').val();
	let zone_code = $('#zone_code').val();
  let remark = $.trim($('#remark').val());

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
		$('#date-add').addClass('has-error');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		$('#invoice').addClass('has-error');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		$('#customer_code').addClass('has-error');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		$('#zone_code').addClass('has-error');
		return false;
	}

	let data = {
		'code' : code,
		'date_add' : date_add,
		'invoice' : invoice,
		'customer_code' : customer_code,
		'zone_code' : zone_code,
		'remark' : remark
	}

  load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(data)
		},
		success:function(rs){
			load_out();

			if(rs == 'success') {
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					text:'ต้องการโหลดข้อมูลรายการสินค้าใหม่หรือไม่ ?',
					type: 'success',
					showCancelButton: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Yes',
					closeOnConfirm: true
				}, function() {
					load_in();
					window.location.reload();
				});
			}
			else
			{
				swal({
					title:'Error!!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}


$('#date-add').datepicker({
	dateFormat:'dd-mm-yy'
});


$('#shipped-date').datepicker({
	dateFormat:'dd-mm-yy'
});


function add() {
	if(click == 0) {
		click = 1;
		clearErrorByClass('h');

		let date_add = $('#date-add').val();
		let invoice = $('#invoice').val();
		let customer_code = $('#customer_code').val();
		let zone_code = $('#zone_code').val();
		let remark = $.trim($('#remark').val());

		if(!isDate(date_add)){
			click = 0;
			$('#date-add').addClass('has-error');
	    swal('วันที่ไม่ถูกต้อง');
	    return false;
	  }

		if(invoice.length == 0){
			click = 0;
			swal('กรุณาอ้างอิงเลขที่บิล');
			$('#invoice').addClass('has-error');
			return false;
		}

		if(customer_code.length == 0){
			swal('กรุณาอ้างอิงลูกค้า');
			$('#customer_code').addClass('has-error');
			return false;
		}

		if(zone_code.length == 0) {
			click = 0;
			swal('กรุณาระบุโซนรับสินค้า');
			$('#zone_code').addClass('has-error');
			return false;
		}

		let data = {
			'date_add' : date_add,
			'invoice' : invoice,
			'customer_code' : customer_code,
			'zone_code' : zone_code,
			'remark' : remark
		}

	  load_in();

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(data)
			},
			success:function(rs) {
				load_out();
				click = 0;
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
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


function invoice_init() {
	let customer_code = $('#customer_code').val();

	$('#invoice').autocomplete({
		source:HOME + 'get_invoice_code/' + customer_code,
		autoFocus:true,
		open:function(event) {
			let ul = $(this).autocomplete('widget');
			ul.css('width', 'auto');
		},
		close:function(){
			var arr = $(this).val().split(' | ');

			if(arr.length > 2) {
				$(this).val(arr[0]);
				$('#customer_code').val(arr[1]);
				$('#customer').val(arr[2]);
				invoice_init();
			}
			else {
				$(this).val('');
				$('#customer_code').val('');
				$('#customer').val('');
				invoice_init();
			}
		}
	});
}


$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
			invoice_init();
		}
		else {
			$('#customer_code').val('');
			$('#customer').val('');
			invoice_init();
		}
	}
});


$('#customer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
			invoice_init();
		}else{
			$('#customer_code').val('');
			$('#customer').val('');
			invoice_init();
		}
	}
});


$('#zone_code').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#zone').val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});


$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#zone').val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});


function recalRow(no) {
	let el = $('#qty-'+no);
	el.clearError();
	let qty = parseDefault(parseFloat(el.val()), 0);
	let sold = parseDefault(parseFloat(el.data('sold')), 0);
	let sellPrice = parseDefault(parseFloat(el.data('sell')), 0);

	if( qty < 0) {
		qty = 0;
		el.val(0);
	}

	if(qty > sold) {
		el.hasError();
	}

	sellPrice = sellPrice < 0 ? 0 : sellPrice;

	let amount = qty * sellPrice;
	$('#amount-' + no).val(addCommas(amount.toFixed(2)));
	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;

	$('.input-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).val()), 0);
		totalQty += qty;
	})

	$('.amount-label').each(function() {
		let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
		totalAmount += amount;
	});

	$('#total-qty').text(addCommas(totalQty.toFixed(2)));
	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
}


function removeRow(no, id){
	if(id != '' && id != '0' && id != 0) {

		$.ajax({
			url:HOME + 'delete_detail/'+id,
			type:'GET',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					$('#row_' + no).remove();
					//reIndex();
					//recalTotal();
				}
				else
				{
					swal(rs);
					return false;
				}
			}
		});
	}
	else
	{
		$('#row_'+no).remove();
		// reIndex();
		// recalTotal();
	}
}


function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}

function acceptConfirm() {
	let code = $('#return_code').val();
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

			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(() => {
					window.location.reload();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	});

}


function rollBackExpired() {
	let code = $('#return_code').val();

	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการทำให้เอกสารนี้ยังไม่หมดอายุหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		closeOnConfirm:true
	},
	function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'roll_back_expired',
				type:'POST',
				cache:false,
				data:{
					'code' : code
				},
				success:function(rs) {
					load_out();

					if(rs == 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						});
					}
				},
				error:function(rs) {
					load_out();

					swal({
						title:'Error!',
						text:rs.responseText,
						type:'error',
						html:true
					});
				}
			})
		}, 200);
	});
}


$(document).ready(function(){
	load_out();
});
