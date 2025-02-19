
window.addEventListener('load', function() {
  load_in();
  Promise.all([syncWorkList(), syncItem()])
  .then(() => {
    getWorkList();
    load_out();
  })
});

function getSearch() {
  let txt = $('#scan-result').val();

  $('#search-text').val(txt);

  setTimeout(() => {
    searchText();
  }, 100);
}


function getSearch() {
  let txt = $('#scan-result').val();

  $('#search-text').val(txt);

  setTimeout(() => {
    searchText();
  }, 100);
}

function searchText() {
  load_in();
  $('#search-icon').addClass('hide');
  $('#clear-icon').removeClass('hide');
  getWorkList();
  load_out();
}

function clearSearch() {
  load_in();
  $('#search-text').val('');
  $('#clear-icon').addClass('hide');
  $('#search-icon').removeClass('hide');
  getWorkList();
  load_out();
}


$('#search-text').keyup(function(e) {
  activeSearch();

  if(e.keyCode == 13) {
    searchText();
  }
})



function activeSearch() {
  $('#clear-icon').addClass('hide');
  $('#search-icon').removeClass('hide');
}



async function updateWorkList() {
  load_in();
  syncWorkList().then((result) => {
    if(result == 'offline') {
      load_out();
      swal({
        title:'Oops',
        text:'ไม่สามารถโหลดใบสั่งงานในขณะออฟไลน์ได้',
        type:'error'
      });

      return false;
    }

    getWorkList();
    load_out();
  });
}

function getWorkList() {
  let search = $.trim($('#search-text').val());
  localforage.getItem('work_list').then((data) => {
    let ds = [];
    if(data != null && data != undefined) {
      var pending = data.filter((obj) => {
        return obj.status == "P";
      })

      if(search != "") {
        const keys = ['pea_no', 'cust_route', 'cust_address'];
        ds = pending.filter((obj) => keys.some((key) => obj[key].includes(search)));
      }
      else {
        ds = pending;
      }

      let source = $('#stock-template').html();
      let output = $('#detail-table');
      render(source, ds, output);
      reIndex();
    }
    else {
      $('#detail-table').html('<div style="font-size:24px; color:#aaa; text-align:center; padding-top:50px;">ไม่พบใบสั่งงาน</div>');
    }
    console.log('work list updated');
  });
}


async function showWorkList(pea_no) {
  load_in();
  let ds = await getSelectedWorkList(pea_no);

  if(ds.id !== undefined) {
    let source = $('#modal-template').html();
    let output = $('#data-list');
    render(source, ds, output);
    load_out();
    $('#work-list-modal').modal('show');
  }
  else {
    load_out();

    setTimeout(() => {
      swal({
        title:'Oops',
        text: ds,
        type:'error'
      });

    }, 100);
  }

}


function getSelectedWorkList(pea_no) {
  return new Promise((resolve, reject) => {
    if(navigator.onLine) {
      let json = JSON.stringify({"pea_no" : pea_no, "team_group_id" : teamGroupId});
      let requestUri = URI + 'get_open_work_list_by_pea_no';
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

        if(ds.status == 'success') {
          resolve(ds.data);
        }
        else {
          resolve(ds.message);
        }
      })
      .catch((error) => {
        reject(error);
      })
    }
    else {
      localforage.getItem('work_list').then((data) => {
        if(data != null && data != undefined) {
          const keys = ['pea_no'];
          let ds = data.filter((obj) => keys.some((key) => obj[key].includes(pea_no)));
          resolve(ds[0]);
        }
        else {
          resolve('ไม่พบใบสั่งงาน');
        }
      })
      .catch((error) => {
        reject(error);
      });
    }
  })
}


function goInstall(pea_no) {
  localStorage.setItem('work_no', pea_no);
  window.location.href = "install.html";
}

function goInform(pea_no) {
  if(pea_no !== null && pea_no !== undefined && pea_no != "") {
    localStorage.setItem('inform_no', pea_no);
  }
  else {
    localStorage.removeItem('inform_no');
  }
  
  window.location.href = "inform.html";
}
