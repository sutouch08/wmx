var center    = ($(document).width() - 800)/2;
var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";


function printAddress(order_code, id_sender)
{
	var target = BASE_URL + 'masters/address/print_address_sheet/'+order_code;
	window.open(target, "_blank", prop);
}
