function follow(followee_id) {
	new Ajax.Request('/follow/'+followee_id, {
		method: 'post',
		evalScripts:true,
		onComplete: function(transport) {
			var el = document.getElementById('follow');
			if (transport.responseText!=0) {
				el.setAttribute('onclick', 'unfollow(\''+followee_id+'\');return false;');
				el.innerHTML = 'Unfollow';
				getFollowers();
			} else {
				alert('An unknown error occurred.');
			}
		}
	});	
}

function unfollow(followee_id) {
	new Ajax.Request('/unfollow/'+followee_id, {
		evalScripts:true,
		onComplete: function(transport) {
			var el = document.getElementById('follow');
			if (transport.responseText!=0) {
				el.setAttribute('onclick', 'follow(\''+followee_id+'\');return false;');
				el.innerHTML = 'Follow';
				getFollowers();
			} else {
				alert('An unknown error occurred.');
			}
		}
	});	
}

function getFollowers() {
	new Ajax.Request('/get_followers', {
		evalScripts:true,
		evalJSON:'force',
		onComplete: function(transport) {
			var i = 0;
			var l = transport.responseJSON.length;
			if (transport.responseText!='0') {
				var el = document.getElementById('follows_filter');
				el.innerHTML = '<option value="-1">Follow</option><option value="0">All</option>';
				while(i<l) {
					el.innerHTML = el.innerHTML + '<option value="'+transport.responseJSON[i].User.id+'">'+transport.responseJSON[i].User.first_name+' '+transport.responseJSON[i].User.last_name+'</option>';
					i++;
				}
			}
		}
	});
}