window.addEventListener('load', () => {
  let id = localStorage.getItem('transfer_id');
  let isOnline = localStorage.getItem('isOnline');

  if(isOnline == 1) {
    let json = JSON.stringify({"id" : id});
    let requestUri = URI + 'view_detail';
    let header = new Headers();
    header.append('X-API-KEY', API_KEY);
    header.append('Authorization', AUTH);
    header.append('Content-type', 'application/json');

    let requestOptions = {
      method : 'POST',
      headers : header,
      body : json,
      redirect : 'follow'
    };

    load_in();

    fetch(requestUri, requestOptions)
      .then(response => response.text())
      .then(result => {
        load_out();
        if(isJson(result)) {
          let ds = JSON.parse(result);
          let condLabel = ds.cond == 1 ? "สภาพดี" : "ชำรุด";
          $('#status').val(ds.status);
          $('#date-add').val(ds.date_add);
          $('#code').val(ds.code);
          $('#u-serial-code').val(ds.uSerial);
          $('#pea-no').val(ds.peaNo);
          $('#run-no').val(ds.powerNo);
          $('#year-no').val(ds.mYear);
          $('#condition').val(ds.cond);
          $('#cond').val(condLabel);
          $('#use-age').val(ds.usageAge);
          $('#from-doc').val(ds.fromDoc);
          $('#u-preview').html('<img id="u-image" src="'+ds.u_image_data+'" class="width-100" alt="Item image" />');

          if(ds.cond == 2 && ds.damage_name) {
            let label = `<div class="alert alert-info" style="font-size:18px;">${ds.damage_name}</div>`;
            $('#damage-label').html(label);
            $('#damage-label').removeClass('hide');
          }

          suggest();

          $('#i-serial-code').val(ds.iSerial);
          $('#item-code').val(ds.itemCode);
          $('#item-name').val(ds.itemName);
          $('#from-wh').val(ds.fromWhsCode);
          $('#i-preview').html('<img id="i-image" src="'+ds.i_image_data+'" class="width-100" alt="Item image" />');

          if(ds.status == 2) {
            $('#cancel-watermark').removeClass('hide');
          }
        }
      })
      .catch(error => console.error('error', error));
    }
    else {
      localforage.getItem("transfers").then((data) => {
        if(data != null || data != undefined) {
          let item = data.filter((el) => {
          return el.iSerial == id;
        });

        if(item.length == 1) {
          let ds = item[0];
          let condLabel = ds.cond == 1 ? "สภาพดี" : "ชำรุด";
          $('#date-add').val(ds.date_add);
          $('#code').val(ds.code);
          $('#u-serial-code').val(ds.uSerial);
          $('#pea-no').val(ds.peaNo);
          $('#run-no').val(ds.powerNo);
          $('#year-no').val(ds.mYear);
          $('#condition').val(ds.cond);
          $('#cond').val(condLabel);
          $('#use-age').val(ds.usageAge);
          $('#from-doc').val(ds.fromDoc);
          $('#u-preview').html('<img id="u-image" src="'+ds.uImage+'" class="width-100" alt="Item image" />');
          suggest();

          $('#i-serial-code').val(ds.iSerial);
          $('#item-code').val(ds.itemCode);
          $('#item-name').val(ds.itemName);
          $('#from-wh').val(ds.fromWhsCode);
          $('#i-preview').html('<img id="i-image" src="'+ds.iImage+'" class="width-100" alt="Item image" />');
        }
        else {
          swal({
            title:'Error!',
            text:'Item Not Found',
            type:'error'
          });
        }
      }
    });
  }
});



function nextStep() {
  let status = $('#status').val();

  $('.body-step').addClass('hide');
  $('#step-2').removeClass('hide');
  $('#head-step-2').addClass('active');
  $('#head-step-1').removeClass('active');
  $('#btn-next').addClass('hide');

  if(status == 0) {
    $('#btn-cancle').removeClass('hide');
    $('#btn-prev').removeClass('not-show');
    $('#btn-prev2').addClass('hide');
  }
  else {
    $('#btn-prev2').removeClass('hide');
    $('#btn-prev').addClass('not-show');
  }
}



function prevStep() {
  $('.body-step').addClass('hide');
  $('#step-1').removeClass('hide');
  $('#head-step-1').addClass('active');
  $('#head-step-2').removeClass('active');
  $('#btn-prev').addClass('not-show');
  $('#btn-prev2').addClass('hide');
  $('#btn-next').removeClass('hide');
  $('#btn-cancle').addClass('hide');
}


