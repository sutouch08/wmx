
function toggleShowPwd(option) {
	console.log(option);
	if(option == 's') {
		$('.p-hide').addClass('hide');
		$('.p-show').removeClass('hide');
	}
	else {
		$('.p-show').addClass('hide');
		$('.p-hide').removeClass('hide');
	}
}

$('#cu-pwd').keyup(function(e) {
	$('#cu-pwd-alt').val($(this).val());
});

$('#cu-pwd-alt').keyup(function() {
	$('#cu-pwd').val($(this).val());
});

$('#pwd').keyup(function() {
	$('#pwd-alt').val($(this).val());
});

$('#pwd-alt').keyup(function() {
	$('#pwd').val($(this).val());
});

$('#cm-pwd').keyup(function() {
	$('#cm-pwd-alt').val($(this).val());
});

$('#cm-pwd-alt').keyup(function() {
	$('#cm-pwd').val($(this).val());
});


function validatePassword(input)
{

	if(useStrongPwd == 1) {
		var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;

		if(input.match(passw))
		{
			return true;
		}

		return false;
	}

	return true;
}


function changePassword() {	
	const current = $('#cu-pwd');
	const newPass = $('#pwd');
	const conPass = $('#cm-pwd');
	const cuErr = $('#cu-pwd-error');
	const pwdErr = $('#pwd-error');
	const cmErr = $('#cm-pwd-error');

	if(current.val().length === 0) {
		current.addClass('has-error');
		cuErr.text("กรุณาใส่รหัสผ่านปัจจุบัน");

		current.focus();
		return false;
	}
	else {
		current.removeClass('has-error');
		cuErr.html('&nbsp;');
	}

	if(newPass.val().length === 0) {
		newPass.addClass('has-error');
		pwdErr.html('กรุณากำหนดรหัสผ่าน');

		newPass.focus();
		return false;
	}
	else {
		newPass.removeClass('has-error');
		pwdErr.html('&nbsp;');
	}

	//--- check use same as current passsword ?
	if(newPass.val() === current.val()) {
		newPass.addClass('has-error');
		pwdErr.html("รหัสใหม่ต้องไม่ซ้ำกับรหัสปัจจุบัน");

		return false;
	}
	else {
		newPass.removeClass('has-error');
		pwdErr.html('&nbsp;');
	}

	//--- check complexity
	if( ! validatePassword(newPass.val())) {
		newPass.addClass('has-error');
		pwdErr.text('รหัสผ่านต้องมีความยาว 8 - 20 ตัวอักษร และต้องประกอบด้วย ตัวอักษรภาษาอังกฤษ พิมพ์เล็ก พิมพ์ใหญ่ และตัวเลขอย่างน้อย อย่างละตัว');

		newPass.focus();
		return false;
	}
	else {
		newPass.removeClass('has-error');
		pwdErr.html('&nbsp;');
	}


	if(newPass.val() !== conPass.val()) {
		conPass.addClass('has-error');
		cmErr.html('ยืนยันรหัสผ่านไม่ตรงกับรหัสผ่านใหม่');

		conPass.focus();
		return false;
	}
	else {
		conPass.removeClass('has-error');
		cmErr.html('&nbsp;');
	}

	let requestUri = URI + 'check_current_password';
	let header = new Headers();
	header.append('X-API-KEY', API_KEY);
	header.append('Authorization', AUTH);
	header.append('Content-type', 'application/json');
	let json = JSON.stringify({"uname" : uname, "pwd" : current.val()});
	let requestOptions = {
		method : 'POST',
		headers : header,
		body : json
	};

	fetch(requestUri, requestOptions)
	.then(response => response.text())
	.then(result => {
		if(isJson(result)) {
			let ds = JSON.parse(result);

			if(ds.result === 'valid') {
				let data = JSON.stringify({
					"uname" : uname,
					"pwd" : current.val(),
					"new_pwd" : newPass.val()
				});

				let uri = URI + 'change_pwd';
				let options = {
					method : 'POST',
					headers : header,
					body : data
				};

				fetch(uri, options)
				.then(res => res.text())
				.then(rs => {
					if( isJson(rs)) {
						cs = JSON.parse(rs);
						if(cs.result == 'success') {
							window.location.href = "changed.html";
						}
						else {
							swal({
								title:'Error!',
								text: cs.result,
								type:'error'
							});
						}
					}
				})
				.catch(error => {
					console.error('error', error);
				});
			}
			else if(ds.result == 'invalid') {
				current.addClass('has-error');
				cuErr.text('รหัสผ่านปัจจุบันไม่ถูกต้อง');
			}
			else {
				current.addClass('has-error');
				cuErr.text(ds.result);
			}
		}
	})
	.catch(error => {
		console.error('error', error);
	});
}
