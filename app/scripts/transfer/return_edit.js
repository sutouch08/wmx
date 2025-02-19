window.addEventListener('load', () => {
  let id = localStorage.getItem('return_id');
  if(navigator.onLine) {
    getDetails(id);
    updateScanType();
  }
});


function getDetails(id) {
  if(navigator.onLine) {
    load_in();

    let json = JSON.stringify({"id" : id});
    let requestUri = URI + 'get_return_detail';
    let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
    let requestOptions = {method : 'POST', headers : header, body : json};

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {
      load_out();

      let ds = JSON.parse(result);
      let source = $('#result-template').html();
      let data = ds.details;
      let output = $('#result');

      render(source, ds, output);

      reIndex();

      $('#btn-save').addClass('not-show');
      $('#btn-unsave').addClass('hide');
      $('#btn-scan').addClass('not-show');
      $('#btn-stop').addClass('hide');

      if(ds.header.status == 0) {
        $('#btn-unsave').removeClass('hide');
        $('#btn-save').addClass('hide');
      }

      if(ds.header.status == -1) {
        $('#btn-save').removeClass('not-show');
      }

      if(ds.header.status < 2) {
        $('#btn-cancle').removeClass('hide');
      }

      if(ds.header.active) {
        $('#btn-scan').removeClass('not-show');
      }
    })
    .catch((error) => {
      console.log('error', error);
    });
  }
}


var scanner;
var config;

function readerInit() {
  let scan_type = $('#scan-type').val();

  let formatToSupport = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.EAN_13,
    Html5QrcodeSupportedFormats.CODE_39,
    Html5QrcodeSupportedFormats.CODE_93,
    Html5QrcodeSupportedFormats.CODE_128,
    Html5QrcodeSupportedFormats.UPD_A,
    Html5QrcodeSupportedFormats.UPC_E,
    Html5QrcodeSupportedFormats.ITF,
    Html5QrcodeSupportedFormats.AZTEC
  ];

  let qrWidth = 250;
  let qrHeight = 250;

  if( scan_type == 'barcode') {
    qrWidth = 350;
    qrHeight = 100;
  }

  
  scanner = new Html5Qrcode("reader", {formatsToSupport: formatToSupport});
  config = {
    fps: 60,
    qrbox: {width: qrWidth, height: qrHeight},
    experimentalFeatures: {
      useBarCodeDetectorIfSupported: true
    }
  };
}



function saveCameraId() {
  let camId = $("input[name='camera_id']:checked").val();

  if(camId === undefined || camId == "") {
    $('#camera-error').text("Please choose camera for use to scan");
    return false;
  }
  else {
    setCookie('cameraId', camId, 3650);
    closeModal('cameras-modal');
    setTimeout(() => {
      startScan();
    }, 200);
  }
}


function changeCameraId() {
  Html5Qrcode.getCameras().then(devices => {
    if(devices && devices.length) {
      $('#select-side').val('');
      let source = $('#cameras-list-template').html();
      let output = $('#cameras-list');

      render(source, devices, output);
      showModal('cameras-modal');
    }
  });
}


function updateScanType() {
  let json = JSON.stringify({"config_code" : "SCANTYPE"});
  let requestUri = URI + 'getConfig';
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

  fetch(requestUri, requestOptions)
  .then(response => response.text())
  .then(result => {
    let ds = JSON.parse(result);
    $('#scan-type').val(ds);
  })
  .then(() => {
    readerInit();
  })
}


function stopScan() {
	scanner.stop().then((ignore) => {
		$('#cam').addClass('hide');
		$('#btn-stop').addClass('hide');
		$('#btn-scan').removeClass('hide');
	});
}


