function getComments(song_id) {
	new Ajax.Request("/comments/get_comments/"+song_id+"/update:true", {
		evalScripts:true,
		evalJSON:'force',
		onComplete:function (transport) {
			var i = 0;
			var l = transport.responseJSON.length;
			var el = document.getElementById('comments');
			el.innerHTML = '';
			while (i<l) {
				el.innerHTML = el.innerHTML + '<div class="comments_top">'+transport.responseJSON[i].User.first_name+' '+transport.responseJSON[i].User.last_name+' WROTE</div>';
				el.innerHTML = el.innerHTML + '<div class="comment_middle"><span class="tweet_date">'+transport.responseJSON[i].Comment.created+'</span><br/>'+transport.responseJSON[i].Comment.comment+'</div>';
				el.innerHTML = el.innerHTML + '<div class="comment_bottom"></div>';
				i++
			}
		}
	});
}