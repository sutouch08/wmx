
window.addEventListener('load', function() {
  updateScanType();
  getItemList();
});

function getSearch() {
  let txt = $('#scan-result').val();

  $('#search-text').val(txt);

  setTimeout(() => {
    searchText();
  }, 100);
}

function stopScan() {
	scanner.stop().then((ignore) => {
		$('#cam').addClass('hide');
    $('#reader-backdrop').addClass('hide');
    $('.sc').removeClass('hide');
	});
}

function searchText() {
  let txt = $.trim($('#search-text').val());

  if(txt.length) {
    $('#search-icon').addClass('hide');
    $('#clear-icon').removeClass('hide');

    setTimeout(() => {
      getItemList();
    }, 100);
  }
}

function clearSearch() {
  $('#search-text').val('');
  $('#clear-icon').addClass('hide');
  $('#search-icon').removeClass('hide');

  setTimeout(() => {
    getItemList();
  }, 100);
}

function getItemList() {
  let search = $.trim($('#search-text').val());
  let all = 0;
  let completed = 0;
  load_in();
  localforage.getItem('inventory').then((data) => {
    let ds = [];
    if(data != null && data != undefined) {

      data.forEach((item, i) => {
        if(item.status == "I") {
          data[i].status = null;
          completed++;
        }
        all++;
      });

      if(search != "") {
        let keys = ['peaNo', 'date', 'name'];
        ds = data.filter((obj) => keys.some((key) => obj[key].includes(search)));
      }
      else {
        ds = data;
      }
    }

    $('#all-meter').text(`ทั้งหมด ${all} รายการ`);
    $('#complete-meter').text(`รอส่งผล ${completed} รายการ`);
    let source = $('#stock-template').html();
    let output = $('#detail-table');
    render(source, ds, output);
    reIndex();
    load_out();
  });
}

async function syncItemList() {
  await syncItem();
  await getItemList();
}

$('#search-text').keyup(function(e) {
  $('#clear-icon').addClass('hide');
  $('#search-icon').removeClass('hide');
  if(e.keyCode == 13) {
    searchText();
  }
})