function startScan() {
  let camId = getCookie('cameraId');

  if(camId == "" || camId == undefined) {
    Html5Qrcode.getCameras().then(devices => {
  		if(devices && devices.length) {
        let source = $('#cameras-list-template').html();
        let output = $('#cameras-list');
        render(source, devices, output);
        showModal('cameras-modal');
  		}
  	})
    .catch((error) => {
      console.log('error', error);
      swal({
        title:'Oops!',
        text: error,
        type:'error'
      });
    });
  }
  else {
    if(navigator.onLine) {
      $('#cam').removeClass('hide');
      $('#btn-scan').addClass('hide');
      $('#btn-stop').removeClass('hide');

      scanner.start({deviceId: {exact: camId}}, config, (decodedText, decodedResult) => {
        stopScan();

        localforage.getItem('inventory').then((data) => {
          if(data != null && data != undefined) {
            items = data.filter((row) => {
              return row.hasOwnProperty(decodedText);
            });
          }

          if(items.length == 0) {
            swal({
              title:'ข้อผิดพลาด',
              text:`ไม่พบ ${decodedText} ในคลัง`,
              type:'error'
            });
            return false;
          }
          else {
            let item = items[0];
            let return_id = localStorage.getItem('return_id');
            let return_code = localStorage.getItem('return_code');
            let body = JSON.stringify({
              "return_id" : return_id,
              "return_code" : return_code,
              "ItemCode" : item.code,
              "ItemName" : item.name,
              "serial" : item.serial,
              "WhsCode" : item.whCode,
              "docNum" : item.docnum
            });

            let requestUri = URI + 'add_return_row';
            let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
            let requestOptions = {method : 'POST', headers : header, body : body};

            fetch(requestUri, requestOptions)
            .then(response => response.text())
            .then(result => {
              let rs = JSON.parse(result);

              if(rs.status == 'success') {
                let source = $('#details-template').html();
                let data = rs.data;
                let output = $('#details-table');
                render(source, data, output);
                reIndex();
                deleteItemStockBySerial(item.serial);
              }
              else {
                swal({
                  title:'Error!',
                  text:rs.message,
                  type:'error'
                });
              }
            });
          }
        });
      });
    }
    else {
      swal({
        title:'ข้อผิดพลาด!',
        text:'ไม่สามารถทำรายการได้ในขณะออฟไลน์',
        type:'warning'
      });
    }
  }
}


function submitSerial() {
  let serial = $.trim($('#input-serial').val());
  if(serial.length > 0) {
    if(navigator.onLine) {
      localforage.getItem('inventory').then((data) => {
        let items = [];

        if(data != null && data != undefined) {
          items = data.filter((row) => {
            return row.hasOwnProperty(serial);
          });
        }

        if(items.length == 0) {
          swal({
            title:'ข้อผิดพลาด',
            text:`ไม่พบ ${serial} ในคลัง`,
            type:'error'
          });
          return false;
        }
        else {
          let item = items[0];
          let return_id = localStorage.getItem('return_id');
          let return_code = localStorage.getItem('return_code');
          let body = JSON.stringify({
            "return_id" : return_id,
            "return_code" : return_code,
            "ItemCode" : item.code,
            "ItemName" : item.name,
            "serial" : item.serial,
            "WhsCode" : item.whCode,
            "docNum" : item.docnum
          });

          let requestUri = URI + 'add_return_row';
          let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
          let requestOptions = {method : 'POST', headers : header, body : body};

          fetch(requestUri, requestOptions)
          .then(response => response.text())
          .then(result => {
            let rs = JSON.parse(result);

            if(rs.status == 'success') {
              let source = $('#details-template').html();
              let data = rs.data;
              let output = $('#details-table');

              render(source, data, output);
              reIndex();
              deleteItemStockBySerial(item.serial);
              $('#input-serial').val('').focus();
            }
            else {
              swal({
                title:'Error!',
                text:rs.message,
                type:'error'
              });
            }
          });
        }
      });
    }
    else {
      swal({
        title:'ข้อผิดพลาด!',
        text:'ไม่สามารถทำรายการได้ในขณะออฟไลน์',
        type:'warning'
      });
    }
  }
}


function deleteItemStockBySerial(serial) {
  localforage.getItem('inventory').then((data) => {
    if(data != null && data != undefined) {
      let items = data.filter((el) => {
        return el.serial != serial;
      });

      if(items.length == 0) {
        localforage.removeItem('inventory').then(() => {
          return true;
        });
      }
      else {
        localforage.setItem('inventory', items).then(() => {
          return true;
        });
      }
    }
  })
}


