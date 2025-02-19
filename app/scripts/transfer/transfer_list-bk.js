
function viewDetail(id) {
  localStorage.setItem('transfer_id', id);
  localStorage.setItem('isOnline', 1);
  setTimeout(() => {
    window.location.href = "transfer_detail.html";
  }, 200);
}


function viewOfflineDetail(id) {
  localStorage.setItem('transfer_id', id);
  localStorage.setItem('isOnline', 0);

  setTimeout(() => {
    window.location.href = "transfer_detail.html";
  }, 200);
}


window.addEventListener('load', () => {
  let code = getCookie('trCode');
  let serial = getCookie('trSerial');
  let from = getCookie('trFrom');
  let to = getCookie('trTo');
  let status = getCookie('trStatus');
  let perpage = $('#perpage').val();
  let offset = $('#offset').val();

  $('#code').val(code);
  $('#serial').val(serial);
  $('#fromDate').val(from);
  $('#toDate').val(to);
  $('#status').val((status == "" ? "all" : status));

  localforage.getItem('inventory').then((data) => {
    let ds = [];
    if(data != null || data != undefined) {
      $('#n-install').addClass('hide');
      $('#i-install').removeClass('hide');
    }
  });

   setTimeout(() => {
     loadPage();
   }, 200);
});

function noDataAlert() {
  swal({
    title:'',
    text:'<center>ไม่พบข้อมูลมิเตอร์ในเครื่อง<br/>กรุณา Check in ก่อนติดตั้งมิเตอร์</center>',
    type:'info',
    html:true
  });
}

function clearFilterList() {
  setCookie('trCode', '');
  setCookie('trSerial', '');
  setCookie('trFrom', '');
  setCookie('trTo', '');
  setCookie('trStatus', 'all');
  // setCookie('trPerpage', 20);
  // setCookie('trOffset', 0);

  window.location.reload();
}

async function loadPage() {
  await updateOfflineList();
  await getFilterList();
}


async function getFilterList() {
  let code = $('#code').val();
  let serial = $('#serial').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let status = $('#status').val();
  let perpage = $('#perpage').val();
  let offset = $('#offset').val();

  setCookie('trCode', code);
  setCookie('trSerial', serial);
  setCookie('trFrom', fromDate);
  setCookie('trTo', toDate);
  setCookie('trStatus', status);

  if(navigator.onLine) {
    load_in();
    let requestUri = URI + 'get_transfer_list';
    let header = new Headers();
    header.append('X-API-KEY', API_KEY);
    header.append('Authorization', AUTH);
    header.append('Content-type', 'application/json');

    let json = JSON.stringify({
      "code" : code,
      "serial" : serial,
      "fromDate" : fromDate,
      "toDate" : toDate,
      "status" : status,
      "perpage" : perpage,
      "offset" : offset
    });

    let requestOptions = {
      method : 'POST',
      headers : header,
      body : json,
      redirect : 'follow'
    };

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {
      load_out();
      if(isJson(result)) {
        let ds = JSON.parse(result);
        $('#code').val(ds.code);
        $('#serial').val(ds.serial);
        $('#fromDate').val(ds.from_date);
        $('#toDate').val(ds.to_date);
        $('#status').val(ds.status);
        $('#num_rows').text(addCommas(ds.rows));
        $('#limit').val(ds.rows);

        if(ds.data.length) {
          let source = $('#online-template').html();
          let output = $('#online-job');
          render_append(source, ds.data, output);
          offset = offset == 0 ? 1 * perpage : offset * perpage;
          $('#offset').val(offset);
          let rows = $('#show_rows').text();
          rows = removeCommas(rows);
          rows = parseDefault(parseInt(rows), 0);
          rows = rows + ds.data.length;
          $('#show_rows').text(addCommas(rows));
          $('#show').val(rows);
        }
        else {
          console.log('nodata');
          noData();
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    })
    .catch(error => console.log('error', error));
  }
  else {
    load_out();
    noData();
  }
}


function noData() {
  $('#no-list').css('display', 'block');
  $('#no-list-label').animate({opacity:0.9},500);

  setTimeout(() => {
    $('#no-list-label').animate({opacity:0}, 500);

    setTimeout(() => {
      $('#no-list').css('display', 'none');
    }, 500);
  }, 1000);
}

async function updateOfflineList() {
  return new Promise((resolve, reject) => {
    let source = $('#offline-template').html();
    let output = $('#offline-job');

    localforage.getItem('transfers').then((data) => {
      if(data != null && data != undefined) {
        //--- send data to server if online
        if(navigator.onLine) {
          data.forEach((ds, index, array) => {
            let json = JSON.stringify({
              "itemCode" : ds.itemCode,
              "itemName" : ds.itemName,
              "fromWhsCode" : ds.fromWhsCode,
              "toWhsCode" : ds.toWhsCode,
              "remark" : ds.remark,
              "uSerial" : ds.uSerial,
              "iSerial" : ds.iSerial,
              "peaNo" : ds.peaNo,
              "runNo" : ds.runNo,
              "mYear" : ds.mYear,
              "cond" : ds.cond,
              "damage_id" : ds.damage_id,
              "usageAge" : ds.usageAge,
              "uImage" : ds.uImage,
              "iImage" : ds.iImage,
              "uOrientation" : ds.uOrientation,
              "iOrientation" : ds.iOrientation,
              "fromDoc" : ds.fromDoc
            });

            let requestUri = URI + 'add_transfer';
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
              if(isJson(result)) {
                let rs = JSON.parse(result);
                if(rs.status == 'success') {
                  deleteOfflineTransfer(ds.iSerial);
                }
              }
              else {
                swal({
                  title:'Error!',
                  text:rs,
                  type:'error'
                });
              }
            })
            .catch(error => console.log('error', error));

            if(index === array.length -1) {
              resolve();
            }
          });
        }
        else {
          render(source, data, output);
          resolve();
        }
      }
      else {
        data = [];
        render(source, data, output);
        resolve();
      }
    });

  });
}


function deleteOfflineTransfer(serial) {
  localforage.getItem('transfers').then((data) => {
    if(data != null && data != undefined) {
      let items = data.filter((el) => {
        return el.iSerial != serial;
      });
      console.log(items);
      if(items.length == 0) {
        localforage.removeItem('transfers');
      }
      else {
        localforage.setItem('transfers', items);
      }
    }
  });
}



function addNew() {
  window.location.href = 'transfer_add.html';
}


async function getTransfer(id) {
  return new Promise(resolve => {
    var data = {};
    let db = localStorage.getItem('transfer');
    if(db.length) {
      data = JSON.parse(db);
      let row = data[id];
      console.log(row);
      resolve(row);
    }
  });
}


function getSearch() {
  $('#offset').val(0);
  $('#rows').text(0);
  $('#show_rows').text(0);
  $('#online-job').html('');
  getFilterList();
}

function loadMore() {
  getFilterList();
}


var throttleTimer;
const throttle = (callback, time) => {
  if (throttleTimer) return;
  throttleTimer = true;
  setTimeout(() => {
    callback();
    throttleTimer = false;
  }, time);
};


function morePage() {
  throttle(() => {
    const endOfPage = window.innerHeight + window.pageYOffset >= document.body.offsetHeight;
    if(endOfPage) {
      let limit = parseDefault(parseInt($('#limit').val()), 0);
      let show = parseDefault(parseInt($('#show').val()), 0)

      if(show < limit) {
        getFilterList();
      }
    }
  }, 1000);
};


window.addEventListener('scroll', () => {
  morePage();
});
