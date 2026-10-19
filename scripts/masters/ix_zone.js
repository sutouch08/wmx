async function syncIXZones() {
  let url = `${BASE_URL}ix_sync/zone_sync/count_zone`;  
  load_in('0%');
  const total = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: null
  }).then(r => r.json());

  const limit = 1000;
  let offset = 0;  
  let retry = 3;
  
  while (offset < total && retry > 0) {
    const percent = Math.floor((offset / total) * 100);
    updateProgress(percent);
    const res = await fetch(`${BASE_URL}ix_sync/zone_sync/get_zones/${limit}/${offset}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: null
    }).then(r => r.json());

    if(res.offset == 0) {
      retry--;
    }

    offset += res.offset;
  }

  load_out();

  if(retry <= 0) {
    swal({
      title:'Something went wrong',
      text:'Failed to sync IX Zones after multiple attempts.',
      type:'error'
    }, 
    function() {
      window.location.reload();
    });    
  }
  else {
    swal({
      title:'Success',
      text:'IX Zones synced successfully.',
      type:'success'
    }, 
    function() {
      window.location.reload();
    });
  }
}

function updateProgress(percent) {
  document.getElementsByClassName('loader-text')[0].innerText = percent + "%";
}