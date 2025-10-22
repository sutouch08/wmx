var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  setFocus();
});


function focus_init() {
	$('.focus').focusout(function() {
    autoFocus = 1;
		setTimeout(() => {
			if(autoFocus == 1) {
				setFocus();
			}
		}, 1000)
	})

	$('.focus').focusin(function() {
		autoFocus = 0;
	});
}


function setFocus() {
  if($('#item-bc').hasClass('hide')) {
    $('#barcode-zone').focus();
  }

  if($('#zone-bc').hasClass('hide')) {
    $('#barcode-item').focus();
  }
}


function zoneInit() {
  let whsCode = $('#warehouse').val();

  $('#barcode-zone').autocomplete({
    source:HOME + 'get_move_zone/'+whsCode,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        $('#barcode-zone').val(arr[0]);
        $('#zone-name').val(arr[1]);
        $('#zone-code').val(arr[0]);
      }
      else {
        $('#barcode-zone').val('');
        $('#zone-name').val('');
        $('#zone-code').val('');
      }
    }
  })
}


function showMoveTable(table) {
  let code = $('#code').val();

  goTo('mobile/move/edit/'+code+'/'+table);
}


function findItem() {
  showMoveTable('items');
}


function showKeyboard() {
  $('#barcode-zone').attr('inputmode', 'text');
  $('#barcode-item').attr('inputmode', 'text');
  $('.icon-qr').addClass('hide');
  $('.icon-keyboard').removeClass('hide');

  zoneInit();
}


function hideKeyboard() {
  $('#barcode-zone').attr('inputmode', 'none');
  $('#barcode-item').attr('inputmode', 'none');
  $('.icon-keyboard').addClass('hide');
  $('.icon-qr').removeClass('hide');
}


$('#barcode-zone').keyup(function(e) {
  if(e.keyCode === 13) {
    let bZone = $(this).val().trim();
    let whsCode = $('#warehouse').val();

    if(bZone.length > 0) {
      getZone(bZone, whsCode);
    }
  }
});


$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val().trim() != "") {
      let tab = $('#tab').val();

      if(tab == 'move_in') {
        moveToZone();
      }

      if(tab == 'move_out') {
        addToTemp();
      }

      if(tab == 'items') {
        getItemZone();
      }
    }
  }
});


$('#btn-increse').click(function() {
  let qty = parseDefault(parseFloat($('#qty').val()), 0);
  qty++;
  $('#qty').val(qty);
  $('#barcode-item').focus();
})


$('#btn-decrese').click(function() {
  let qty = parseDefault(parseFloat($('#qty').val()), 0);
  qty--;
  $('#qty').val(qty);
  $('#barcode-item').focus();
})


function getZone(bZone, whsCode) {
  let code = $('#code').val().trim();

  if(bZone != "" && bZone !== undefined && bZone !== null) {
    load_in();

    $.ajax({
      url:HOME + '/get_zone',
      type:'GET',
      cache:false,
      data:{
        'move_code' : code,
        'warehouse_code' : whsCode,
        'zone_code' : bZone
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status === 'success') {
            $('#barcode-zone').val(ds.zone.code);
            $('#zone-code').val(ds.zone.code);
            $('#zone-name').text(ds.zone.name);
            $('#zone-bc').addClass('hide');
            $('#item-qty').removeClass('hide');
            $('#item-bc').removeClass('hide');
            $('#qty').val(1);
            $('#barcode-item').val('').focus();
            $('#temp-list').addClass('show-qty');
          }
          else {
            beep();
            $('#barcode-zone').val('');
            $('#zone-code').val('');
            $('#zone-name').val('');
            showError(ds.message);
          }
        }
        else {
          beep();
          $('#barcode-zone').val('');
          $('#zone-code').val('');
          $('#zone-name').val('');
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        $('#barcode-zone').val('');
        $('#zone-code').val('');
        $('#zone-name').val('');
        showError(rs);
      }
    })
  }
}


