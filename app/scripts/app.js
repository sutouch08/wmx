var BASE_URL = 'http://localhost/wmx/';
var COOKIE_PATH = 'wmx';

if('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js').then(() => {
    console.log('Service Worker Registered');
  });
}


function logout() {
  if(navigator.onLine) {
    swal({
      title:'ออกจากระบบ',
      text:"ต้องการลงชื่อออกจากระบบหรือไม่ ?",
      type:'warning',
      html:true,
      showCancelButton: true,
  		confirmButtonColor: '#FA5858',
  		confirmButtonText: 'ยืนยัน',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: true
    }, () => {
      doLogout();
    });
  }
  else {
    swal({
      title:'คำเตือน !',
      text:"คุณกำลังจะออกจากระบบในขณะออฟไลน์ <br/>คุณจะไม่สามารถกลับเข้าระบบได้อีกจนกว่าจะกลับมาออนไลน์อีกครั้ง<br/>ต้องการออกจากระบบหรือไม่ ?",
      type:'warning',
      html:true,
      showCancelButton: true,
  		confirmButtonColor: '#FA5858',
  		confirmButtonText: 'ยืนยัน',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: true
    }, () => {
      doLogout();
    });
  }
}


function doLogout() {
  deleteCookie('uid', COOKIE_PATH);
  window.location.href = "login.html";
}
