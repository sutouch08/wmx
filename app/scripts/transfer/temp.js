
var all_offline = 0;
var result_filter = 0;
window.addEventListener('load', () => {
  loadPage();
});

async function loadPage() {
  load_in();
  await getOfflineList();

  if(navigator.onLine) {
    $('#btn-send-all').removeAttr('disabled');
    $('#btn-send-checked').removeAttr('disabled');
  }
  else {
    $('#btn-send-all').attr('disabled', 'disabled');
    $('#btn-send-checked').attr('disabled', 'disabled');
  }
  load_out();
}

function clearSearch() {
  $('#search-text').val('');
  activeSearch();
  setTimeout(() => {
    searchText();
  }, 500);
}

function activeSearch() {
  $('#clear-icon').addClass('hide');
  $('#search-icon').removeClass('hide');
}

$('#search-text').keyup(function(e) {
  activeSearch();

  if(e.keyCode == 13) {
    searchText();
  }
})

async function getSearch() {
  $('#offset').val(0);
  $('#rows').text(0);
  $('#show_rows').text(0);
  $('#offline-job').val('');

  await getOfflineList();
}

function getSearch() {
  let txt = $('#scan-result').val();
  $('#search-text').val(txt);

  setTimeout(() => {
    searchText();
  }, 500);
}

async function searchText() {
  load_in();
  let txt = $('#search-text').val();

  if(txt.length) {
    $('#search-icon').addClass('hide');
    $('#clear-icon').removeClass('hide');
  }

  $('#offset').val(0);
  $('#limit').val(20);
  $('#show').val(0);
  $('#show_rows').text('0');

  await getOfflineList();
  load_out();
}

function getOfflineList() {
  return new Promise((resolve) => {
    localforage.getItem('transfers')
    .then((data) => {
      if(data !== null && data !== undefined) {
        all_offline = data.length;
        let search = $('#search-text').val();
        let ds = [];
        if(search.length) {
          let keys = ['u_pea_no', 'route'];
          //ds = data.filter((row) => keys.some((key) => row[key].includes(search)));
          data.forEach((item, i) => {
            if(item['u_pea_no'].includes(search) || item['route'].includes(search)) {
              if(item.type == "install") {
                item.can_edit = true;
              }
              else {
                item.can_edit = false;
              }

              ds.push(item);
            }
          });
        }
        else {
          //ds = data;
          data.forEach((item, i) => {
            if(item.type == "install") {
              item.can_edit = true;
            }
            else {
              item.can_edit = false;
            }

            ds.push(item);
          });
        }

        result_filter = ds.length;
        $('#show_rows').text(result_filter);
        $('#num_rows').text(all_offline);
        let source = $('#offline-template').html();
        let output = $('#offline-job');
        render(source, ds, output);
      }

      resolve(true);
    });
  })
}


function showWorkList(u_pea_no) {
  try {
    localStorage.setItem('offline_u_pea_no', u_pea_no);
    setTimeout(() => {
      window.location.href = "offline_detail.html";
    }, 500);
  }
  catch(e) {
    if(! (e instanceof Error)) {
      e = new Error(e);
    }

    swal({
      title:'Error!',
      text: e.message,
      type:'error',
      html:true
    });
  }
}

var success = [];
var meter = [];
var failed = [];
var all = 0;

