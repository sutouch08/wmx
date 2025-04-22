$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty').val();
    doReceive();
  }
});


$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    load_invoice();
  }
})


//---- ยิงบาร์โค้ดเพื่อรับสินค้า
//---- 1. เช็คก่อนว่ามีรายการอยู่ในตารางหน้านี้หรือไม่ ถ้ามีเพิ่มจำนวน แล้วคำนวนยอดใหม่
//---- 2. ถ้าไม่มีรายการอยู่ เช็คสินค้าก่อนว่ามีในระบบหรือไม่
//---- 3. ถ้ามีในระบบ เพิ่มรายการเข้าตาราง
function doReceive()
{
  let barcode = $('#barcode').val();
  let qty = parseDefault(parseFloat($('#qty').val()), 1);

  if(barcode.length) {
    $('#barcode').attr('disabled', 'disabled');

    let bc = md5(barcode);

    if($('#'+bc).length == 0) {
      beep();
      swal({
        title: "บาร์โค้ดไม่ถูกต้อง",
        type:"error"
      }, function() {
        setTimeout(() => {
          $('#barcode').removeAttr('disabled').focus().select();
        })
      })

      return false;
    }

    $('.'+bc).each(function() {
      if(qty > 0) {
        let id = $(this).data('id');
        let rtQty = parseDefault(parseFloat(removeCommas($('#return-qty-'+id).val())), 0);
        let reQty = parseDefault(parseFloat($('#qty-'+id).val()), 0);

        if(rtQty > 0 && reQty <= rtQty) {
          diff = rtQty - reQty;

          if(diff <= qty) {
            reQty += diff;
            qty -= diff;
            $('#qty-'+id).val(reQty).change();
          }
          else {
            reQty += qty;
            qty = 0;
            $('#qty-'+id).val(reQty).change();
          }
        }
      }
    });

    if(qty > 0) {
      beep();

      swal({
        title:"Oops",
        text:"จำนวนเกิน " + qty + " ชิ้น",
        type:"error",
        html:true
      }, function() {
        setTimeout(() => {
          $('#barcode').removeAttr('disabled').focus();
        }, 100);
      })
    }

    $('#qty').val(1);
    $('#barcode').removeAttr('disabled').val('').focus();
  }
}