function addToTemp() {
  let move_code = $('#code').val().trim();
  let zone_code = $('#zone-code').val().trim();
  let barcode = $('#barcode-item').val().trim();

  $('#barcode-item').val('').blur();

  if(zone_code.length == 0) {
    beep();
    showError("กรุณาระบุโซน");
    $('#barcode-item').focus();
    return false;
  }

  if(barcode.length == 0) {
    beep();
    showError("กรุณาแสกนสินค้า");
    $('#barcode-item').focus();
    return false;
  }

  let qty = parseDefaultFloat($('#qty').val(), 1);

  if(qty == 0) {
    $('#barcode-item').val('').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_to_temp',
    type:'POST',
    cache:false,
    data:{
      "move_code" : move_code,
      "zone_code" : zone_code,
      "qty" : qty,
      "barcode" : barcode,
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let id = ds.temp.id;

          // if temp row exists :: update qty
          if($('#temp-qty-'+id).length) {
            if(ds.temp.qty <= 0) {
              $('#temp-row-'+id).remove();
            }
            else {
              $('#temp-qty-'+id).val(addCommas(ds.temp.qty));
            }
          }
          else {
            // if not exists :: render new temp row
            let source = $('#temp-row-template').html();
            let data = ds.temp;
            let output = $('#temp-list');

            render_prepend(source, data, output);
          }

          if(ds.temp.qty > 0) {
            $('.temp-row').removeClass('highlight');
            $('#temp-row-'+id).addClass('highlight');
            $('#temp-row-'+id).prependTo('#temp-list');
          }

          reIndex('no');
          recalTemp();
          $('#qty').val(1);
          $('#barcode-item').val('').focus();
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
  })
}