function renderList(data) {
  let source = $('#details-template').html();
  let output = $('#details-table');
  render(source, data, output);
  reIndex();
}


function removeRow(id, serial) {
  swal({
    title:"Warning",
    text:`ต้องการลบ ${serial} หรือไม่ ?`,
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#d15b47',
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ไม่',
    closeOnConfirm: true
  }, function() {
    if(navigator.onLine) {
      let json = JSON.stringify({'id' : id});
      let requestUri = URI + 'remove_return_row';
      let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
      let requestOptions = {method : 'POST', headers : header, body : json};
      load_in();
      fetch(requestUri, requestOptions)
      .then(response => response.text())
      .then(result => {
        load_out();
        let ds = JSON.parse(result);
        if(ds.status == 'success') {
          $('#row-'+id).remove();
          reIndex();
          syncItem();
          setTimeout(function(){
            console.log('swal');
            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            });
          }, 200);
        }
        else {
          setTimeout(() => {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });
          }, 200);
        }
      })
      .catch((error) => {
        console.log('error', error)
      });
    }
    else {
      swal({
        title:'ข้อผิดพลาด',
        text:'ไม่สามารถทำรายการได้ในขณะออฟไลน์',
        type:'error'
      });
    }
  });
}


function save() {
  let id = localStorage.getItem('return_id');
  let code = localStorage.getItem('return_code');
  let remark = $('#remark').val();

  let json = JSON.stringify({"code" : code, "id" : id, "remark" : remark});
  let requestUri = URI + 'save_return';
  let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
  let requestOptions = {method : 'POST', headers : header, body : json};

  load_in();

  fetch(requestUri, requestOptions)
  .then(response => response.text())
  .then(result => {
    load_out();

    let ds = JSON.parse(result);
    if(ds.status == 'success') {
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
      swal({
        title:'Error!',
        text:ds.message,
        type:'error'
      });
    }
  })
  .catch((error) => {
    console.log('error', error);
  });
}



function unsave() {
  swal({
    title:'Unsave',
    text:`<center>สถานะเอกสารจะกลับไปเป็น "ดราฟ" <br/>ต้องการยกเลิกการบันทึกหรือไม่ ?</center>`,
    type:'info',
    html:true,
    showCancelButton:true,
    confirmButtonColor:'#ffb752',
    confirmButtonText:'ใช่',
    cancelButtonText:'ไม่',
    closeOnConfirm:true
  },
  function() {
    load_in();

    let id = localStorage.getItem('return_id');
    let code = localStorage.getItem('return_code');

    let json = JSON.stringify({"code" : code, "id" : id});
    let requestUri = URI + 'unsave_return';
    let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
    let requestOptions = {method : 'POST', headers : header, body : json};

    load_in();

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {
      load_out();

      let ds = JSON.parse(result);
      if(ds.status == 'success') {
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
        setTimeout(() => {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          }, () => {
            window.location.reload();
          });
        }, 200);
      }
    })
    .catch((error) => {
      console.log('error', error);
    });
  });
}



function cancle() {
  swal({
    title:'คุณแน่ใจ ?',
    text:`<center>เมื่อยกเลิกแล้วจะไม่สามารถย้อนกลับได้อีก <br/>ยืนยันการยกเลิกหรือไม่ ?</center>`,
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonColor:'#d15b47',
    confirmButtonText:'ยืนยัน',
    cancelButtonText:'ไม่',
    closeOnConfirm:true
  },
  function() {
    load_in();

    let id = localStorage.getItem('return_id');
    let code = localStorage.getItem('return_code');

    let json = JSON.stringify({"code" : code, "id" : id});
    let requestUri = URI + 'cancle_return';
    let header = new Headers({"X-API-KEY" : API_KEY, "Authorization" : AUTH, "Content-Type" : "application/json"});
    let requestOptions = {method : 'POST', headers : header, body : json};

    load_in();

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {

      let ds = JSON.parse(result);

      if(ds.status == 'success') {

        if(syncItem()) {
          load_out();

          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
      }
      else {
        setTimeout(() => {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          }, () => {
            window.location.reload();
          });
        }, 200);
      }
    })
    .catch((error) => {
      console.log('error', error);
    });
  });
}
