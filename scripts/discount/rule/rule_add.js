function changeURL(id, tab)
{
	var url = HOME + 'edit/' + id + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'discount_rule', url);
}

function saveAdd() {
  let name = $('#name').val();

	if(name.length == 0) {
		$('#name').addClass('has-error');
		$('#name').focus();
		return false;
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"name" : name
		},
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs)

				if(ds.status == 'success') {
					goEdit(ds.id);
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
			beep();
			showError(rs);
		}
	});
}


function activeRule(){
  $('#isActive').val(1);
  $('#btn-active-rule').addClass('btn-success');
  $('#btn-dis-rule').removeClass('btn-danger');
}


function disActiveRule(){
  $('#isActive').val(0);
  $('#btn-active-rule').removeClass('btn-success');
  $('#btn-dis-rule').addClass('btn-danger');
}

function updateRule() {
  var id = $('#id_rule').val();
  var isActive = $('#isActive').val();
  var name = $('#txt-rule-name').val();

	if(isNaN(parseInt(id))){
    swal('ไม่พบ ID Rule');
    return false;
  }

  if(name.length < 4){
    swal('ข้อผิดพลาด!', 'ชื่อเงื่อนไขต้องมากกว่า 4 ตัวอักษร', 'error');
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'update_rule/'+id,
    type:'POST',
    cache:'false',
    data:{
      'name' : name,
      'active' : isActive
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
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