function sendChecked() {
  if(navigator.onLine) {
    let list = [];
    $('.chk:checked').each(function() {
      list.push($(this).val());
    });

    if(list.length) {
      swal({
        title:'ส่งผลการสับเปลี่ยน',
        text:`ต้องการส่งผลการสับเปลี่ยนตามรายการที่เลือกหรือไม่ ?`,
        showCancelButton:true,
        confirmButtonColor:'#428bca',
        confirmButtonText:'ส่ง',
        cancelButtonColor:'#9e9e9e',
        cancelButtonText:'ยกเลิก',
        closeOnConfirm:true
      }, function() {
        load_in();
        var promises = [];
        success = [];
        meter = [];
        failed = [];
        all = list.length;

        localforage.getItem('transfers')
        .then((res) => {
          let ds = [];
          if(res !== null && res !== undefined) {
            ds = res.filter((obj) => {
              return list.includes(obj.u_pea_no);
            });

            if(ds.length) {
              ds.forEach((item) => {
                if(item.type == "install") {
                  promises.push(fetchData(item));
                }

                if(item.type == "inform") {
                  promises.push(fetchInform(item))
                }
              });

              Promise.all(promises)
              .then(() => {
                localforage.getItem('transfers')
                .then((result) => {
                  var rs = result.filter((row) => {
                    return !list.includes(row.u_pea_no);
                  });

                  return rs;
                })
                .then((ro) => {
                  if(ro.length) {
                    localforage.setItem('transfers', ro);
                  }
                  else {
                    localforage.removeItem('transfers');
                  }

                  load_out();
                })
              })
              .then(() => {
                setTimeout(() => {
                  swal({
                    title:'Success',
                    type:'success',
                    timer:1000
                  });
                }, 200)

                syncWorkList();
                syncItem();
                setTimeout(() => {
                  reload();
                }, 1200);
              });
            }
            else {
              setTimeout(() => {
                swal({
                  title:'System error!',
                  text:'No data found',
                  type:'error'
                });
              }, 200)
            }
          }
          else {
            setTimeout(() => {
              swal({
                title:'ไม่พบรายการ',
                text:'กรุณาเลือกรายการที่ต้องการส่งผลการติดตั้ง',
                type:'info'
              });
            }, 200)
          }
        })
      });
    }
    else {
      swal({
        title:'ไม่พบรายการ',
        text:'กรุณาเลือกรายการที่ต้องการส่งผลการติดตั้ง',
        type:'info'
      });
    }
  }
  else {
    swal({
      title:'Ofline',
      text:'ไม่สามารถส่งข้อมูลขณะออฟไลน์ได้',
      type:'warning'
    });
  }
}

function sendAll() {
  if(navigator.onLine) {
    if($('.chk').length) {
      swal({
        title:'ส่งผลการสับเปลี่ยน',
        text:`ต้องการส่งผลการสับเปลี่ยนทั้งหมดหรือไม่ ?`,
        showCancelButton:true,
        confirmButtonColor:'#428bca',
        confirmButtonText:'ส่ง',
        cancelButtonColor:'#9e9e9e',
        cancelButtonText:'ยกเลิก',
        closeOnConfirm:true
      }, function() {
        load_in();
        var promises = [];
        success = [];
        meter = [];
        failed = [];
        all = 0;

        localforage.getItem('transfers')
        .then((res) => {
          if(res !== null && res !== undefined) {
            all = res.length;
            res.forEach((item) => {
              if(item.type == "install") {
                promises.push(fetchData(item)) //--- push
              }

              if(item.type == "inform") {
                promises.push(fetchInform(item));
              }
            }) //-- foreach


            Promise.all(promises)
            .then(() => {
              load_out();
              if(success.length > 0 && success.length == all) {
                localforage.removeItem('transfers');
              }

              if(success.length > 0 && success.length < all) {
                localforage.getItem('transfers')
                .then((data) => {
                  var ds = [];
                  if(data !== null && data !== undefined) {
                    ds = data.filter((obj) => {
                      return !success.includes(obj.u_pea_no);
                    })

                    if(ds.length > 0) {
                      localforage.setItem('transfers', ds);
                    }
                    else {
                      localforage.removeItem('transfers');
                    }
                  }
                })
              }
            })
            .then(() => {
              setTimeout(() => {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });
              }, 200)
              syncWorkList();
              syncItem();
              setTimeout(() => {
                reload();
              }, 1200);
            })
          }
        })
      })
    }
  }
  else {
    swal({
      title:'Ofline',
      text:'ไม่สามารถส่งข้อมูลขณะออฟไลน์ได้',
      type:'warning'
    });
  }
}

function fetchData(item) {
  return new Promise((resolve, reject) => {
    //console.log(item);
    let json = JSON.stringify(item);
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
    .then((result) => {
      if(isJson(result)) {
        let rs = JSON.parse(result);
        //console.log(result);
        if(rs.status == 'success') {
          success.push(item.u_pea_no);
          meter.push(item.i_pea_no);
          $('#div-'+item.u_pea_no).remove();
        }
        else {
          failed.push(item.u_pea_no);
        }

        resolve('x');
      }
      else {
        failed.push(item.u_pea_no);
        resolve('x')
      }
    })
  })
}

function fetchInform(item) {
  return new Promise((resolve, reject) => {
    //console.log(item);
    let json = JSON.stringify(item);
    let requestUri = URI + 'add_inform';
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
      if(isJson(result)) {
        let rs = JSON.parse(result);
        //console.log(result);
        if(rs.status == 'success') {
          success.push(item.u_pea_no);          
          $('#div-'+item.u_pea_no).remove();
        }
        else {
          failed.push(item.u_pea_no);
        }

        resolve('x');
      }
      else {
        failed.push(item.u_pea_no);
        resolve('x')
      }
    })
  })
}
