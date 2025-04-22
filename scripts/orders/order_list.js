
// window.addEventListener('load', () => {
//   let height = $(window).height();
// 	let pageContentHeight = height - 128;
// 	// header = 80, hr = 15, table margin = 10, footer 170, margin-bottom = 15
// 	let itemTableHeight = pageContentHeight - (112);
//
// 	$('.page-content').css('height', pageContentHeight + 'px');
// 	$('#order-table').css('height', itemTableHeight + 'px');
//
// })

$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});


$('#chk-all').change(function() {
	if($(this).is(':checked')) {
		$('.chk-wms').prop('checked', true);
	}
	else {
		$('.chk-wms').prop('checked', false);
	}
})