function suggest() {
  let year = parseDefault(parseInt($('#year-no').val()), 0);
  let cond = $('#condition').val();
  let age = $('#use-age').val();
  let label = "";

  if(year == 0 || year == "" || cond == "") {
    $('#suggest-label').html(`<div class="alert alert-normal">กรุณาระบุปีและสภาพมิเตอร์</div>`);
  }
  else {

    let label = `<div class="alert" style="background-color:red; color:white; min-height:100px; font-size:18px;">ใช้งานมาแล้ว ${age} ปี ติดสติ๊กเกอร์สีแดง</div>`;

    if( age < 10 )
    {
      if( cond == 2 && age > 3) {
        label = `<div class="alert" style="background-color:orange; color:white; min-height:100px; font-size:18px;">ใช้งานมาแล้ว ${age} ปี สภาพชำรุด ติดสติ๊กเกอร์สีส้ม</div>`;
      }

      if( cond == 2 && age <= 3) {
        label = `<div class="alert" style="background-color:blue; color:white; min-height:100px; font-size:18px;">ใช้งานมาแล้ว ${age} ปี สภาพชำรุด ติดสติ๊กเกอร์สีน้ำเงิน</div>`;
      }

      if( cond == 1) {
        label = `<div class="alert" style="background-color:green; color:white; min-height:100px; font-size:18px;">ใช้งานมาแล้ว ${age} ปี สภาพดี ติดสติ๊กเกอร์สีเขียว</div>`;
      }
    }

    $('#suggest-label').html(label);
  }
}


function confirmCancle() {
  let id = localStorage.getItem('transfer_id');
  let isOnline = localStorage.getItem('isOnline');
  let code = $('#code').val();
  let docnum = $('#from-doc').val();

  if(id != "" || id != undefined) {
    swal({
      title:'ต้องการยกเลิก <br/>'+code,
      text:'เมื่อยกเลิกสำเร็จแล้วจะไม่สามารถย้อนกลับได้อีก ยืนยันการยกเลิกหรือไม่ ?',
      type:'warning',
      html:true,
      showCancelButton: true,
      confirmButtonColor: '#d15b47',
      confirmButtonText: 'ยืนยัน',
      cancelButtonText: 'ไม่',
      closeOnConfirm: true
    },
    function() {
      if(isOnline == 1) {
        //--- request to server
        load_in();
        let json = JSON.stringify({"id" : id, "user_id" : userId});
        let requestUri = URI + 'cancle_transfer';
        let header = new Headers();
        header.append('X-API-KEY', API_KEY);
        header.append('Authorization', AUTH);
        header.append('Content-type', 'application/json');
        let requestOptions = {
          method : 'POST',
          headers : header,
          body : json
        };

        fetch(requestUri, requestOptions)
        .then(response => response.text())
        .then(result => {
          load_out();
          let rs = JSON.parse(result);
          if(rs.status == 'success') {


            let ds = [];
            localforage.getItem('inventory').then(items => {
              if(items != null && items != undefined) {
                ds = items;
              }

              let serial = $('#i-serial-code').val();
              let docnum = $('#from-doc').val();

              let arr = {
                "docnum" : $('#from-doc').val(),
                "serial" : $('#i-serial-code').val(),
                "code" : $('#item-code').val(),
                "name" : $('#item-name').val(),
                "whCode" : $('#from-wh').val()
              };

              arr[serial] = serial;
              arr[docnum] = docnum;

              ds.push(arr);

              if(ds.length > 0) {
                localforage.setItem('inventory', ds);
              }
            });

            setTimeout(() => {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(() => {
                window.location.reload();
              }, 1200);
            }, 200);
          }
          else {
            setTimeout(() => {
              swal({
                title:'Error!',
                text:rs.message,
                type:'error'
              });
            }, 200);
          }
        })
        .catch(error => {
          console.error('error', error);
        });

      }
      else {
        localforage.getItem("transfers").then((data) => {
          if(data != null || data != undefined) {
            let item = data.filter((el) => {
              return el.iSerial == id;
            });

            if(item.length == 1) {
              let ds = [];
              localforage.getItem('inventory').then((items) => {
                if(items != null && items != undefined) {
                  ds = items;
                }

                // console.log(item);
                item = item[0];

                let serial = item.iSerial;
                let docnum = item.fromDoc;

                let arr = {
                  "docnum" : docnum,
                  "serial" : serial,
                  "code" : item.itemCode,
                  "name" : item.itemName,
                  "whCode" : item.fromWhsCode
                };

                arr[serial] = serial;
                arr[docnum] = docnum;

                ds.push(arr);

                if(ds.length > 0) {
                  localforage.setItem('inventory', ds).then(() => {
                    localforage.getItem('transfers').then((data) => {
                      if(data != null && data != undefined) {
                        let items = data.filter((el) => {
                          return el.iSerial != serial;
                        });

                        if(items.length == 0) {
                          localforage.removeItem('transfers').then(() => {
                            swal({
                              title:'Success',
                              type:'success',
                              timer:1000
                            });

                            setTimeout(() => {
                              goList();
                            }, 1200);
                          });
                        }
                        else {
                          localforage.setItem('transfers', items).then(() => {
                            swal({
                              title:'Success',
                              type:'success',
                              timer:1000
                            });

                            setTimeout(() => {
                              goList();
                            }, 1200);
                          });
                        }
                      }
                    })
                  })
                  .catch((err) => {
                    console.log(err);
                    swal({
                      title:'Error!',
                      text:"Delete failed",
                      type:'error'
                    });
                  });
                }
              });
            }
          }
        })
      }
    });
  }
}
