window.addEventListener('load', () => {
  let uuid = get_uuid();

  if(uuid == "" || uuid == null || uuid == undefined) {
    uid = generateUID();

		localStorage.setItem('wms_uuid', uid);
  }
});


function get_uuid() {
	return localStorage.getItem('wms_uuid');
}


function showError(response) {
  load_out();

  setTimeout(() => {
    swal({
      title:'Error!',
      text:(typeof response === 'object') ? response.responseText : response,
      type:'error',
      html:true
    })
  }, 100);
}


//--- save side bar layout to cookie
function toggle_layout(){
	var sidebar_layout = getCookie('sidebar_layout');
	if(sidebar_layout == 'menu-min'){
		setCookie('sidebar_layout', '', 90);
	}else{
		setCookie('sidebar_layout', 'menu-min', 90);
	}
}


function load_in(){
	$("#loader").css("display","block");
	$('#loader-backdrop').css('display', 'block');
	$("#loader").animate({opacity:0.8},300);
}


function load_out(){
	$("#loader").animate({
		opacity:0
	},300,
	function() {
		$("#loader").css("display","none");
		$('#loader-backdrop').css('display', 'none');
	});
}


function isDate(txtDate) {
  var currVal = txtDate;

  if(currVal == '') {
    return false;
  }

  //Declare Regex
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?
  if (dtArray == null) {
    return false;
  }

  //Checks for mm/dd/yyyy format.
  dtDay= dtArray[1];
  dtMonth = dtArray[3];
  dtYear = dtArray[5];
  if (dtMonth < 1 || dtMonth > 12) {
    return false;
  }
  else if (dtDay < 1 || dtDay> 31) {
    return false;
  }
  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) {
    return false;
  }
  else if (dtMonth == 2) {
    var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
    if (dtDay> 29 || (dtDay ==29 && !isleap)){
      return false;
    }
  }

  return true;
}


function getCurrentDate() {
  let date = new Date();
  let dd = date.getDate();
  let mm = date.getMonth()+1;
  let yy = date.getFullYear();

  dd = dd < 10 ? "0"+dd : dd;
  mm = mm < 10 ? "0"+mm : mm;

  return `${dd}-${mm}-${yy}`;
}


function removeCommas(str) {
  while (str.search(",") >= 0) {
    str = (str + "").replace(',', '');
  }
  return str;
}


function addCommas(number) {
  return (number.toString())
  .replace(/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) {
    var reverseString = function(string) { return string.split('').reverse().join(''); };
    var insertCommas  = function(string) {
      var reversed   = reverseString(string);
      var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
      return reverseString(reversedWithCommas);
    };
    return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
  });
}


function render(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	output.html(html);
}


function render_prepend(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	output.prepend(html);
}


function render_append(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	output.append(html);
}


function render_after(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertAfter(output);
}

function render_before(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertBefore(output);
}


function set_rows() {
	var rows = $('#set_rows').val();
	setCookie('rows', rows);
}


$('#set_rows').keyup(function(e) {
	if(e.keyCode == 13 && $(this).val() > 0) {
		set_rows();
	}
});


function reIndex(className) {
  className = className === undefined ? 'no' : className;

  $('.'+className).each(function(index, el) {
    no = index +1;
    $(this).text(addCommas(no));
  });
}


var downloadTimer;

function get_download(token) {
	load_in();
	downloadTimer = window.setInterval(function(){
		var cookie = getCookie("file_download_token");
		if(cookie == token)
		{
			finished_download();
		}
	}, 1000);
}


function finished_download() {
	window.clearInterval(downloadTimer);
	deleteCookie("file_down_load_token");
	load_out();
}


function isJson(str) {
  try {
    JSON.parse(str);
  }
  catch(e){
    return false;
  }
  return true;
}


function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}


function deleteCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


function parseDefault(value, def){
	if(isNaN(value)){
		return def; //--- return default value
	}

	return value;
}


$('.filter').change(function() {
  getSearch();
})


function getSearch() {
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
	if(e.keyCode === 13) {
		getSearch();
	}
});


function goBack() {
	window.location.href = HOME;
}


function clearFilter() {
	let url = HOME + 'clear_filter';
	$.get(url, function(rs){ goBack(); });
}


function generateUID() {
  return Math.random().toString(36).substring(2, 15) +
  Math.random().toString(36).substring(2, 15);
}


function validCode(input){
  var regex = /[^a-z0-9-_]+/gi;
  input.value = input.value.replace(regex, '');
}


function closeModal(name) {
  $('#'+name).modal('hide');
}


$.fn.hasError = function(msg) {
  name = this.attr('id');

  if(msg !== undefined) {
    $('#'+name+'-error').text(msg);
  }

  return this.addClass('has-error');
};


$.fn.clearError = function() {
  name = this.attr('id');
  $('#'+name+'-error').text('');
  return this.removeClass('has-error');
};


function clearErrorByClass(className) {
  $('.'+className).each(function() {
    let name = $(this).attr('id');
    $('#'+name+'-error').text('');
    $(this).removeClass('has-error');
  })
}
