function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function edit(code){
  window.location.href = HOME + 'edit/' + code;
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});

function approve()
{
  var order_code = $('#order_code').val();

	load_in();

  $.ajax({
    url:BASE_URL + 'orders/orders/do_approve/'+order_code,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        change_state();
      }
			else{
				load_out();
        swal({
          title:'Error!',
          text:rs,
          type:'error',
					html:true
        });
      }
    },
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
  });
}


function unapprove()
{
  var order_code = $('#order_code').val();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_approve/'+order_code,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ยกเลิกการอนุมัติแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
