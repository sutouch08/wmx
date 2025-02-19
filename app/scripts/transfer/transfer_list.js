
window.addEventListener('load', () => {
  loadPage();
});


async function loadPage() {
  load_in();
  await getFilterList();
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
  $('#online-job').html('');

  //await getOfflineList();
  await getFilterList();
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
  $('#online-job').html('');

  await getFilterList();
  load_out();
}

function getFilterList() {
  return new Promise((resolve) => {
    if(navigator.onLine) {
      let searchText = $('#search-text').val();
      let perpage = $('#perpage').val();
      let offset = $('#offset').val();
      let requestUri = URI + 'get_transfer_history';
      let header = new Headers();
      header.append('X-API-KEY', API_KEY);
      header.append('Authorization', AUTH);
      header.append('Content-type', 'application/json');

      let json = JSON.stringify({
        "search_text" : searchText,
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
          $('#num_rows').text(addCommas(ds.rows));
          $('#limit').val(ds.rows);

          if(ds.data.length) {
            let source = $('#online-template').html();
            let output = $('#online-job');
            render_append(source, ds.data, output);
            offset = offset == 0 ? 1 * perpage : offset * perpage;
            $('#offset').val(offset);
            let rows = parseDefault(parseInt($('#show').val()), 0) + ds.data.length;
            $('#show_rows').text(addCommas(rows));
            $('#show').val(rows);
          }
          else {
            noData();
          }

          resolve(true);
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });

          resolve();
        }
      })
      .catch((error) => {
        console.log('error', error);
      });
    }
    else {
      noData();
      resolve(true);
    }
  })
}

function noData(){
  $('#no-list').css('display', 'block');
  $('#no-list-label').animate({opacity:0.9},500);

  setTimeout(() => {
    $('#no-list-label').animate({opacity:0}, 500);

    setTimeout(() => {
      $('#no-list').css('display', 'none');
    }, 500);
  }, 1000);
}

function showDetail(id) {
  if(navigator.onLine) {
    load_in();
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

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then((result) => {
      let ds = JSON.parse(result);
      $('#u-pea-no').val(ds.u_pea_no);
      $('#use-age').val(ds.use_age);
      $('#cust-route').val(ds.route);
      $('#u-power-no').val(ds.u_power_no);
      $('#u-dispose-id').val(ds.damage_name);
      $('#u-lat').html(ds.u_lat);
      $('#u-long').html(ds.u_lng);
      $('#u-preview').html('<img id="u-image" src="'+ds.u_image+'" style="width:100%; border-radius:10px;" alt="Item image" />');


      $('#i-pea-no').val(ds.i_pea_no);
      $('#i-power-no').val(ds.i_power_no);
      $('#phase-selected').val(ds.phase);
      let txt = `<p>Item Code: ${ds.ItemCode}</p><p>Description: ${ds.ItemName}</p><p>Serial: ${ds.i_serial}</p>`;
      $('#i-result').html(txt);
      $('#i-lat').html(ds.latitude);
      $('#i-long').html(ds.longitude);
      $('#remark').val(ds.remark);

      $('#i-preview').html('<img id="i-image" src="'+ds.i_image+'" style="width:100%; border-radius:10px;" alt="Item image" />');

      toggleSign(ds.sign_status);

      if(ds.sign_status == '0') {
        signaturePad.fromDataURL(ds.signature_image, {ratio: 1, width: parentWidth, height: parentHeight});
      }


      signaturePad.off();

      suggest();
      load_out();

      $('body').addClass('noscroll');
      $('#cover').addClass('slide-in');
      $('#close-cover').removeClass('hide');
    })
  }
  else {
    swal({
      title:'Offline',
      text:'ไม่สามารถแสดงรายการได้ในขณะออฟไลน์',
      type:'info'
    });
  }


}


function closeCover() {
  $('body').removeClass('noscroll');
  $('#close-cover').addClass('hide');
  $('#cover').removeClass('slide-in');
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

function loadMore() {
  throttle(() => {
    const endOfPage = window.innerHeight + window.pageYOffset >= document.body.offsetHeight;
    if(endOfPage) {
      let limit = parseDefault(parseInt($('#limit').val()), 0);
      let show = parseDefault(parseInt($('#show').val()), 0)
      console.log('load');
      if(show < limit) {
        getFilterList();
      }
      else {
        noData();
      }
    }
  }, 500);
};

window.addEventListener('scroll', () => {
  loadMore();
});
