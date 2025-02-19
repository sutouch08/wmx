window.addEventListener('load', () => {
  $('#date-add').datepicker({
    dateFormat:'dd-mm-yy'
  });

  $('#date-add').val(getCurrentDate());

  $('#remark').autosize({append:"\n"});
});


function goList() {
  window.location.href = "return.html";
}


function add() {
  let date_add = $('#date-add').val();
  let remark = $('#remark').val();

  if(navigator.onLine) {
    let json = JSON.stringify({"date_add" : date_add, "remark" : remark, 'whsCode' : fWhCode, 'user_id' : userId});
    let requestUri = URI + 'new_return';
    let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" :"application/json" });
    let requestOptions = {
      method:'POST',
      headers:header,
      body:json
    };

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {
      let rs = JSON.parse(result);

      if(rs.status == 'success') {
        localStorage.setItem('return_id', rs.id);
        window.location.href = "return_edit.html";
      }
      else {
        swal({
          title:'Error!',
          text:rs.message,
          type:'error'
        });
      }
    })
    .catch(error => console.error('error', error));

  }
  else {
    swal({
      title:'Oops!',
      text:'Internet ขัดข้องไม่สามารถทำรายการได้',
      type:'warning'
    });
  }
}
