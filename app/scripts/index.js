var scanner;
var config;
var work_list_qty = 0;
var meter_list_qty = 0;
var inform_qty = 0;


function readerInit() {

  let formatToSupport = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.EAN_13,
    Html5QrcodeSupportedFormats.CODE_39,
    Html5QrcodeSupportedFormats.CODE_93,
    Html5QrcodeSupportedFormats.CODE_128
  ];

  let qrWidth = 200;
  let qrHeight = 200;

  if( scanType == 'barcode') {
    formatToSupport = [
      Html5QrcodeSupportedFormats.EAN_13,
      Html5QrcodeSupportedFormats.CODE_39,
      Html5QrcodeSupportedFormats.CODE_93,
      Html5QrcodeSupportedFormats.CODE_128
    ];

    qrWidth = 300;
    qrHeight = 100;
  }

  if( scanType == 'qrcode') {
    formatToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
  }

  scanner = new Html5Qrcode("reader", {formatsToSupport: formatToSupport});
  config = {
    fps: 60,
    qrbox: {width: qrWidth, height: qrHeight, center: (scanType === 'barcode')},
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
    localStorage.setItem('cameraId', camId);
    closeModal('cameras-modal');
  }
}

function changeCameraId() {
  Html5Qrcode.getCameras().then(devices => {
    if(devices && devices.length) {
      let source = $('#cameras-list-template').html();
      let output = $('#cameras-list');
      let camId = localStorage.getItem('cameraId');
      render(source, devices, output);
      $('#'+camId).prop('checked', true);
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

async function updateScanType() {
  if(navigator.onLine) {
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
      localStorage.setItem('scanType', ds);
    })
    .then(() => {
      readerInit();
    })
  }
  else {
    let ds = localStorage.getItem('scanType');

    if(ds == "qrcode" || ds == "barcode" || ds == "both")
    {
      $('#scan-type').val(ds);
    }

    readerInit();
  }
}

function startScan(actionCallback) {
  let camId = localStorage.getItem('cameraId');

  if(camId == "" || camId == undefined) {
    changeCameraId();
  }
  else {

    $('#cam').removeClass('hide');
    $('#reader-backdrop').removeClass('hide');
    $('.sc').addClass('hide');

    scanner.start({deviceId: {exact: camId}}, config, (decodedText, decodedResult) => {
      stopScan();
      $('#scan-result').val(decodedText);

      if(actionCallback != null && actionCallback != undefined) {
        actionCallback();
      }
      else {
        console.log(actionCallback);
      }
    });

  }
}

function stopScan() {
	scanner.stop().then((ignore) => {
		$('#cam').addClass('hide');
    $('#reader-backdrop').addClass('hide');
    $('.sc').removeClass('hide');
	});
}

async function syncAll() {
  const i = await Promise.all([syncItem(), syncWorkList(), syncDamageList()]);
  return i;
}

async function init() {
  load_in();
  //await syncAll();
  //await updateMenu();
  $('#work-qty').html(work_list_qty);
  $('#meter-list-qty').html(meter_list_qty);
  load_out();
}



function syncDamageList() {
  return new Promise((resolve, reject) => {
    if(navigator.onLine) {
      $('#loader-message').text('กำลังซิงค์ตัวเลือกสภาพมิเตอร์');
      let json = JSON.stringify({"user" : 0});
      let requestUri = URI + 'get_damaged_list';
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
      .then((result) => {
        let ds = JSON.parse(result);
        if(ds.status == 'success') {
          localforage.setItem('damageList', ds.data);
        }
      });

      resolve(console.log('sync completed'));
    }
    else {
      resolve('ofline');
    }
  })
}


function renderDamageList() {
  return new Promise((resolve, reject) => {
    localforage.getItem('damageList').then((data) => {
      if(data != null && data != undefined) {
        let source = $('#damage-list-template').html();
        let output = $('#u-dispose-id');

        render(source, data, output);
      }

      resolve('done');
    });
  });
}

function syncItem(actionCallback) {
  return new Promise((resolve, reject) => {
    if(navigator.onLine) {
      $('#loader-message').text('กำลังซิงค์ข้อมูลมิเตอร์..');
      let ud = JSON.parse(localStorage.getItem('userdata'));
      let json = JSON.stringify({'team_group_id' : ud.team_group_id});
      let requestUri = URI + 'sync_team_group_items';
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

        if(ds.data != null || ds.data != "") {
          let data = [];
          var promises = [];

          ds.data.forEach((item, i) => {
            promises.push(
              localforage.getItem('inventory')
              .then((res) => {
                var row = [];
                if(res !== null && res !== undefined) {
                  row = res.filter((obj) => {
                    //console.log(obj.peaNo, item.PeaNo);
                    return obj.peaNo == item.PeaNo && obj.status == "I";
                  });
                }

                if(row.length) {
                  var arr = {
                    "docnum" : item.DocNum,
                    "peaNo" : item.PeaNo,
                    "serial" : item.Serial,
                    "code" : item.ItemCode,
                    "name" : item.ItemName,
                    "whCode" : item.WhsCode,
                    "binCode" : item.BinCode,
                    "date" : item.date,
                    "state" : item.status,
                    "state_label" : item.status_label,
                    "state_color" : item.status_color,
                    "status" : 'I'
                  };
                }
                else {
                  var arr = {
                    "docnum" : item.DocNum,
                    "peaNo" : item.PeaNo,
                    "serial" : item.Serial,
                    "code" : item.ItemCode,
                    "name" : item.ItemName,
                    "whCode" : item.WhsCode,
                    "binCode" : item.BinCode,
                    "date" : item.date,
                    "state" : item.status,
                    "state_label" : item.status_label,
                    "state_color" : item.status_color,
                    "status" : 'P'
                  };

                  meter_list_qty++;
                }

                data.push(arr);
              })
            ); //-- push
          });


          Promise.all(promises)
          .then(() => {
            if(data.length == 0) {
              localforage.removeItem('inventory')
              .then(() => {
                if(actionCallback != null && actionCallback != undefined) {
                  actionCallback();
                }
              });
            }
            else {
              localforage.setItem('inventory', data)
              .then(() => {
                if(actionCallback != null && actionCallback != undefined) {
                  actionCallback();
                }
              });
            }
            resolve(console.log('sync completed'));
          });
        }
      });
    }
    else {
      localforage.getItem('inventory')
      .then((data) => {
        if(data !== null && data !== undefined && data.length) {
          var ds = data.filter((obj) => {
            return obj.status === "P";
          });

          meter_list_qty = ds.length;
        }

        resolve('offline');
      })
    }
  })
}

