function validateNewUserForm(field) {
	var input = document.getElementById(field).value;
	if (input.length==0) {
		document.getElementById('valid_'+field).setAttribute('class', 'red');
		document.getElementById('valid_'+field).innerHTML = '\u2717';
		return;
	} else if (field=='password') {
		if (input.length<8) {
			document.getElementById('valid_'+field).setAttribute('class', 'red');
			document.getElementById('valid_'+field).innerHTML = '\u2717';		
			return;
		}
	} else if (field=='password_again') {
		if (input.length<8 || input!=document.getElementById('password').value) {
			document.getElementById('valid_'+field).setAttribute('class', 'red');
			document.getElementById('valid_'+field).innerHTML = '\u2717';		
			return;
		}
	} else if (field=='username_again') {
		if (input!=document.getElementById('username').value) {
			document.getElementById('valid_'+field).setAttribute('class', 'red');
			document.getElementById('valid_'+field).innerHTML = '\u2717';		
			return;
		}
	}
	document.getElementById('valid_'+field).setAttribute('class', 'green');
	document.getElementById('valid_'+field).innerHTML = '\u2713';		
}