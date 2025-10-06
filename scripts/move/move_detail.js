function deleteMoveItem(id, code){
	var move_code = $('#move-code').val();

  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'delete_detail/'+ id,
			type:"POST",
      cache:false,
			data:{
				'code' : move_code,
				'id' : id
			},
			success: function(rs) {
				if( rs.trim() == 'success' ) {
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					$('#row-'+id).remove();
					reIndex();
					reCal();
				}
				else{
					beep();
					showError(rs);
				}
			},
			error:function(rs) {
				beep();
				showError(rs);
			}
		});
	});
}


function reCal(){
	let total = 0;
	$('.qty').each(function() {
		let qty = parseDefaultFloat(removeCommas($(this).text()), 0);
		total += qty;
	});

	$('#total').text(addCommas(total));
}


function getMoveTable(){
	var code	= $("#move-code").val();
	$.ajax({
		url: HOME + 'get_move_table/'+ code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#moveTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#move-list");
				render(source, data, output);
			}
		}
	});
}


function getTempTable() {
	var code = $("#move-code").val();
	$.ajax({
		url: HOME + 'get_temp_table/'+code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);

				setTimeout(() => {
					let zone = $('#to_zone_code').val().trim();

					if(zone.length) {
						$('#barcode-item-to').focus();
					}
					else {
						$("#toZone-barcode").focus();
					}
				}, 200);
			}
		}
	});
}


function addToMove() {
	if(click == 0) {
		click = 1;

		error = 0;

		$('#from-zone').clearError();
		$('#to-zone').clearError();
		clearErrorByClass('input-qty');

		let h = {
			'code' : $('#move-code').val(),
			'from_zone' : $('#from_zone_code').val(),
			'to_zone' : $('#to_zone_code').val(),
			'items' : []
		};

		if(h.from_zone.length == 0) {
			$('#from-zone').hasError();
			click = 0;
			return false;
		}

		if(h.to_zone.length == 0) {
			$('#to-zone').hasError();
			click = 0;
			return false;
		}

		if(h.from_zone == h.to_zone) {
			click = 0;
			$('#from-zone').hasError();
			$('#to-zone').hasError();
			showError("โซนต้นทาง - ปลายทาง ต้องเป็นคนละโซนกัน");
			return false;
		}

		$('.input-qty').each(function() {
			let limit = parseDefaultFloat($(this).data('qty'), 0);
			let qty = parseDefaultFloat($(this).val(), 0);

			if(qty < 0 || qty > limit) {
				$(this).hasError();
				error++;
			}

			if(qty > 0 && qty <= limit) {
				let row = {
					'code' : $(this).data('code'),
					'name' : $(this).data('name'),
					'qty' : qty
				};

				h.items.push(row);
			}
		});


		if(h.items.length == 0) {
			click = 0;
			return false;
		}

		if(error > 0) {
			click = 0;
			showError('กรุณาแก้ไขรายการที่ไม่ถูกต้อง');
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'add_to_move',
			type:'POST',
			cache:false,
			data: {
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();
				click = 0;
				if(rs.trim() === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						showMoveTable();
						getProductInZone();
					}, 1200);
				}
				else {
					showError(rs);
				}
			},
			error:function(rs) {
				click = 0;
				showError(rs);
			}
		})
	}
}


function selectAll() {
	$('.input-qty').each(function(index, el) {
		$(this).val($(this).data('qty'));
	});
}


function clearAll(){
	$('.input-qty').each(function(index, el){
		$(this).val('');
	});
}
