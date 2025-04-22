var HOME = BASE_URL + 'masters/sponsor_budget/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id){
  window.location.href = HOME + 'view_detail/'+id;
}

$('#from-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#to-date').datepicker('option', 'minDate', sd);
  }
});

$('#to-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#from-date').datepicker('option', 'maxDate', sd);
  }
})

$('#amount').change(function() {
  let amount = parseDefault(parseFloat(removeCommas($('#amount').val())), 0);
  let used = parseDefault(parseFloat(removeCommas($('#used').val())), 0);
  let balance = amount - used;

  $('#amount').val(addCommas(amount.toFixed(2)));
  $('#balance').val(addCommas(balance.toFixed(2)));
});


function add() {
  clearErrorByClass('e');

	let h = {
    'reference' : $('#reference').val().trim(),
    'amount' : parseDefault(parseFloat(removeCommas($('#amount').val())), 0),
    'from_date' : $('#from-date').val(),
    'to_date' : $('#to-date').val(),
    'budget_year' : $('#budget-year').val(),
    'active' : $('#active').val() == '1' ? 1 : 0,
    'remark' : $('#remark').val().trim()
  };

  if(h.amount <= 0) {
    $('#amount').hasError();
    return false;
  }

  if( ! isDate(h.from_date)) {
    $('#from-date').hasError();
    return false;
  }

  if( ! isDate(h.to_date)) {
    $('#to-date').hasError();
    return false;
  }

  if(h.budget_year == '') {
    $('#budget-year').hasError();
    return false;
  }


	load_in();

	$.ajax({
		url: HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

			if(rs.trim() == 'success') {
        swal({
          title:'Success',
          text:'เพิ่มงบประมาณสำเร็จแล้ว ต้องการเพิ่มใหม่หรือไม่ ?',
          type:'success',
          showCancelButton:true,
          cancelButtonText:'No',
          confirmButtonText:'Yes',
          closeOnConfirm:true
        }, function(isConfirm) {
          if(isConfirm) {
            addNew();
          }
          else {
            goBack();
          }
        })
      }
      else {
        showError(rs);
      }
		},
		error:function(rs) {
			load_out();
			showError(rs);
		}
	})
}


function update() {
  clearErrorByClass('e');

	let h = {
    'id' : $('#id').val(),
    'reference' : $('#reference').val().trim(),
    'amount' : parseDefault(parseFloat(removeCommas($('#amount').val())), 0),
    'used' : parseDefault(parseFloat(removeCommas($('#used').val())), 0),
    'from_date' : $('#from-date').val(),
    'to_date' : $('#to-date').val(),
    'budget_year' : $('#budget-year').val(),
    'active' : $('#active').val() == '1' ? 1 : 0,
    'remark' : $('#remark').val().trim()
  };

  if(h.amount <= 0) {
    $('#amount').hasError();
    return false;
  }

  if(h.amount < h.used) {
    $('#amount').hasError('ไม่สามารถแก้ไขให้น้อยกว่ายอดที่ใช้ไปได้');
    return false;
  }

  if( ! isDate(h.from_date)) {
    $('#from-date').hasError();
    return false;
  }

  if( ! isDate(h.to_date)) {
    $('#to-date').hasError();
    return false;
  }

  if(h.budget_year == '') {
    $('#budget-year').hasError();
    return false;
  }


	load_in();

	$.ajax({
		url: HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

			if(rs.trim() == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        })
      }
      else {
        showError(rs);
      }
		},
		error:function(rs) {
			load_out();
			showError(rs);
		}
	})
}



function clearFilter() {
  $.get(HOME + 'clear_filter', function(rs){
    goBack();
  });
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'คุณแน่ใจว่าต้องการลบ ' + name + '<br/>เมื่อลบแล้วจะไม่สามารถกู้คืนได้</br>ต้องการลบหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){

		$.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				"id" : id
			},
			success:function(rs) {
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						goBack();
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					})
				}
			}
		})
  })
}

$('.filter').change(function(){
  getSearch();
});



function getSearch(){
  $('#searchForm').submit();
}
