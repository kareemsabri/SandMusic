function facebookLogin() {
	new Ajax.Request('/users/facebook_login', {
	  method: 'post',
	  onSuccess: function(transport) {
	  	if (transport.responseText!='0') {
	  		window.location.reload();
	  	}
	  }
	});
}

function facebookPermsCheck() {
	/*new Ajax.Request('/users/get_fb_id', {
	  method: 'post',
	  onSuccess: function(transport) {
	  	if (transport.responseText!='0') {
	  		var fb_id = transport.responseText;
	  	}
	  }
	});*/
	FB.api(
		{
		method: 'users.hasAppPermission',
		ext_perm: 'publish_stream'
		},
		function(response) {
	  		if (response=='0') {
				FB.login(function(response) {
					if (response.session) {
						if (response.perms) {
				    	} else {
				    		document.getElementById('fb_share').checked = false;
				    		alert('You must grant permission to publish to your facebook wall in order to use this feature.');
				    	}
				  	} else {
				  		window.location = '/';
				  	}
				}, {perms:'publish_stream'});	  			
	  		}
	  	}
	);
}