window.addEventListener('load', () => {
	fromZoneInit();
});


$('#item-code').keyup((e) => {
	if(e.keyCode == 13) {
		getProductInZone();
	}
});


//-------  ดึงรายการสินค้าในโซน
function getProductInZone() {
	$('#from-zone-code').clearError();

	let h = {
		'zone_code' : $('#from-zone-code').val().trim(),
		'item_code' : $('#item-code').val().trim()
	};

	if( h.zone_code.length > 0 ) {
			if(h.item_code.length == 0) {
				swal("กรุณาระบุสินค้า หากต้องการทั้งหมดใส่ *");
				return false;
			}

		load_in();

		$.ajax({
			url: HOME + 'get_product_in_zone',
			type:"POST",
      cache:"false",
      data:{
				'data' : JSON.stringify(h)
      },
			success: function(rs) {
				load_out();

				if( isJson(rs) ) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						$('#zone-modal-title').text(h.zone_code);

						let source = $('#stock-zone-template').html();
						let output = $('#stock-zone-table');

						render(source, ds.data, output);

						$('#item-zone-modal').modal('show');
					}
					else {
						showError(ds.message);
					}
				}
				else {
					showError(rs);
				}
			},
			error:function(rs) {
				showError(rs);
			}
		});
	}
	else {
		$('#from-zone-code').hasError().focus();
	}
}


function fromZoneInit() {
	let whsCode = $('#from-warehouse').val();

	$("#from-zone-code").autocomplete({
		source: HOME + 'get_transfer_zone/'+ whsCode,
		autoFocus: true,
		close: function() {
			let arr = $(this).val().split(' | ');

			if( arr.length == 2 ) {
				$(this).val(arr[0]);
				$('#from-zone-name').val(arr[1]);

				setTimeout(() => {
					$('#item-code').focus();
				}, 100);
			}
			else {
				$(this).val('');
				$('#from-zone-name').val('');
			}
		}
	});
}


$("#from-zone-code").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){
			getProductInZone();
		}, 100);
	}
});


function clearAll() {
	$('.zone-qty').val('');
}


function selectAll() {
	console.log('selectall');
	$('.zone-qty').each(function() {
		$(this).val($(this).data('qty'));
	});
}


function addToTransfer() {
	clearErrorByClass('zone-qty');
	let error = 0;

	let h = {
		'code' : $('#code').val(),
		'zone_code' : $('#from-zone-code').val().trim(),
		'items' : []
	}

	$('.zone-qty').each(function() {
		let el = $(this);
		let qty = parseDefaultFloat(el.val(), 0);
		let limit = parseDefaultFloat(el.data('qty'), 0);

		if(qty > 0) {
			if(qty > limit) {
				el.hasError();
				error++;
			}
			else {
				h.items.push({
					'product_code' : el.data('item'),
					'product_name' : el.data('name'),
					'qty' : el.data('qty')
				});
			}
		}
	})


	if(error > 0) {
		return false;
	}

	if(h.items.length == 0) {
		return false;
	}

	closeModal('item-zone-modal');

	load_in();

	$.ajax({
		url:HOME + 'add_to_transfer',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
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
					reloadTransferTable();
				}, 1000);
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
}


function reloadTransferTable() {
	let code = $('#code').val();

	$.ajax({
		url:HOME + 'get_transfer_table/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					let source = $('#rows-template').html();
					let output = $('#transfer-table');

					render(source, ds.data, output);

					reIndex();
					reCal();
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	})
}


function checkAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	let code = $('#code').val();
	let ids = [];

	if($('.chk:checked').length) {
		$('.chk:checked').each(function() {
			ids.push($(this).val());
		});

		if(ids.length > 0) {
			swal({
				title: 'คุณแน่ใจ ?',
				text: 'ต้องการลบ '+ ids.length +' รายการที่เลือก หรือไม่ ?',
				type: 'warning',
				showCancelButton: true,
				comfirmButtonColor: '#DD6855',
				confirmButtonText: 'ใช่ ฉันต้องการ',
				cancelButtonText: 'ไม่ใช่',
				closeOnConfirm: true
			},
			function() {
				load_in();

				setTimeout(() => {
					$.ajax({
						url:HOME + 'delete_detail',
						type:'POST',
						cache:false,
						data:{
							'code' : code,
							'ids' : JSON.stringify(ids)
						},
						success:function(rs) {
							load_out();

							if(rs == 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								$('.chk:checked').each(function() {
									let id = $(this).val();
									$('#row-'+id).remove();
								});

								reIndex();
								reCal();
							}
							else {
								swal({
									title:'Error!',
									text:rs,
									type:'error'
								});
							}
						}
					})
				}, 200);
			});
		}
	}
}


function reCal() {
	let total = 0;
	$('.qty').each(function(){
		total += parseDefaultFloat($(this).val(), 0);
	});

	$('#total').val(addCommas(total));
}
