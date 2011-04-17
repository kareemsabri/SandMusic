function goToPlaylistMode(playlist_id, slideUp) {
	if (slideUp!==undefined) {
		Effect.SlideUp('hidden_playlist_edit');
	}
	new Ajax.Updater("content", "/songs/index/edit_playlist:"+playlist_id, {
		evalScripts:true,
		onCreate:function (transport) {
			new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});
		},
		onComplete:function (transport) {
			setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);
			YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
		}
	});
}

function addNewSongToPlaylist(playlist_id,song_id) {
	new Ajax.Request('/playlists/add_song/playlist:'+playlist_id+'/song:'+song_id, {
	  method: 'post',
	  onSuccess: function(transport) {
	  	document.getElementById('add_'+song_id).innerHTML = '\u2717';
	  	document.getElementById('add_'+song_id).setAttribute('class', 'red');
	  	document.getElementById('add_'+song_id).setAttribute('onclick', 'removeSongFromPlaylist('+playlist_id+','+song_id+');return false;');
	  	document.getElementById('add_'+song_id).setAttribute('title', 'Remove from Playlist');
	  }
	});
}

function removeSongFromPlaylist(playlist_id,song_id) {
	new Ajax.Request('/playlists/remove_song/playlist:'+playlist_id+'/song:'+song_id, {
	  method: 'post',
	  onSuccess: function(transport) {
	  	document.getElementById('add_'+song_id).innerHTML = '+';
	  	document.getElementById('add_'+song_id).setAttribute('class', 'add');
	  	document.getElementById('add_'+song_id).setAttribute('onclick', 'addNewSongToPlaylist('+playlist_id+','+song_id+');return false;');
	  	document.getElementById('add_'+song_id).setAttribute('title', 'Add to Playlist');
	  }
	});
}

function deletePlaylist(playlist_id) {
	new Ajax.Request('/playlists/delete/'+playlist_id, {
	  method: 'post',
	  onSuccess: function(transport) {
	  	Effect.SlideUp('playlist_'+playlist_id);
	  	var select = document.getElementById('playlist_filter');
	  	var len = select.options.length;
	  	for(i=0; i<len; i++) {
	  		if (select.options[i].value == playlist_id) {
	  			select.options[i].remove();
	  			break;
	  		}
	  	}
	  }
	});
}

function getPlaylists(user_id) {
	new Ajax.Request("/playlists/get_playlists_by_user_id/"+user_id, {
		evalScripts:true,
		evalJSON:'force',
		onComplete:function (transport) {
			var len = transport.responseJSON.length;
			document.getElementById('playlist_filter').innerHTML = '<option value="0">Playlist</option>';
			document.getElementById('playlist_edit_list').innerHTML = '';
			for(i=0; i<len; i++) {
				id = transport.responseJSON[i]['Playlist']['id'];
				name = transport.responseJSON[i]['Playlist']['name'];
				document.getElementById('playlist_filter').innerHTML+='<option value="'+transport.responseJSON[i]['Playlist']['id']+'">'+transport.responseJSON[i]['Playlist']['name']+'</option>';
				document.getElementById('playlist_edit_list').innerHTML+='<div id="playlist_'+id+'"><div class="grid_3"><a href="#" onclick="goToPlaylistMode('+id+',true);return false;">'+name+'</a></div><div class="grid_1"><a href="#" title="Delete Playlist" onclick="deletePlaylist('+id+');return false;" class="red">\u2717</a></div></div><div class="clear"></div>';
			}
		}
	});
}