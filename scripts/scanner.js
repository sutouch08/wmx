var scanner;
var config;
// var scanType = "barcode"; //-- qrcode, barcode, both

window.addEventListener('load', () => {
  readerInit();
});

function readerInit() {

  let formatToSupport = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.EAN_13,
    Html5QrcodeSupportedFormats.CODE_39,
    Html5QrcodeSupportedFormats.CODE_93,
    Html5QrcodeSupportedFormats.CODE_128
  ];

  let qrWidth = window.innerWidth;
  let qrHeight = window.innerHeight;

  // if( scanType == 'barcode') {
  //   formatToSupport = [
  //     Html5QrcodeSupportedFormats.EAN_13,
  //     Html5QrcodeSupportedFormats.CODE_39,
  //     Html5QrcodeSupportedFormats.CODE_93,
  //     Html5QrcodeSupportedFormats.CODE_128
  //   ];
  //
  //   qrWidth = 250;
  //   qrHeight = 250;
  // }
  //
  // if( scanType == 'qrcode') {
  //   formatToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
  // }

  scanner = new Html5Qrcode("reader", {formatsToSupport: formatToSupport});

  config = {
    fps: 60,
    qrbox: {width: qrWidth, height: qrHeight, center: false},
    experimentalFeatures: {
      useBarCodeDetectorIfSupported: true
    }
  };
}

function saveCameraId() {
  let camId = $("input[name='camera_id']:checked").val();

  if(camId === undefined || camId == "") {
    $('#camera-error').text("Please choose camera for use to scan");
    return false;
  }
  else {
    localStorage.setItem('cameraId', camId);
    closeModal('cameras-modal');
  }
}

function changeCameraId() {
  closeExtraMenu();

  Html5Qrcode.getCameras().then(devices => {
    if(devices && devices.length) {
      let source = $('#cameras-list-template').html();
      let output = $('#cameras-list');
      let camId = localStorage.getItem('cameraId');
      render(source, devices, output);
      $('#'+camId).prop('checked', true);
      showModal('cameras-modal');
    }
  })
  .catch((error) => {
    console.log('error', error);
    swal({
      title:'Oops!',
      text: error,
      type:'error'
    });
  });
}


function startScan(actionCallback) {
  let camId = localStorage.getItem('cameraId');

  if(camId == "" || camId == undefined) {
    changeCameraId();
  }
  else {
    $('#reader-backdrop').removeClass('hide');

    scanner.start({deviceId: {exact: camId}}, config, (decodedText, decodedResult) => {
      stopScan();
      $('#scan-result').val(decodedText);
      console.log(decodedText);

      if(actionCallback != null && actionCallback != undefined) {
        actionCallback();
      }
      else {
        console.log(actionCallback);
      }
    });
  }
}

function stopScan() {
	scanner.stop().then((ignore) => {
    $('#reader-backdrop').addClass('hide');
	});
}
