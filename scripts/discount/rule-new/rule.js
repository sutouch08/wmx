
function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new/';
}


function goEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id){
	//--- properties for print
	var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";
	var center    = ($(document).width() - 800)/2;

	var target  = HOME + 'view_rule_detail/'+id;
	window.open(target, '_blank', prop);
}


function getDelete(id, name){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+name+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:BASE_URL + "discount/discount_rule/delete_rule",
				type:"POST",
        cache:"false",
        data:{
          "rule_id" : id
        },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						$("#row-"+id).remove();
            reIndex();
					}else{
						swal("Error !", rs, "error");
					}
				}
			});
	});
}
