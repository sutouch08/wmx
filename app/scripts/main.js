
var uid = localStorage.getItem('sttc_uid');
var cameraId = localStorage.getItem('cameraId');
var uname = "";
var displayName = "";
var ugroup = "";
var teamName = "";
var fWhCode = "";
var tWhCode = "";
var userId = "";
var teamId = "";
var teamGroupId = "";
var teamGroupName = "";
var canGetMeter = 0;
var useStrongPwd = 0;
var scanType = "both";

window.addEventListener('load', function() {

  if(uid == null || uid == "" || uid == 0 || uid == undefined) {
    window.location.href = "login.html";
  }
  else {
    dataInit();
  }

  var isOnline = setInterval(() => {

    if(navigator.onLine) {
      $('#online-status').text('Online');
    }
    else {
      $('#online-status').text('Offline');
    }
  }, 10000);

  loadSettingMenu();
});


function loadSettingMenu() {
  let menuPad = `
  <input type="hidden" id="scan-type" value="" />
  <input type="hidden" id="scan-result" value="" />
  <input type="hidden" id="code" value="" />
  <div id="cam" class="hide" style="position: fixed; top:45px; left:0; width:100vw; z-index:13;">
    <div id="reader" style="width:100%;"></div>
  </div>
  <div id="reader-backdrop" class="hide" style="position: fixed; top:0px; width:100%; height:100vh; background-color:#000000e0; z-index:12;">
    <p class="text-center" style="position:absolute; bottom:90px; width:100vw;">
      <a class="text-center" id="btn-stop" href="javascript:stopScan()"
      style="margin:0px; border:none; border-radius:25px;
      padding:10px 17px; font-size:24px; line-height:0.8;
      background-color:salmon; color:black;">&times;</a>
    </p>
  </div>

  <div class="menu-pad move-out" id="menu-pad">
    <div class="width-100" style="height:100vh; padding-top:45px; color:white;">
      <li class="menu-pad-li"><a href="javascript:renderUserData()"><i class="fa fa-user"></i> ข้อมูลผู้ใช้งาน</a></li>
      <li class="menu-pad-li"><a href="javascript:changeCameraId()"><i class="fa fa-camera"></i> ตั้งค่ากล้อง</a></li>
      <li class="menu-pad-li"><a href="javascript:change_pwd()"><i class="fa fa-key"></i> เปลี่ยนรหัสผ่าน</a></li>
      <li class="menu-pad-li"><a href="javascript:logout()"><i class="fa fa-sign-out"></i> ออกจากระบบ</a></li>
      <li class="menu-pad-cl"><a href="javascript:toggleMenu()">&times;</a></li>
    </div>
    <input type="hidden" id="menu" value="hide" />
  </div>


  <div class="modal fade" id="cameras-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:500px; max-width:95%; margin-left:auto; margin-right:auto;">
     <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         <h4 class="modal-title">Choose Camera</h4>
         <input type="hidden" id="select-side" value="i" />
        </div>
        <div class="modal-body">
          <div class="row" id="cameras-list" style="padding-left:12px; padding-right:12px;">

          </div>
          <div class="err-label" id="camera-error"></div>
        </div>
        <div class="modal-footer">
  				<button type="button" class="btn btn-sm btn-success btn-100" onclick="saveCameraId()">Save</button>
        </div>
     </div>
   </div>
  </div>

  <script id="cameras-list-template" type="text/x-handlebarsTemplate">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    {{#each this}}
    <div class="radio">
      <label>
        <input type="radio" name="camera_id" id="{{id}}"  class="ace" maxlength="100"	value="{{id}}" />
        <span class="lbl">{{label}}</span>
      </label>
    </div>
    {{/each}}
  </div>
  </script>

  <div class="modal fade" id="user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:500px; max-width:95%; margin-left:auto; margin-right:auto;">
     <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         <h4 class="modal-title">Your Information</h4>
        </div>
        <div class="modal-body">
          <div class="row" id="user-table" style="padding-left:12px; padding-right:12px;">

          </div>
        </div>
     </div>
   </div>
  </div>

  <script id="user-template" type="text/x-handlebarsTemplate">
    <table class="table table-bordered border-1">
      <tr><td class="fix-width-100">Username</td><td class="">{{uname}}</td></tr>
      <tr><td class="fix-width-100">ชื่อ</td><td class="">{{displayName}}</td></tr>
      <tr><td class="fix-width-100">เขต/พื้นที่</td><td class="">{{teamName}}</td></tr>
      <tr><td class="fix-width-100">ทีมติดตั้ง</td><td class="">{{team_group_name}}</td></tr>
      <tr><td class="fix-width-100">คลังต้นทาง</td><td class="">{{fromWhsCode}} : {{from_warehouse_name}}</td></tr>
      <tr><td class="fix-width-100">คลังปลายทาง</td><td class="">{{toWhsCode}} : {{to_warehouse_name}}</td></tr>
    </table>
  </script>

  <script id="damage-list-template" type="text/x-handlebarsTemplate">
    {{#each this}}
      <option value="{{reason_id}}">{{title}}</option>
    {{/each}}
  </script>
  `;

  $('#main-container').append(menuPad);
}

