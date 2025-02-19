const URI = BASE_URL +"rest/ic/";
const API_KEY = "513bcfa2b82dc1735a07b97b7f870106";
const USERNAME = "api@wms";
const REALM = "Warrix#1";
const AUTH = "Basic YXBpQHdtczpXQHJyaXgkcDBydA==";


/* Example
var myHeaders = new Headers();
myHeaders.append("X-API-KEY", "513bcfa2b82dc1735a07b97b7f870106");
myHeaders.append("Authorization", "Digest username=\"api@bexsys\", realm=\"Bexsys@1234\", nonce=\"undefined\", uri=\"/sttc/rest/V1/api/user_data\", algorithm=\"MD5\", response=\"2de2be8b95bc35331783ce7b4722da59\"");
myHeaders.append("Content-Type", "application/json");
myHeaders.append("Cookie", "ci_session=79bcedca08db9c25abaab900760d22cdeb43871b");

var raw = JSON.stringify({
  "uid": "513bcfa2b82dc1735a07b97b7f870106",
  "uname": "out1"
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("localhost/sttc/rest/V1/api/user_data", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
*/
