window.addEventListener('load', () => {
  let code = getCookie('rnCode');
  let from = getCookie('rnFrom');
  let to = getCookie('rnTo');
  let status = getCookie('rnStatus');

  $('#code').val(code);
  $('#fromDate').val(from);
  $('#toDate').val(to);
  $('#status').val((status == "" ? "all" : status));

  getFilterList();
});


function clearFilterList() {
  setCookie('rnCode', '');
  setCookie('rnFrom', '');
  setCookie('rnTo', '');
  setCookie('rnStatus', 'all');
  setCookie('rnPerpage', 20);
  setCookie('rnOffset', 0);

  window.location.reload();
}


function getSearch() {
  $('#offset').val(0);
  $('#rows').text(0);
  $('#show_rows').text(0);
  $('#online-job').html('');
  getFilterList();
}

async function getFilterList() {
  let code = $('#code').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let status = $('#status').val();
  let perpage = $('#perpage').val();
  let offset = $('#offset').val();

  setCookie('rnCode', code);
  setCookie('rnFrom', fromDate);
  setCookie('rnTo', toDate);
  setCookie('rnStatus', status);

  if(navigator.onLine) {
    load_in();
    let requestUri = URI + 'get_return_list';
    let header = new Headers();
    header.append('X-API-KEY', API_KEY);
    header.append('Authorization', AUTH);
    header.append('Content-type', 'application/json');

    let json = JSON.stringify({
      "code" : code,
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


const morePage = () => {
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

function addNew() {
  window.location.href = 'return_add.html';
}


function edit(id, code) {
  localStorage.setItem('return_id', id);
  localStorage.setItem('return_code', code);
  window.location.href = "return_edit.html";
}