async function dataInit() {
  await updateUserData();

  let userdata = localStorage.getItem('userdata');

  if(userdata.length) {
    ud = JSON.parse(userdata);
    uid = ud.uid; //getCookie('uid');
    userId = ud.userId; //getCookie('userId');
    uname = ud.uname; //getCookie('uname');
    displayName = ud.displayName;//decodeURIComponent(getCookie('displayName'));
    ugroup = ud.ugroup;//getCookie('ugroup');
    teamId = ud.team_id;
    teamName = ud.teamName; // getCookie('teamName');
    teamGroupId = ud.team_group_id;
    teamGroupName = ud.team_group_name;
    fWhCode = ud.fromWhsCode; //getCookie('fromWhsCode');
    tWhCode = ud.toWhsCode; //getCookie('toWhsCode');
    canGetMeter = ud.can_get_meter;
    useStrongPwd = parseDefault(parseInt(ud.is_strong_pwd), 0);
    scanType = ud.scanType;
  }

  readerInit();
}

function updateUserData() {
  return new Promise((resolve) => {
    if(navigator.onLine) {
      // get data from server
      let requestUri = URI + 'user_data';
      let header = new Headers();
      header.append('X-API-KEY', API_KEY);
      header.append('Authorization', AUTH);
      header.append('Content-type', 'application/json');
      let json = JSON.stringify({"uid" : uid});
      let requestOptions = {
        method : 'POST',
        headers : header,
        body : json,
        redirect : 'follow'
      };

      fetch(requestUri, requestOptions)
      .then(response => response.text())
      .then(result => JSON.parse(result))
      .then((ds) => {
        if(ds.status === 'success') {
          if(ds.userdata != null || ds.userdata != "") {
            localStorage.setItem('userdata', JSON.stringify(ds.userdata));
            resolve(console.log('ok'));
          }
          else {
            console.log('empty userdata');
            resolve(console.log('empty'));
          }
        }
        else {
          resolve(console.log(ds.message));
        }
      });
    }
    else
    {
      resolve(console.log('offline'));
    }
  });
}

function toggleMenu() {
  let menu = $('#menu');
  let pad = $('#menu-pad');

  if(menu.val() == "hide") {
    menu.val("show");
    pad.addClass('move-in');
  }
  else {
    menu.val("hide");
    pad.removeClass('move-in');
  }
}


function renderUserData() {
  let userdata = localStorage.getItem('userdata');

  if(userdata.length) {
    let data = JSON.parse(userdata);
    let source = $('#user-template').html();
    let output = $('#user-table');

    render(source, data, output);

    $('#user-modal').modal('show');
  }
}


function change_pwd() {
  if(navigator.onLine) {
    window.location.href = "change_pwd.html";
  }
  else {
    swal({
      title:'ข้อผิดพลาด',
      text:'ไม่สามารถเปลี่ยนรหัสผ่านได้ในขณะออฟไลน์',
      type:'error'
    });
  }
}
