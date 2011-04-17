function getLikes(song_id) {
	new Ajax.Request("/likes/get_likes/"+song_id, {
		evalScripts:true,
		evalJSON:'force',
		onComplete:function (transport) {
			var i = 0;
			var l = transport.responseJSON.length;
			var el = document.getElementById('comments');
			el.innerHTML = '';
			var code = '<div class="comments_top">LIKES</div><div class="comment_middle">';
			while (i<l) {
				code = code + transport.responseJSON[i].User.first_name+' '+transport.responseJSON[i].User.last_name+'<br/>';
				i++
			}
			code = code + '</div><div class="comment_bottom"></div>';
			el.innerHTML = code;
		}
	});
}