window.addEventListener('load', () => {
  getData();
});

async function getData() {
  load_in();
  await renderDamageList();
  let ds = await getWorkData();

  if(ds.u_pea_no != undefined) {
    console.log(ds);
    $('#u-pea-no').val(ds.u_pea_no);
    $('#use-age').val(ds.use_age);
    $('#cust-route').val(ds.route);
    $('#u-power-no').val(ds.u_power_no);
    $('#u-dispose-id').val(ds.damage_id);
    $('#u-lat').html(ds.u_lat);
    $('#u-long').html(ds.u_lng);
    $('#u-preview').html('<img id="u-image" src="'+ds.u_image+'" style="width:100%; border-radius:10px;" alt="Item image" />');
    $('#u-photo-btn').addClass('hide');
    $('#u-preview').removeClass('hide');
    $('#del-u-image').removeClass('hide');
    $('#u-blob').val(ds.u_image);
    $('#u-orientation').val(ds.u_orientation);

    $('#i-pea-no').val(ds.i_pea_no);
    $('#i-pea').val(ds.i_pea_no);
    $('#c-pea').val(ds.i_pea_no); //--- ไว้ใช้กรณีที่มีการเปลียนมิเตอร์ลูกใหม่
    $('#i-power-no').val(ds.i_power_no);
    $('#phase-selected').val(ds.phase);
    $('#i-serial').val(ds.i_serial);
    $('#from-doc').val(ds.fromDoc);
    $('#item-code').val(ds.itemCode);
    $('#item-name').val(ds.itemName);
    let txt = `<p>Item Code: ${ds.itemCode}</p><p>Description: ${ds.itemName}</p><p>Serial: ${ds.i_serial}</p>`;
    $('#i-result').html(txt);
    $('#i-lat').html(ds.i_lat);
    $('#i-long').html(ds.i_lng);
    $('#remark').val(ds.remark);
    $('#select-clear-alarm').val(ds.is_clear_alarm);
    $('#is-clear-alarm').val(ds.is_clear_alarm);

    $('#i-preview').html('<img id="i-image" src="'+ds.i_image+'" style="width:100%; border-radius:10px;" alt="Item image" />');
    $('#i-photo-btn').addClass('hide');
    $('#i-preview').removeClass('hide');
    $('#del-i-image').removeClass('hide');
    $('#i-blob').val(ds.i_image);
    $('#i-orientation').val(ds.i_orientation);

    toggleSign(ds.sign_status);
    if(ds.sign_status == '0') {
      signaturePad.fromDataURL(ds.signature_image, {ratio: 1, width: parentWidth, height: parentHeight});
    }

    $('#signature').val(ds.signature_image);

    signaturePad.off();

    await suggest();
    load_out();
  }
  else {
    load_out();
    swal({
      title:'Error!',
      text:ds,
      type:'error'
    });
  }
}

function getWorkData() {
  return new Promise((resolve, reject) => {
    let pea_no = localStorage.getItem('offline_u_pea_no');
    localforage.getItem('transfers').then((data) => {
      if(data != null && data != undefined) {
        let ds = data.filter((obj) => {
          return obj.u_pea_no == pea_no;
        });
        console.log(1);
        resolve(ds[0]);
      }
      else {
        console.log(2);
        resolve('ไม่พบใบสั่งงาน');
      }
    })
    .catch((error) => {
      console.log(3);
      resolve(error);
    });
  })
}

const locationOptions = {
  enableHighAccuracy: true,
  timeout: 5000,
  maximumAge: 0
}

function locationReadSuccess(pos) {
  const crd = pos.coords;
  $('#i-lat').html(`${crd.latitude.toFixed(6)}`);
  $('#i-long').html(`${crd.longitude.toFixed(6)}`);
}

function locationReadError(err) {
  console.warn(`ERROR(${err.code}): ${err.message}`);
}

