function addNew() {
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});
