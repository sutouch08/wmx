window.addEventListener('load', function() {
  let pea_no = localStorage.getItem('inform_no');

  if(pea_no !== null && pea_no !== undefined && pea_no !== "") {
    $('#pea_no').val(pea_no);
    getData(pea_no);
  }
  else {
    $('#pea_no').focus();
  }
});

function getData(pea_no) {
  var pea_no = pea_no === undefined ? $('#pea_no').val() : pea_no;

  if(pea_no !== null && pea_no !== undefined && pea_no !== "") {
    localforage.getItem('work_list')
    .then((data) => {
      if(data !== null && data !== undefined && data.length) {
        var ds = data.filter((obj) => {
          return obj.pea_no === pea_no && obj.status === "P";
        });

        if(ds.length) {
          let rs = ds[0];

          for(const key in rs) {
            $('#'+key).val(rs[key]);
          }

          navigator.geolocation.getCurrentPosition(locationReadSuccess, locationReadError, locationOptions);
          $('#pea_no').attr('disabled', 'disabled');
          $('.i').removeClass('hide');
          $('#btn-ok').addClass('hide');
          $('#btn-change').removeClass('hide');
        }
        else {
          swal({
            title:'ไม่พบใบสั่งงาน',
            text:'ไม่พบใบสั่งงานตาม PEA NO ที่ระบุ PEA NO อาจไม่ถูกต้อง หรือ ใบสั่งงานอาจถูกดำเนินการไปแล้ว หรือ คุณไม่ได้รับมอบหมายให้จัดการใบสั่งงานนี้',
            type:'warning'
          });

          $('.i').addClass('hide');
        }
      }
    })

  }
  else {
    swal("กรุณาระบุ PEA NO");
    $('.i').addClass('hide');
    return false;
  }

}


function clearData() {
  $('.v').val('');
  $('.i').addClass('hide');
  $('#btn-change').addClass('hide');
  $('#btn-ok').removeClass('hide');
  $('#pea_no').removeAttr('disabled').focus();
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
}

function saveInform() {
  let data = {};
  var pea_no = $('#pea_no').val();

  localforage.getItem('work_list')
  .then((result) => {
    if(result !== null && result !== undefined && result.length) {
      let ds = result.filter((obj) => {
        return obj.pea_no == pea_no && obj.status == "P";
      });

      if(ds.length) {
        data = ds[0];
        data.u_pea_no = pea_no;
        data.u_image = $('#u-blob').val();
        data.u_orientation = $('#u-orientation').val();
        data.lat = $('#i-lat').text();
        data.lng = $('#i-long').text();
        data.i_image = $('#i-blob').val();
        data.i_orientation = $('#i-orientation').val();
        data.remark = $.trim($('#remark').val());
        data.type = "inform";
      }
    }
  })
  .then(() => {
    if(data.hasOwnProperty('pea_no')) {
      if(data.pea_no.length < 5) {
        swal({
          title:"ข้อผิดพลาด",
          text:"PEA NO ไม่ถูกต้อง กรุณาตรวจสอบ",
          type:"warning"
        });
        return false;
      }

      if(data.u_image.length < 10) {
        swal({
          title:"ข้อผิดพลาด",
          text:"กรุณาถ่ายรูปมิเตอร์ด้านหน้า",
          type:"warning"
        });
        return false;
      }

      if(data.i_image.length < 10) {
        swal({
          title:"ข้อผิดพลาด",
          text:"กรุณาถ่ายรูปมิเตอร์ด้านข้าง",
          type:"warning"
        });
        return false;
      }

      if(data.remark.length < 3) {
        swal({
          title:"ข้อผิดพลาด",
          text:"กรุณาระบุสาเหตุหรือข้อมูลเพิ่มเติม",
          type:"warning"
        });
        return false;
      }

      if(navigator.onLine) {
        addInform(data);
      }
      else {
        addInformOffline(data);
      }
    }
    else {
      swal({
        title:'ไม่พบใบสั่งงาน',
        text:'ไม่พบใบสั่งงานตาม PEA NO ที่ระบุ PEA NO อาจไม่ถูกต้อง หรือ ใบสั่งงานอาจถูกดำเนินการไปแล้ว หรือ คุณไม่ได้รับมอบหมายให้จัดการใบสั่งงานนี้',
        type:'error'
      });
    }
  })
}


function addInform(data) {
  if(navigator.onLine) {
    let json = JSON.stringify(data);
    let requestUri = URI + 'add_inform';
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

    fetch(requestUri, requestOptions)
    .then(response => response.text())
    .then((result) => {
      var ds = JSON.parse(result);

      if(ds.status === 'success') {
        if(ds.ex == 0) {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.href = "work_list.html";
          }, 1200)
        }
        else {
          swal({
            title:'',
            text:ds.message,
            type:'error'
          }, function() {
            window.location.href = "work_list.html";
          })
        }
      }
      else {
        swal({
          title:'Error!',
          text:ds.message,
          type:'error'
        })
      }
    });
  }
  else {
    addInformOffline(data);
  }
}


function addInformOffline(data) {
  localforage.getItem('transfers')
  .then((result) => {
    var ds = [];

    if(result !== null && result !== undefined && result.length) {
      ds = result.filter((res) => {
        return res.u_pea_no != data.pea_no;
      });
    }

    ds.push(data);

    localforage.setItem('transfers', ds)
    .then(() => {
      localforage.getItem('work_list')
      .then((worklist) => {
        if(worklist !== null && worklist !== undefined && worklist.length) {
          worklist.forEach((item, i) => {
            if(item.pea_no == data.pea_no) {
              worklist[i].status = "I";
            }
          });

          localforage.setItem('work_list', worklist);
        }
      })
      .then(() => {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.href = "work_list.html";
        }, 1200);
      })
    })
  })
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