function peaScan() {
  let camId = localStorage.getItem('cameraId');

  if(camId == "" || camId == undefined) {
    $('#select-side').val('pea');
    Html5Qrcode.getCameras().then(devices => {
      if(devices && devices.length) {
        let source = $('#cameras-list-template').html();
        let output = $('#cameras-list');

        render(source, devices, output);
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
  else
  {
    $('#cam').removeClass('hide');
    $('#btn-u-scan').addClass('hide');
    $('#btn-pea-stop').removeClass('hide');
    $('#space').addClass('hide');

    scanner.start({deviceId: {exact: camId}}, config, (decodedText, decodedResult) => {
      stopScan('pea');

      $('#pea-no').val(decodedText);
    });
  }
}

function iSerialScan() {
  let result = $('#scan-result').val();
  $('#i-serial-code').val(result);

}

function uSerialScan() {
  let result = $('#scan-result').val();
  $('#u-serial-code').val(result);
}

function iPeaScan() {
  let result = $('#scan-result').val();
  $('#i-pea-no').val(result);

  setTimeout(() => {
    getPeaNo();
  }, 500);
}

function getPeaNo() {
  let peaNo = $('#i-pea-no').val();

  if(peaNo.length) {
    $('#pea-search').addClass('hide');
    $('#pea-clear').removeClass('hide');
  }

  localforage.getItem('inventory')
  .then((result) => {
    if(result != null && result != undefined) {
      if(peaNo.length) {
        let keys = ['peaNo'];
        var ds = result.filter((obj) => keys.some((key) => obj[key].includes(peaNo)));
      }
      else {
        var ds = result;
      }

      if(ds.length == 1) {
        updatePeaNo(ds[0]);
        return;
      }

      if(ds.length > 1) {
        showMeterList(ds);
        return;
      }

      if(ds.length == 0) {
        swal({
          title:'Oops!',
          text: "ไม่พบ PEA NO",
          type:"info"
        });

        return;
      }
    }
    else {
      swal({
        title:'Oops!',
        text: "ไม่พบ PEA NO",
        type:"info"
      });

      return;
    }
  });
}

function getSerial() {
  let serial = $('#i-serial-code').val();

  if(serial.length) {
    toggleIcon('clear', 'serial');

    localforage.getItem('inventory')
    .then((result) => {
      if(result != null && result != undefined) {
        let keys = ['serial'];
        ds = result.filter((obj) => keys.some((key) => obj[key].includes(serial)));

        if(ds.length == 1) {
          updatePeaNo(ds[0]);
          return;
        }

        if(ds.length > 1) {
          showMeterList(ds);
          return;
        }

        if(ds.length == 0) {
          swal({
            title:'Oops!',
            text: "ไม่พบ PEA NO ที่สแกน",
            type:"info"
          });

          return;
        }
      }
      else {
        swal({
          title:'Oops!',
          text: "ไม่พบ PEA NO ที่สแกน",
          type:"info"
        });

        return;
      }
    });
  }
}

function updatePeaNo(ds) {
  if(ds.peaNo != undefined) {
    $('#i-pea-no').val(ds.peaNo);
    $('#i-pea').val(ds.peaNo);
    $('#i-serial-code').val(ds.serial);
    $('#i-serial').val(ds.serial);
    $('#from-doc').val(ds.docnum);
    $('#item-code').val(ds.code);
    $('#item-name').val(ds.name);
    $('#is-clear-alarm').val(0);
    $('#select-clear-alarm').val(0);

    $('#pea-search').addClass('hide');
    $('#pea-clear').removeClass('hide');
    $('#serial-search').addClass('hide');
    $('#serial-clear').removeClass('hide');
    let txt = `<p>Item Code: ${ds.code}</p><p>Description: ${ds.name}</p><p>Serial: ${ds.serial}</p>`;
    $('#i-result').html(txt);
  }
  else {
    $('#i-result').text('ไม่พบมิเตอร์');
  }
}

function clearPeaNo() {
  $('#i-pea-no').val('');
  $('#i-pea').val('');
  $('#i-serial').val('');
  $('#i-serial-code').val('');
  $('#from-doc').val('');
  $('#item-code').val('');
  $('#item-name').val('');
  $('#pea-clear').addClass('hide');
  $('#pea-search').removeClass('hide');
  $('#serial-clear').addClass('hide');
  $('#serial-search').removeClass('hide');
  $('#i-result').text("ข้อมูลมิเตอร์ใหม่");
  $('#i-pea-no').focus();
}

function clearSerial() {
  $('#i-pea-no').val('');
  $('#i-pea').val('');
  $('#i-serial').val('');
  $('#i-serial-code').val('');
  $('#from-doc').val('');
  $('#item-code').val('');
  $('#item-name').val('');
  $('#pea-clear').addClass('hide');
  $('#pea-search').removeClass('hide');
  $('#serial-clear').addClass('hide');
  $('#serial-search').removeClass('hide');
  $('#i-result').text("ข้อมูลมิเตอร์ใหม่");
  $('#i-pea-no').focus();
}

function showMeterList(ds) {
  let source = $('#meter-list-template').html();
  let output = $('#meter-list');
  render(source, ds, output);
  $('#meter-modal').modal('show');
}

async function showAllMeterList() {
  const data = await getMeterList();
  let source = $('#meter-list-template').html();
  let output = $('#meter-list');
  await render(source, data, output);
  $('#meter-modal').modal('show');
}

function getMeterList() {
  return new Promise((resolve, reject) => {
    localforage.getItem('inventory')
    .then((result) => {
      if(result != null && result != undefined) {
        resolve(result);
      }
      else {
        resolve({});
      }
    })
  })
}

function getMeterData(peaNo) {
  return new Promise((resolve, reject) => {
    let ds = [];
    localforage.getItem('inventory')
    .then((result) => {
      if(result !== null && result !== undefined) {
        ds = result.filter((obj) => { return obj.peaNo == peaNo});
        resolve(ds[0]);
      }
      else {
        resolve(ds)
      }
    })
  })
}

async function selected(peaNo) {
  const data = await getMeterData(peaNo);
  updatePeaNo(data);
  $('#meter-modal').modal('hide');
}

function showConfirmResetAlarm() {
  let el = $('#select-clear-alarm');
  let is = $('#is-clear-alarm');
  let btn = $('#btn-take-photo-i');

  if(el.val() == 0) {
    is.val(0);
    el.addClass('has-error');
    btn.attr('disabled', 'disabled');
    return false;
  }

  if(el.val() == 1 && is.val() == 0) {
    swal({
      title:"ยืนยัน",
      text:"ยืนยันว่าคุณได้ทำการ Reset Alarm มิเตอร์ที่ติดตั้งแล้วใช่หรือไม่ ?",
      type:'info',
      showCancelButton:true,
      confirmButtonText:"ยืนยัน",
      cancelButtonText:"ยกเลิก",
      closeOnConfirm:true
    }, function() {
      is.val(1);
      el.removeClass('has-error');
      btn.removeAttr('disabled');
    });
  }
}

function takePhoto(side) {
	$('#'+side+'-photo').click();
}

function getExif(name) {
	var img = document.getElementById(name);
  //console.log(img);
	EXIF.getData(img, function () {
		var MetaData = EXIF.getAllTags(this);
    //console.log(MetaData);
    return JSON.stringify(MetaData, null, "\t");
		//console.log(JSON.stringify(MetaData, null, "\t"));
	});
}

function readURL(input, side) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#'+side+'-preview').html('<img id="'+side+'-image" src="'+e.target.result+'" style="width:100%; border-radius:10px;" alt="Item image" />');
      $('#'+side+'-blob').val(e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
    $('#'+side+'-preview').removeClass('hide');
    $('#'+side+'-photo-btn').addClass('hide');
    $('#del-'+side+'-image').removeClass('hide');
  }
}

function readImageASBlob(input) {
  let result = "";
  if(input.files && input.files[0]) {
    let reader = new FileReader();

    reader.onload = (e) => {
      result = e.target.result;
    }

    reader.readerAsDataURL(input.files[0]);
  }

  return result;
}

function removeImage(side){
	$("#del-"+side+"-image").addClass('hide');
	$("#"+side+"-photo").val('');
  $('#'+side+'-blob').val('');
  $('#'+side+'-photo-btn').removeClass('hide');
  $("#"+side+"-preview").html('');
  $('#'+side+'-preview').addClass('hide');

  if(side == 'i') {
    let is_clear_alarm = $('#is-clear-alarm').val();

    if(is_clear_alarm == 1) {
      $('#btn-take-photo-i').removeAttr('disabled');
    }
    else {
      $('#btn-take-photo-i').attr('disabled', 'disabled');
    }
  }
}

function saveUpdate() {
  let data = {};
  data.u_pea_no = $('#u-pea-no').val();
  data.route = $('#cust-route').val();
  data.use_age = $('#use-age').val();
  data.u_power_no = $('#u-power-no').val();
  data.damage_id = $('#u-dispose-id').val();
  data.u_image = $('#u-blob').val();
  data.u_orientation = $('#u-orientation').val();
  data.u_lat = $('#u-lat').text();
  data.u_lng = $('#u-long').text();

  data.i_pea_no = $('#i-pea-no').val();
  data.i_pea = $('#i-pea').val();
  data.c_pea = $('#c-pea').val();
  data.i_power_no = $('#i-power-no').val();
  data.phase = $('#phase-selected').val();
  data.i_lat = $('#i-lat').text();
  data.i_lng = $('#i-long').text();
  data.remark = $.trim($('#remark').val());
  data.i_image = $('#i-blob').val();
  data.i_orientation = $('#i-orientation').val();

  data.itemCode = $('#item-code').val();
  data.itemName = $('#item-name').val();
  data.i_serial = $('#i-serial').val();
  data.fromDoc = $('#from-doc').val();

  data.clear_alarm = $('#select-clear-alarm').val();
  data.is_clear_alarm =$('#is-clear-alarm').val();

  data.sign_status = $('#sign-status').val();
  data.signature_image = $('#signature').val();


  if(data.u_power_no.length != 5) {
    $('#u-power-no').addClass('has-error').focus();
    swal({
      title:"ข้อผิดพลาด",
      text:"หน่วยตัดกลับไม่ถูกต้อง",
      type:"warning"
    });

    return false;
  }
  else {
    $('#u-power-no').removeClass('has-error');
  }

  if(data.u_image.length < 10) {
    swal({
      title:"ข้อผิดพลาด",
      text:"กรุณาถ่ายรูปมิเตอร์เก่าที่ถอดแล้ว",
      type:"warning"
    });
    return false;
  }

  if(data.i_pea_no.length < 2 || data.i_pea != data.i_pea_no) {
    $('#i-pea-no').addClass('has-error');
    $('#i-pea-no').focus();
    swal({
      title:"ข้อผิดพลาด",
      text:"PEA NO ของมิเตอร์ใหม่ไม่ถูกต้อง",
      type:"warning"
    });
    return false;
  }
  else {
    $('#i-pea-no').removeClass('has-error');
  }

  if(data.i_power_no.length != 5) {
    $('#i-power-no').addClass('has-error').focus();
    swal({
      title:"ข้อผิดพลาด",
      text:"หน่วยตั้งต้นไม่ถูกต้อง",
      type:"warning"
    });
    return false;
  }
  else {
    $('#i-power-no').removeClass('has-error');
  }

  if(data.is_clear_alarm !== '1') {
    $('#select-clear-alarm').addClass('has-error').focus();
    swal({
      title:"ข้อผิดพลาด",
      text:"ยังไม่ได้ Reset Alarm",
      type:"warning"
    });
    return false;
  }
  else {
    $('#select-clear-alarm').removeClass('has-error');
  }

  if(data.i_image.length < 10) {
    swal({
      title:"ข้อผิดพลาด",
      text:"กรุณาถ่ายรูปมิเตอร์ใหม่",
      type:"warning"
    });

    return false;
  }

  if(data.sign_status !== '0' && data.sign_status !== '1' && data.sign_status !== '2') {
    swal({
      title:"ข้อผิดพลาด",
      text:"กรุณาเลือกสถานะผู้ใช้ไฟ<br/>รับทราบ หรือ ไม่อยู่ หรือ ไม่ลงนาม",
      type:'warning',
      html:true
    });

    return false;
  }

  if(data.sign_status === '0' && data.signature_image.length < 10) {
    swal({
      title:"ข้อผิดพลาด",
      text:"กรุณาลงลายมือชื่อ",
      type:"warning"
    });
    return false;
  }

  updateTransferOffline(data);
}

function goBack(){
  setTimeout(() => {
    window.location.href = "temp.html";
  }, 1200);
}

function updateTransferOffline(data) {
  let ds = [];
  load_in();
  localforage.getItem('transfers')
  .then((result) => {
    if(result != null && result != undefined) {
      ds = result.filter((obj) => {
        return obj.u_pea_no != data.u_pea_no;
      });
    }
      ds.push(data);
  })
  .then(() => {
    localforage.setItem('transfers', ds)
    .then(() => {
      updateMeterStatus(data.c_pea, data.i_pea);
    })
    .then(() => {
      load_out();
      swal({
        title:'Success!',
        type:'success',
        timer:1000
      });

      setTimeout(() => {
        window.location.href = "temp.html";
      }, 1200);
    })
    .catch((err) => {
      load_out();
      swal({
        title:'Error!',
        text:err,
        type:'error',
        html:true
      });
    });
  })
  .catch(error => {
    load_out();
    swal({
      title:'Error!',
      text:error,
      type:'error',
      html:true
    });
  });
}

function updateMeterStatus(c_pea, i_pea) {
  return new Promise((resolve, reject) => {
    //--- ถ้ามีการเปลี่ยนมิเตอร์
    if(c_pea != i_pea) {
      //----
      localforage.getItem('inventory')
      .then((res) => {
        if(res !== null && res !== undefined) {
          res.forEach((item, i) => {
            if(item.peaNo == i_pea) {
              res[i].status = "I";
            }

            if(item.peaNo == c_pea) {
              res[i].status = "P";
            }
          }); //-- foreach

          localforage.setItem('inventory', res);
        }

        resolve('ok');
      })
    }
    else {
      resolve('noneedtoupdate');
    }
  });
}

function cancleJob() {
  const u_pea_no = localStorage.getItem('offline_u_pea_no');
  const i_pea_no = $('#c-pea').val();

  swal({
    title:'ยกเลิกการติดตั้ง',
    text:`ต้องการยกเลิกการติดตั้งใบงาน ${u_pea_no} นี้ใช่หรือไม่ ?`,
    type:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d15b47',
    confirmButtonText:'ใช่',
    cancelButtonText:'ไม่ใช่',
    closeOnConfirm:true
  }, function() {

    if( u_pea_no.length && i_pea_no.length ) {
      localforage.getItem('work_list')
      .then((result) => {
        //-- change status of work_list
        if(result !== null && result !== undefined && result.length) {
          result.forEach((item, i) => {
            if(item.pea_no == u_pea_no) {
              result[i].status = "P";
            }
          });

          localforage.setItem('work_list', result);
        }
      })
      .then(() => {
        //--- change meter status
        localforage.getItem('inventory')
        .then((meters) => {
          if(meters !== null && meters !== undefined && meters.length) {
            meters.forEach((item, i) => {
              if(item.peaNo == i_pea_no) {
                meters[i].status = "P";
              }
            });

            localforage.setItem('inventory', meters);
          }
        })
      })
      .then(() => {
        localforage.getItem('transfers')
        .then((rows) => {
          let ds = [];
          if(rows !== null && rows !== undefined && rows.length) {
            ds = rows.filter((row) => {
              return row.u_pea_no != u_pea_no;
            });

            if(ds.length == 0) {
              localforage.removeItem('transfers');
            }
            else {
              localforage.setItem('transfers', ds);
            }
          }
        })
      })
      .then(() => {
        setTimeout(() => {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          goBack();
        }, 200);
      })
      //--- remove transfer
    }
    else {
      setTimeout(() => {
        swal({
          title:"Error !",
          text: 'ไม่พลเลขที่ใบสั่งงาน',
          type:'error'
        });
      }, 200);
    }
  });

}

function toggleIcon(option, name) {
  if(option == 'search') {
    $('#'+name+'-clear').addClass('hide');
    $('#'+name+'-search').removeClass('hide');
    return;
  }
  else {
    $('#'+name+'-search').addClass('hide');
    $('#'+name+'-clear').removeClass('hide');
    return;
  }
}

$("#u-photo").change(function(){
  if($(this).val() != '')
  {
    var file 		= this.files[0];
    var name		= file.name;
    var type 		= file.type;
    var size		= file.size;
    if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
    {
      swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
      $(this).val('');
      return false;
    }

    readURL(this, 'u');

    setTimeout(() => {
      var img = document.getElementById("u-image");
      EXIF.getData(img, function () {
        let orientation = EXIF.getTag(this, "Orientation");
        $('#u-orientation').val(orientation);
      });
    }, 1000);
  }
});

$("#i-photo").change(function(){
  if($(this).val() != '')
  {
    let file 		= this.files[0];
    let name		= file.name;
    let type 		= file.type;
    let size		= file.size;

    if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
    {
      swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
      $(this).val('');
      return false;
    }

    readURL(this, 'i');

    setTimeout(() => {
      let img = document.getElementById("i-image");
      EXIF.getData(img, function () {
        let orientation = EXIF.getTag(this, "Orientation");
        $('#i-orientation').val(orientation);
      });
    }, 1000);
  }
});

$('#u-power-no').on('input', function() {
  if($(this).val().length > 5) {
    $(this).val($(this).val().slice(0, 5));
  }
});

$('#i-power-no').on('input', function() {
  if($(this).val().length > 5) {
    $(this).val($(this).val().slice(0, 5));
  }
});

$('#i-pea-no').keyup((e) => {
  toggleIcon('search', 'pea');

  if(e.keyCode === 13) {
    getPeaNo();
  }
});

$('#i-serial-code').keyup((e) => {
  toggleIcon('search', 'serial');

  if(e.keyCode === 13) {
    getSerial();
  }
});
