var HOME = BASE_URL + 'masters/sponsors/';

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


function toggleBudget() {
  let id = $('#budget').val();

  if(id != "") {
    let code = $('#budget option:selected').data('code');
    let reference = $('#budget option:selected').data('reference');
    let amount = $('#budget option:selected').data('amount');
    let used = $('#budget option:selected').data('used');
    let balance = $('#budget option:selected').data('balance');
    let from = $('#budget option:selected').data('from');
    let to = $('#budget option:selected').data('to');
    let year = $('#budget option:selected').data('year');
    let active = $('#budget option:selected').data('active');

    $('#budget-code').val(code);
    $('#budget-reference').val(reference);
    $('#budget-amount').val(addCommas(amount));
    $('#budget-used').val(addCommas(used));
    $('#budget-balance').val(addCommas(balance));
    $('#from-date').val(from);
    $('#to-date').val(to);
    $('#budget-year').val(year);
    $('#budget-active').val(active);
  }
  else {
    $('#budget-code').val("");
    $('#budget-reference').val("");
    $('#budget-amount').val("");
    $('#budget-used').val("");
    $('#budget-balance').val("");
    $('#from-date').val("");
    $('#to-date').val("");
    $('#budget-year').val("");
    $('#budget-active').val("");
  }
}


$('#customer-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val().trim();
    let ar = rs.split(' | ');
    if(ar.length == 2) {
      $(this).val(ar[0]);
      $('#customer-name').val(ar[1]);
    }
    else {
      $(this).val('');
      $('#customer-name').val('');
    }
  }
});


var click = 0;

function add() {
  if(click === 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'customer_code' : $('#customer-code').val().trim(),
      'active' : $('#active').val(),
      'budget_id' : $('#budget').val()
    };

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      click = 0;
      return false;
    }

    if(h.budget_id == "") {
      $('#budget').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มรายการเรียบร้อยแล้ว<br/>ต้องการเพิ่มอีกหรือไม่ ?',
            type:'success',
            html:true,
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
          });
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        click = 0;
        load_out();
        showError(rs);
      }
    })
  }
}


function update() {
  if(click === 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'id' : $('#id').val(),
      'customer_code' : $('#customer-code').val().trim(),
      'active' : $('#active').val(),
      'budget_id' : $('#budget').val()
    };

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      click = 0;
      return false;
    }

    if(h.budget_id == "") {
      $('#budget').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
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
            window.location.reload();
          }, 1200);
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        click = 0;
        load_out();
        showError(rs);
      }
    })
  }
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function(rs){
    goBack();
  });
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {
    load_in();
    setTimeout(() => {
      $.ajax({
        url:HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          "id" : id
        },
        success:function(rs) {
          load_out();

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
            showError(rs);
          }
        },
        error:function(rs) {
          load_out();
          showError(rs);
        }
      })
    }, 100);
  })
}



$('.filter').change(function(){
  getSearch();
});



function getSearch(){
  $('#searchForm').submit();
}


function get_template() {
	var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'download_template/'+token;
}


$('#credit_term').focus(function(){
	$(this).select();
})

$('#CreditLine').focus(function(){
	$(this).select();
})

$('#name').focus(function(){
	$(this).select();
});

$('#Tax_id').focus(function() {
	$(this).select();
})


$('#attributeModal').on('shown.bs.modal', function(){
	$('#a_code').focus();
});

function saveAttribute() {
	var attribute = $('#attribute').val();
	var code = $('#a_code').val();
	var name = $('#a_name').val();

	if(code.length === 0) {
		$('#a_code').addClass('has-error');
		return false;
	}
	else {
		$('#a_code').removeClass('has-error');
	}

	if(name.length === 0) {
		$('#a_name').addClass('has-error');
		return false;
	}
	else {
		$('#a_name').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'add_attribute',
		type:'POST',
		cache:false,
		data:{
			'attribute' : attribute,
			'code' : code,
			'name' : name
		},
		success:function(rs) {
			load_out();
			$('#attributeModal').modal('hide');
			var rs = $.trim(rs);
			if(rs === 'success') {
				var option = '<option value="'+code+'">'+name+'</option>';
				$('#'+attribute).append(option);
				$('#'+attribute).val(code);

				//--- reset input
				$('#attribute').val('');
				$('#a_code').val('');
				$('#a_name').val('');
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, satus, error) {
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	})
}

var attr = {
	"group": "เพิ่มกลุ่มลูกค้า",
	"kind" : "เพิ่มประเภทลูกค้า",
	"type" : "เพิ่มชนิดลูกค้า",
	"class" : "เพิ่มเกรดลูกค้า",
	"area" : "เพิ่มเขตการขาย"
}


function addAttribute(attribute){
	$('#title').text(attr[attribute]);
	$('#attribute').val(attribute);
	$('#a_code').val('');
	$('#a_name').val('');

	$('#attributeModal').modal('show');
}
