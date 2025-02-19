async function syncItem() {
  if(navigator.onLine) {
    let json = JSON.stringify({'user_id' : userId});
    let requestUri = URI + 'sync_user_items';
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

    const res = await fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then(result => {
      let ds = JSON.parse(result);

      if(ds.data != null || ds.data != "") {
        let data = [];

        ds.data.forEach((item, i) => {
          let serial = item.Serial;
          let docnum = item.DocNum;

          let arr = {
            "docnum" : item.DocNum,
            "serial" : item.Serial,
            "code" : item.ItemCode,
            "name" : item.ItemName,
            "whCode" : item.WhsCode
          };

          arr[serial] = serial;
          arr[docnum] = docnum;

          data.push(arr);
        });

        if(data.length == 0) {
          localforage.removeItem('inventory');
        }
        else {
          localforage.setItem('inventory', data);
        }

        return true;
      }
    })
    .catch((error) => {
      console.error('error', error);
      return false;
    });

    return res;
  }

  return false;
}