function deleteTemp() {
  let code = $('#code').val();
  let count = $('.temp-chk:checked').length;

  if(count > 0) {
    closeMoreMenu();
    
    swal({
      title:'คุณแน่ใจ ?',
      text:'ต้องการลบ '+count+' รายการที่เลือกหรือไม่ ?',
      type:'warning',
      html:true,
      showCancelButton:true,
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      confirmButtonColor: '#DD6855',
      closeOnConfirm:true
    }, function() {
       setTimeout(() => {
         load_in();

         let ids = [];

         $('.temp-chk:checked').each(function() {
           if($(this).is(':checked')) {
             ids.push($(this).val());
           }
         });

         if(ids.length > 0) {
           $.ajax({
             url:HOME + 'delete_selected_temp',
             type:'POST',
             cache:false,
             data: {
               'code' : code,
               'ids' : ids
             },
             success:function(rs) {
               load_out();

               if(rs.trim() === 'success') {
                 ids.forEach(function(id) {
                   $('#temp-row-'+id).remove();
                 });

                 reIndex();
                 recalTemp();
                 toggleMoreMenu();
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
       },100)
    })
  }
}


function moveToZone() {
  let move_code = $('#code').val().trim();
  let zone_code = $('#zone-code').val().trim();
  let barcode = $('#barcode-item').val().trim();

  $('#barcode-item').val('').blur();

  if(zone_code.length == 0) {
    beep();
    showError("กรุณาระบุโซน");
    $('#barcode-item').focus();
    return false;
  }

  if(barcode.length == 0) {
    beep();
    showError("กรุณาแสกนสินค้า");
    $('#barcode-item').focus();
    return false;
  }

  let qty = parseDefaultFloat($('#qty').val(), 1);

  if(qty == 0) {
    $('#barcode-item').val('').focus();
    return false;
  }

  $('.temp-row').removeClass('highlight');

  load_in();

  $.ajax({
    url: HOME + 'move_to_zone',
    type:"POST",
    cache:false,
    data:{
      "move_code" : move_code,
      "zone_code" : zone_code,
      "qty" : qty,
      "barcode" : barcode
    },
    success: function(rs) {
      load_out();

      if( isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          ds.effected_ids.forEach(function(row) {
            let id = row.id;
            let qty = parseDefaultFloat(row.qty, 0);
            let el = $('#temp-qty-'+id);
            let cqty = parseDefaultFloat(el.val(), 0);

            if(cqty > qty) {
              el.val(cqty - qty);
              $('#temp-row-'+id).addClass('highlight');
              $('#temp-row-'+id).prependTo('temp-list');
            }

            if(cqty <= qty) {
              $('#temp-row-'+id).remove();
            }
          });

          reIndex();
          recalTemp();
          $('#qty').val(1);
          $('#barcode-item').val('').focus();
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


function deleteMoveItems() {
  let code = $('#code').val();
  let count = $('.move-chk:checked').length;

  if(count > 0) {
    closeMoreMenu();

    swal({
      title:'คุณแน่ใจ ?',
      text:'ต้องการลบ '+count+' รายการที่เลือกหรือไม่ ? <br/>รายการที่ลบจะถูกเพิ่มกลับเข้า Temp',
      type:'warning',
      html:true,
      showCancelButton:true,
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      confirmButtonColor: '#DD6855',
      closeOnConfirm:true
    }, function() {
      setTimeout(() => {
        load_in();

        let ids = [];

        $('.move-chk:checked').each(function() {
          if($(this).is(':checked')) {
            ids.push($(this).val());
          }
        });

        if(ids.length > 0) {
          $.ajax({
            url:HOME + 'move_to_temp',
            type:'POST',
            cache:false,
            data: {
              'code' : code,
              'ids' : ids
            },
            success:function(rs) {
              load_out();

              if(rs.trim() === 'success') {
                ids.forEach(function(id) {
                  $('#move-row-'+id).remove();
                });

                reIndex();
                reCal();
                toggleMoreMenu();
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
      },100)
    });
  }
}


function reCal() {
	let total = 0;

	$('.move-qty').each(function() {
		let qty = parseDefaultInt(removeCommas($(this).val()), 0);
    total += qty;
	});

	$('#move-total').val(addCommas(total));
}


function recalTemp() {
  let total = 0;
  $('.temp-qty').each(function() {
    let qty = parseDefaultInt(removeCommas($(this).val()), 0);
    total += qty;
  })

  $('#temp-total').val(addCommas(total));
}


function changeZone() {
  $('#zone-code').val('');
  $('#zone-name').val('');
  $('#barcode-zone').val('');
  $('#item-qty').addClass('hide');
  $('#item-bc').addClass('hide');
  $('#zone-bc').removeClass('hide');
  $('#barcode-zone').focus();
}


function toggleTempChecked(id) {
  if($('#temp-chk-'+id).is(':checked')) {
    $('#temp-chk-'+id).prop('checked', false);
    $('#temp-row-'+id).removeClass('active');
  }
  else {
    $('#temp-chk-'+id).prop('checked', true);
    $('#temp-row-'+id).addClass('active');
  }
}


function toggleMoveChecked(id) {
  if($('#move-chk-'+id).is(':checked')) {
    $('#move-chk-'+id).prop('checked', false);
    $('#move-row-'+id).removeClass('active');
  }
  else {
    $('#move-chk-'+id).prop('checked', true);
    $('#move-row-'+id).addClass('active');
  }
}


function toggleMoreMenu() {
  if($('#more-menu').hasClass('run-in')) {
    $('#more-menu').removeClass('run-in');
  }
  else {
    $('#more-menu').addClass('run-in');
  }
}


function closeMoreMenu() {
  $('#more-menu').removeClass('run-in');
}


function getItemZone() {
  let warehouse_code = $('#warehouse').val();
  let barcode = $('#barcode-item').val().trim();

  $('#barcode-item').val('').blur();

  if(barcode.length == 0) {
    beep();
    showError("กรุณาแสกนสินค้า");
    $('#barcode-item').focus();
    return false;
  }

  if(warehouse_code.length == 0) {
    beep();
    showError('ไม่พบคลังสินค้า');
    $('#barcode-item').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_product_zone',
    type:'POST',
    cache:false,
    data:{
      "warehouse_code" : warehouse_code,
      "barcode" : barcode,
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#txt-item-code').text(ds.sku);
          let source = $('#items-template').html();
          let output = $('#items-list');

          render(source, ds.data, output);
        }
        else {
          $('#txt-item-code').text('ค้นหาสินค้า');
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
  })
}
