var canvas = document.getElementById('canvas');
//var signature = document.getElementById('signature');
var parentWidth = $(canvas).parent().outerWidth();
var parentHeight = $(canvas).parent().outerHeight();
canvas.setAttribute("height", parentHeight);
canvas.setAttribute("width", parentWidth);
// signature.setAttribute('height', parentHeight);
// signature.setAttribute('width', parentWidth);
var signaturePad = new SignaturePad(canvas, {backgroundColor: 'rgb(255, 255, 255)'});

signaturePad.off(); //-- ปิดไว้ก่อน กดรับทราบแล้วค่อย on
canvas.style.touchAction = 'auto';


function clearSignaturePad() {
  let signStatus = $('#sign-status').val();

  if(signStatus == '0') {
    $('#signature').val('');
    signaturePad.clear();
    signaturePad.on();
  }
}


function toggleSign(option) {
  // 0 = รับทราบ,
  // 1 = ไม่อยู่
  // 2 = ไม่ลงนาม
  $('#signature').val('');
  $('#sign-status').val(option);
  $('.tg').removeClass('btn-primary');

  if(option == '0') {
    $('#btn-sign-0').addClass('btn-primary');
    signaturePad.on();
    canvas.style.touchAction = 'none';
    return;
  }

  if(option == '1') {
    $('#btn-sign-1').addClass('btn-primary');
    signaturePad.clear();
    signaturePad.off();
    canvas.style.touchAction = 'auto';
    return;
  }

  if(option == '2') {
    $('#btn-sign-2').addClass('btn-primary');
    signaturePad.clear();
    signaturePad.off();
    canvas.style.touchAction = 'auto';
  }
}


function signAccept() {
  if(signaturePad.isEmpty()) {
    swal({
      title:'กรุณาลงลายมือชื่อ',
      type:'warning'
    });
  }
  else {
    $('#signature').val(signaturePad.toDataURL("image/jpeg"));
    signaturePad.off();
    canvas.style.touchAction = 'auto';
  }
}
