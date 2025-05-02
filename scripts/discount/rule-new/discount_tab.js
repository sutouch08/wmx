function saveDiscount() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id_rule').val(),
    'discType' : $("input[name='discType']:checked").val(),
    'price' : parseDefault(parseFloat($('#net-price').val()), 0),
    'disc1' : parseDefault(parseFloat($('#disc1').val()), 0),
    'disc2' : parseDefault(parseFloat($('#disc2').val()), 0),
    'disc3' : parseDefault(parseFloat($('#disc3').val()), 0),
    'freeQty' : parseDefault(parseFloat($('#free-qty').val()), 0),
    'minQty' : parseDefault(parseFloat($('#min-qty').val()), 0),
    'minAmount' : parseDefault(parseFloat($('#min-amount').val()), 0),
    'canGroup' : $('#can-group').is(':checked') ? 1 : 0,
    'canRepeat' : $('#can-repeat').is(':checked') ? 1 : 0,
    'priority' : $('#priority').val(),
    'freeItemList' : []
  }

	if(h.discType == 'F' && h.freeQty <= 0) {
		swal('Warning', 'กรุณาระบุจำนวนของแถม', 'error');
		$('#free-qty').hasError();
    return false;
	}

  if(h.discType == 'N' && h.price <= 0) {
    swal('Warning', 'ราคาขายต้องมากกว่า 0', 'error');
		$('#net-price').hasError();
    return false;
  }

  if(h.discType == 'D' && (h.disc1 <= 0 || h.disc1 > 100 ) && h.freeQty <= 0) {
    swal('Warning', 'ส่วนลดไม่ถูกต้อง', 'error');
		$('#disc1').hasError();
    return false;
  }

	if(h.discType == 'D' && ((h.disc1 <= 0 && h.disc2 > 0) || h.disc2 < 0 || h.disc2 > 100 || (h.disc1 == 100 && h.disc2 > 0 ))) {
    swal('Warning', 'ส่วนลดไม่ถูกต้อง', 'error');
		$('#disc2').hasError();
    return false;
  }

	if(h.discType == 'D' && ((h.disc2 <= 0 && h.disc3 > 0) || h.disc3 < 0 || h.disc3 > 100 || ((h.disc2 == 100 || h.disc2 == 0) && h.disc3 > 0 ))) {
    swal('Warning', 'ส่วนลดไม่ถูกต้อง', 'error');
		$('#disc3').hasError();
    return false;
  }

  if(h.minQty < 0) {
    swal('Warning', 'จำนวนขั้นต่ำต้องไม่น้อยกว่า 0', 'error');
		$('#min-qty').hasError();
    return false;
  }

  if(h.minAmount < 0){
    swal('Warning', 'มูลค่าขั้นต่ำต้องไม่น้อยกว่า 0', 'error');
		$('#min-amount').hasError();
    return false;
  }

  if(h.discType == 'F' && $('.free-chk').length == 0) {
    swal('Warning', 'กรุณาระบุสินค้าที่ต้องการแถม', 'error');
    return false;
  }

  if(h.discType == 'F')
  {
    $('.free-chk').each(function() {
      h.freeItemList.push({
        'id' : $(this).val(),
        'code' : $(this).data('code')
      })
    })
  }

  load_in();

  $.ajax({
    url:HOME + 'set_discount',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();
      if(rs.trim() == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

				setTimeout(function() {
					window.location.reload();
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
}


$('#free-item-box').autocomplete({
	source: BASE_URL + 'auto_complete/get_item_code_name_id',
	autoFocus:true,
	close:function() {
		let txt = $(this).val();
		let arr = txt.split(' | ');

		if(arr.length == 3) {
			let code = arr[0];
			let name = arr[1];
      let id = arr[2];

			$(this).val(code);
      $('#free-id').val(id);
      $('#free-id').data('code', code);
      $('#free-id').data('name', name);
		}
		else {
      $(this).val('');
      $('#free-id').val('');
      $('#free-id').data('code', '');
      $('#free-id').data('name', '');
		}
	}
});


function addItemToList() {
	let txt = $('#free-item-box').val();
	let id = $('#free-id').val();
  let code = $('#free-id').data('code');
  let name = $('#free-id').data('name');

	if(txt != "" && id != "" && code != "") {
		if($('#free-item-'+id).length == 0) {
      let ds = {"id" : id, "code" : code, "name" : name}
      let source = $('#freeItemTemplate').html();
      let output = $('#freeItemList');
      render_append(source, ds, output);
      $('#free-id').val('');
      $('#free-id').data('code', '');
      $('#free-id').data('name', '');
      $('#free-item-box').val('').focus();
		}
	}
}


$('#free-item-box').keyup(function(e) {
	if(e.keyCode === 13) {
		setTimeout(() => {
      addItemToList();
    },100);
	}
});


function removeFreeItem() {
	$('.del-chk:checked').each(function() {
    let id = $(this).val();
    $('#free-row-'+id).remove();
	})
}


function toggleDiscType(option) {
	if(option == 'N') {
		$('.disc-input').attr('disabled', 'disabled');
		$('.free').attr('disabled', 'disabled');
		$('#visible-free').addClass('hide');
		$('.price-input').removeAttr('disabled');
		$('#net-price').focus();
		return;
	}

	if(option == 'D') {
		$('.price-input').attr('disabled', 'disabled');
		$('.free').attr('disabled', 'disabled');
		$('#visible-free').addClass('hide');
		$('.disc-input').removeAttr('disabled');
		$('#disc1').focus();
		return;
	}

	if(option == 'F') {
    let freeQty = parseDefault(parseFloat($('#free-qty').val()), 0);

    if(freeQty <= 0) {
      $('#free-qty').val(1);
    }
    
		$('.disc-input').attr('disabled', 'disabled');
		$('.price-input').attr('disabled', 'disabled');
		$('.free').removeAttr('disabled');
		$('#visible-free').removeClass('hide');
		$('#free-item-box').focus();
		return;
	}
}
