window.addEventListener('load', () => {
  input_init();
});

$('#pd-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code_and_name',
  autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
});

$('#pd-code').keyup(function(e){
  if(e.keyCode == 13){
    getProductGrid();
  }
});



$('#item-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}

    $('#item-qty').focus();
  }
});


function getProductGrid(){
	var pdCode 	= $("#pd-code").val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: HOME + 'get_product_grid/'+pdCode,
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            $('#modal').css("width", ds.width + "px");
            $('#modalTitle').html(pdCode);
            $('#modalBody').html(ds.data);
            $('#orderGrid').modal('show');
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
        beep();
        showError(rs);
      }
		});
	}
}


//---- เพิ่มรายการสินค้าเช้าใบสั่งซื้อ
function addToPo(){
  if(click == 0) {
    click = 1;

    let h = {
      'code' : $('#code').val().trim(),
      'items' : []
    }

    $('.item-grid').each(function() {
      let qty = parseDefault(parseFloat($(this).val()), 0);

      if(qty > 0) {
        let item = {
          'code' : $(this).data('code'),
          'name' : $(this).data('name'),
          'price' : $(this).data('cost'),
          'unit_code' : $(this).data('unit'),
          'qty' : qty
        }

        h.items.push(item);
      }
    });

    if(h.items.length > 0) {
      $('#orderGrid').modal('hide');

      load_in();

      $.ajax({
        url:HOME + 'add_details',
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
              refresh();
            }, 1200);
          }
          else {
            click = 0;
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
    else {
      click = 0;
    }
  }
}


function addRow() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('c');
    let id = $('#id').val();
    let code = $('#code').val();
    let item_code = $('#item-code').val().trim();
    let qty = parseDefault(parseFloat($('#item-qty').val()), 0);

    if(item_code.length == 0) {
      $('#item-code').hasError();
      click = 0;
      return false;
    }

    if(qty <= 0) {
      $('#item-qty').hasError();
      click = 0;
      return false;
    }

    $.ajax({
      url:HOME + 'add_detail',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'code' : code,
        'product_code' : item_code,
        'qty' : qty
      },
      success:function(rs) {
        click = 0;

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            if(ds.method == 'add') {
              let source = $('#row-template').html();
              let output = $('#detail-table');

              render_prepend(source, ds.data, output);

              reIndex('no');

              input_init();

              recalAmount(ds.data.id);
            }

            if(ds.method == 'update') {
              let row_id = ds.data.id;
              $('#qty-'+row_id).val(ds.data.qty);
              $('#qty-'+row_id).data('open', ds.data.open_qty);
              recalAmount(row_id);
            }

            $('#item-qty').val('');
            $('#item-code').val('').focus();
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


function updateDetail(id) {
  let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
  let price = parseDefault(parseFloat($('#price-'+id).val()), 0);

  if(qty <= 0) {
    $('#qty-'+id).data('valid', 0);
    $('#qty-'+id).hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update_detail',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'qty' : qty,
      'price' : price
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        $('#qty-'+id).data('valid', 1);
        $('#qty-'+id).clearError();
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


function removeChecked(id) {
  let err = 0;
  let errmsg = "";
  let rows = $('.del-chk:checked').length;
  if(rows > 0) {
    clearErrorByClass('e');

    swal({
      title: "คุณแน่ใจ ?",
      text: "ต้องการลบ " + rows + " รายการที่เลือกหรือไม่ ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      closeOnConfirm: true
    }, function() {
      setTimeout(() => {
        let h = {
          'code' : $('#code').val(),
          'rows' : []
        }

        $('.del-chk:checked').each(function() {
          let id = $(this).val();
          let el = $('#qty-'+id);
          let qty = parseDefault(parseFloat(el.val()), 0);
          let open_qty = parseDefault(parseFloat(el.data('open')), 0);

          if(open_qty < qty) {
            err++;
            el.hasError();
          }
          else {
            h.rows.push({'id' : $(this).val()});
          }
        });

        if(err > 0) {
          beep();
          swal({
            title:'Error !',
            text:'ไม่สามารถลบบางรายการได้ เนื่องจากมีการรับเข้าไปแล้ว',
            type:'error'
          });

          return false;
        }

        if(h.rows.length == 0) {
          swal("กรุณาเลือกอย่างน้อย 1 รายการ");
          return false;
        }

        load_in();

        $.ajax({
          url: HOME + 'remove_checked_details',
          type:'POST',
          cache:false,
          data:{
            'data' : JSON.stringify(h)
          },
          success:function(rs){
            load_out();

            if(rs.trim() === 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              h.rows.forEach((row, i) => {
                $('#row-'+row.id).remove();
              });

              reIndex('no');

              recalTotal();
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
}


$('#item-qty').keyup(function(e){
  if(e.keyCode == 13){
    addRow();
  }
});


function input_init() {
  $('.price').keyup(function(){
    let id = $(this).data('id');
    recalAmount(id);
  });

  $('.qty').keyup(function(){
    let id = $(this).data('id');
    recalAmount(id);
  });
}


function recalAmount(id) {
  let price = parseDefault(parseFloat($('#price-'+id).val()), 0);
  let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
  let amount = qty * price;
  $('#amount-'+id).val(addCommas(amount.toFixed(2)));

  recalTotal();
}


function recalTotal(){
  let total_amount = 0;
  let total_qty = 0;

  $('.amount').each(function(){
    let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
    total_amount += amount;
  });

  $('.qty').each(function(){
    let qty = parseDefault(parseFloat($(this).val()), 0);
    total_qty += qty;
  });

  $('#total-qty').text(addCommas(total_qty.toFixed(2)));
  $('#total-amount').text(addCommas(total_amount.toFixed(2)));
}


function chkAll(el) {
  if(el.is(':checked')) {
    $('.del-chk').prop('checked', true);
  }
  else {
    $('.del-chk').prop('checked', false);
  }
}
