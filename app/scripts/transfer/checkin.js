
window.addEventListener('load', function() {
  getItemList();
});

function showTab(name) {
  $('.tab-pane').removeClass('active in');
  $('#'+name+'-tab').addClass('active in');

  if(name == 'home') {
    $('#title').text('รับมิเตอร์');
    $('#sync-li').addClass('hide');
    $('#scan-li').removeClass('hide');
    $('#home').addClass('hide');
    $('#detail').removeClass('hide');
  }
  else {
    $('#title').text('รายการมิเตอร์');
    $('#scan-li').addClass('hide');
    $('#sync-li').removeClass('hide');
    $('#detail').addClass('hide');
    $('#home').removeClass('hide');
  }
}



function submitDocument() {
  let exists = false;
  let docnum = $('#doc-num').val();

  if(docnum.length < 5) {
    return false;
  }

  if(navigator.onLine) {
    getTransferDetail(docnum);
  }
  else {
    swal({
      title:'ข้อผิดพลาด',
      text:'ไม่สามารถโหลดข้อมูลในขณะออฟไลน์ได้',
      type:'warning'
    });
  }
}


$('#doc-num').keyup(function(e) {
  if(e.keyCode == 13) {
    getTransferDetail();
  }
  else {
    let val = $(this).val();
    $('#scan-result').val(val);
  }
});


function scanResult() {
  setTimeout(() => {
    const docnum = $('#scan-result').val();
    $('#doc-num').val(docnum);

    getTransferDetail();
  }, 100);
}

function activeSearch(option) {
  if(option == 'on') {
    $('#clear-icon').addClass('hide');
    $('#search-icon').removeClass('hide');
    return;
  }
  else {
    $('#search-icon').addClass('hide');
    $('#clear-icon').removeClass('hide');
  }
}

function clearSearch() {

}


function getTransferDetail() {
  let docnum = $('#scan-result').val();
  if(docnum.length ) {

    if(navigator.onLine) {
      $('#code').val(docnum);
      load_in();
      let json = JSON.stringify({"docNum" : docnum, 'reload' : 'N'});
      let requestUri = URI + 'get_transfer_details';
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
          load_out();
          $('#doc-num').val('');
          if(isJson(result)) {
            let ds = JSON.parse(result);
            if(ds.status == 'success') {
              let source = $('#template').html();
              let output = $('#result');

              render(source, ds.data, output);
              $('#btn-save').removeClass('hide');
              $('#promt-text').addClass('hide');
              window.scrollTo(0, document.body.scrollHeight);
            }
            else if(ds.status == 'exists') {
              swal({
                title:'เอกสารถูกโหลดไปแล้ว',
                text:`เอกสาร ${docnum} ถูกโหลดไปแล้ว ต้องการโหลดใหม่อีกครั้งหรือไม่`,
                type:'warning',
                showCancelButton: true,
            		confirmButtonColor: '#FA5858',
            		confirmButtonText: 'โหลดใหม่',
            		cancelButtonText: 'ยกเลิก',
            		closeOnConfirm: true
              }, function() {
                let body = JSON.stringify({"docNum" : docnum, 'reload' : 'Y'});
                let uri = URI + 'get_transfer_details';
                let hd = new Headers({
                  'X-API-KEY' : API_KEY,
                  'Authorization' : AUTH,
                  'Content-Type' : 'application/json'
                });

                let options = {method : 'POST', headers : hd, body : body};

                load_in();

                fetch(uri, options).then(rest => rest.text())
                .then(res => {
                  let rs = JSON.parse(res);
                  load_out();
                  if(rs.status == 'success') {
                    let source = $('#template').html();
                    let output = $('#result');

                    render(source, rs.data, output);
                    $('#btn-save').removeClass('hide');
                    $('#promt-text').addClass('hide');
                    window.scrollTo(0, document.body.scrollHeight);
                  }
                  else {
                    swal({
                      title:'Error!',
                      text:rs.message,
                      type:'error'
                    });
                  }
                })
              });
            }
            else {
              swal({
                title:'Error!',
                text:ds.message,
                type:'error',
                html:true
              });
            }
          }
          else {
            swal({
              title:'Error!',
              text:result,
              type:'error',
              html:true
            });
          }
        })
        .catch(error => {
          swal({
            title:'Error Excention!',
            text:error,
            type:'error',
            html:true
          });
        });
    }
    else {
      swal({
        title:'ข้อผิดพลาด',
        text:'ไม่สามารถโหลดข้อมูลในขณะออฟไลน์ได้',
        type:'warning'
      });
    }
  }
  else {
    swal({
      title:'Error!',
      text:"Barcode ไม่ถูกต้อง",
      type:'error'
    });
  }
}


function getItemList() {
  localforage.getItem('inventory').then((data) => {
    let ds = [];
    if(data == null || data == undefined) {
      data = [];
    }
    else {

      data.forEach((item, i) => {

        let xx = ds.filter((row) => {
          return row.docnum == item.docnum;
        });

        if(xx.length > 0) {
          let index = ds.map(object => object.docnum).indexOf(xx[0].docnum);

          ds[index].qty++;
        }
        else {
          ds.push({"docnum" : item.docnum, "qty" : 1});
        }
      });
    }

    let source = $('#stock-template').html();
    let output = $('#detail-table');
    render(source, data, output);
    reIndex();

    let sc = $('#docnum-template').html();
    let op = $('#doc-table');
    render(sc, ds, op);
  });
}



function saveItem() {
  const code = $('#code').val();
  if(navigator.onLine) {
    let requestUri = URI + 'update_user_item';
    let json = JSON.stringify({"docNum" : code});
    let header = new Headers({
      'X-API-KEY' : API_KEY,
      'Authorization' : AUTH,
      'Content-Type' : 'application/json'
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
      let rs = JSON.parse(result);
      if(rs.status == 'success') {
        syncItem();
        setTimeout(() => {
          swal({
            title:'Success!',
            type:'success',
            timer:1000
          });

          $('#result').html('');
          $('#btn-save').addClass('hide');
          $('#promt-text').removeClass('hide');
          getItemList();
          showTab('detail');

        }, 500);
      }
      else {
        swal({
          title:'Error!',
          text:rs.message,
          type:'error'
        });
      }
    })
    .catch((error)=> {
      console.error('error', error);
    });

  }
}



function deleteStockByDocNum(code) {
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
  },function() {
    let json = JSON.stringify({'docNum' : code});
    let requestUri = URI + 'delete_open_team_group_items';
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
      let rs = JSON.parse(result);

      if(rs.status == 'success') {
        localforage.getItem('inventory').then((data) => {
          if(data != null && data != undefined) {
            let items = data.filter((el) => {
              return el.docnum != code;
            });

            if(items.length == 0) {
              localforage.removeItem('inventory').then(() => {
                getItemList();
              });
            }
            else {
              localforage.setItem('inventory', items).then(() => {
                getItemList();
              });
            }
          }
        }).then(() => {
          setTimeout(() => {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });
          }, 200);
        })
      }
      else {
        swal({
          title:'Error!',
          text:rs.message,
          type:'error'
        });
      }
    })
    .catch(error => {
      console.error('error', error);
    });
  });
}


async function syncItemList() {
  load_in();
  await syncItem(getItemList);
  load_out();
}