function syncWorkList() {
  return new Promise((resolve, reject) => {
    if(navigator.onLine) {
      let ud = JSON.parse(localStorage.getItem('userdata'));

      let json = JSON.stringify({'team_group_id' : ud.team_group_id});
      let requestUri = URI + 'sync_team_group_work_list';
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
        if(ds.data != null || ds.data != "") {
          var promises = [];
          let data = [];

          ds.data.forEach((item, i) => {
            promises.push(
              localforage.getItem('work_list')
              .then((res) => {
                let row = [];
                if(res !== null && res !== undefined) {
                  row = res.filter((obj) => {
                    return obj.pea_no == item.pea_no && obj.status == "I";
                  });
                }

                if(row.length) {
                  var arr = {
                    "id" : item.id,
                    "pea_no" : item.pea_no,
                    'cust_route' : item.cust_route,
                    "cust_no" : item.cust_no,
                    "ca_no" : item.ca_no,
                    "cust_name" : item.cust_name,
                    "cust_address" : item.cust_address,
                    "cust_tel" : item.cust_tel,
                    "age_meter" : item.age_meter,
                    "latitude" : item.latitude,
                    "longitude" : item.longitude,
                    "state" : item.status,
                    "state_label" : item.status_label,
                    "state_color" : item.status_color,
                    "status" : "I"
                  };
                }
                else {
                  var arr = {
                    "id" : item.id,
                    "pea_no" : item.pea_no,
                    'cust_route' : item.cust_route,
                    "cust_no" : item.cust_no,
                    "ca_no" : item.ca_no,
                    "cust_name" : item.cust_name,
                    "cust_address" : item.cust_address,
                    "cust_tel" : item.cust_tel,
                    "age_meter" : item.age_meter,
                    "latitude" : item.latitude,
                    "longitude" : item.longitude,
                    "state" : item.status,
                    "state_label" : item.status_label,
                    "state_color" : item.status_color,
                    "status" : "P"
                  };

                  work_list_qty++;
                }

                data.push(arr);
              }) //-- then
            ); //-- promises.push

          }); //-- foreach

          Promise.all(promises)
          .then(() => {

            if(data.length == 0) {
              localforage.removeItem('work_list');
            }
            else {
              localforage.setItem('work_list', data);
            }

            resolve(console.log('sync completed'));
          });
        }
      });
    }
    else {
      localforage.getItem('work_list')
      .then((data) => {
        if(data !== null && data !== undefined && data.length) {
          var ds = data.filter((obj) => {
            return obj.status === "P";
          })
        }

        work_list_qty = ds.length;

        resolve("offline");
      })
    }
  });
}

function updateMenu () {
  return new Promise((resolve, reject) => {
    $('#check-in').addClass('hide');
    $('#meter-list').addClass('hide');

    if(canGetMeter == 1) {
      $('#check-in').removeClass('hide');
    }
    else {
      $('#meter-list').removeClass('hide');
    }

    $('#first-menu').removeClass('hide');

    resolve(true);
  });
}


function suggest(id) {

  let cond = (id === undefined) ? $('#u-dispose-id').val() : id;
  let age = parseDefault(parseInt($('#use-age').val()), 0);
  let color = "red";
  let text = `ใช้งานมาแล้ว ${age} ปี ติดสติ๊กเกอร์สีแดง`;

  if( age <= 10 )
  {
    if( cond != '0' && age > 3) {
      color = "orange";
      text =  `ใช้งานมาแล้ว ${age} ปี สภาพชำรุด ติดสติ๊กเกอร์สีส้ม`;
    }

    if( cond != '0' && age <= 3) {
      color = "blue";
      text =  `ใช้งานมาแล้ว ${age} ปี สภาพชำรุด ติดสติ๊กเกอร์สีน้ำเงิน`;
    }

    if( cond == '0') {
      color = "green";
      text =  `ใช้งานมาแล้ว ${age} ปี สภาพดี ติดสติ๊กเกอร์สีเขียว`;
    }
  }

  let label = `<div class="alert" style="background-color:${color}; color:white; min-height:60px; font-size:18px;">${text}</div>`;

  $('#suggest-label').html(label);
}

function scrollToTop() {
  window.scrollTo({top:0, behavior:'smooth'});
}

function scrollToButtom() {
  window.scrollTo({top: document.body.scrollHeight, behavior: 'smooth'});
}

function reload() {
  window.location.reload();
}


function resetMeter() {
  localforage.getItem('inventory')
  .then((data) => {
    if(data !== null && data !== undefined && data.length) {
      data.forEach((item, i) => {
        data[i].status = "P";
      })

      localforage.setItem('inventory', data);
    }
  })
}


function resetWorkList() {
  localforage.getItem('work_list')
  .then((data) => {
    if(data !== null && data !== undefined && data.length) {
      data.forEach((item, i) => {
        data[i].status = "P";
      })

      localforage.setItem('work_list', data);
    }
  })
}
