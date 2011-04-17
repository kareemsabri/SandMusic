function getAlbums(artist_id, select_id) {
	document.getElementById(select_id).disabled = false;
	new Ajax.Request('/albums/view_by_artist/'+artist_id, {
	  method: 'post',
	  onSuccess: function(transport) {
	    document.getElementById(select_id).innerHTML = transport.responseText;
	  }
	});
}

function showHideForm(id, button_id, action, message) {
	if (action=='show') {
		Effect.SlideDown(id);
		document.getElementById(button_id).innerHTML = 'CANCEL';
		document.getElementById(button_id).setAttribute('onclick', 'showHideForm(\''+id+'\',\''+button_id+'\',\'hide\',\''+message+'\');return false;');
	} else if (action=='hide') {
		Effect.SlideUp(id);
		document.getElementById(button_id).innerHTML = message;
		document.getElementById(button_id).setAttribute('onclick', 'showHideForm(\''+id+'\',\''+button_id+'\',\'show\',\''+message+'\');return false;');
	}
}

function getSongs() {
	var jsRequest = new Ajax.Updater(
		"content",
		"/", {
			evalScripts:true,
			onComplete:function (transport) {
				YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true)
			}
		}
	);
}

function notEmpty(input_id, valid_id) {
	var name = document.getElementById(input_id).value;
	if (name=='') {
		document.getElementById(valid_id).setAttribute('class', 'red');
		document.getElementById(valid_id).innerHTML = '\u2717';
	} else {
		if (input_id!='link') {
			document.getElementById(valid_id).setAttribute('class', 'green');
			document.getElementById(valid_id).innerHTML = '\u2713';
		} else if( (name.match(".mp3") || name.match(".m4a")) && name.match("http://") ) {
			document.getElementById(valid_id).setAttribute('class', 'green');
			document.getElementById(valid_id).innerHTML = '\u2713';
		} else {
			document.getElementById(valid_id).setAttribute('class', 'red');
			document.getElementById(valid_id).innerHTML = '\u2717';			
		}
	}	
	return false;
}

function resetValid(valid_id) {
	document.getElementById(valid_id).setAttribute('class', 'red');
	document.getElementById(valid_id).innerHTML = '\u2717';
}

function deadSong(id) {
	new Ajax.Request('/songs/mark_as_dead/'+id, {
	  method: 'post',
	  onSuccess: function(transport) {
	  	Effect.SlideUp('song_row_'+id);
	  }
	});
}

function getMoreSongs() {
	var page = document.getElementById('next_page').innerHTML;
	var max = document.getElementById('page_count').innerHTML;
	if (page<=max) {
		new Ajax.Request('/songs/index/page:'+page+'?more=true', {
			evalScripts:true,
			method: 'post',
			onComplete: function(transport) {
				var el = document.getElementById('songs_table');
				el.innerHTML+=transport.responseText;
				document.getElementById('next_page').innerHTML++;
				YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
			}
		});
	}
}

function filter() {
	var playlist_filter = document.getElementById('playlist_filter').value;
	var artist_filter = document.getElementById('artist_filter').value;
	var follows_filter = document.getElementById('follows_filter').value;
	var genre_filter = document.getElementById('genre_filter').value;
	new Ajax.Updater("content", '/songs/index/page:1/playlist:'+playlist_filter+'/artist:'+artist_filter+'/genre:'+genre_filter+'/user:'+follows_filter, {
	  evalScripts:true,
	  method: 'post',
	  onCreate:function (transport) {new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});},
	  onComplete: function(transport) {
	    setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
	    initialize();
	  }
	});
}

function filterByArtist(id) {
	new Ajax.Updater("content", '/songs/index/page:1/artist:'+id, {
	  evalScripts:true,
	  method: 'post',
	  onCreate:function (transport) {new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});},
	  onComplete: function(transport) {
	    setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
	    initialize();
	  }
	});
}

function filterByAlbum(id) {
	new Ajax.Updater("content", '/songs/index/page:1/album:'+id, {
	  evalScripts:true,
	  method: 'post',
	  onCreate:function (transport) {new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});},
	  onComplete: function(transport) {
	    setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
	    initialize();
	  }
	});
}

function filterByUser(user_id) {
	new Ajax.Updater("content", '/songs/index/page:1/user:'+user_id, {
	  evalScripts:true,
	  method: 'post',
	  onCreate:function (transport) {new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});},
	  onComplete: function(transport) {
	    setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
	    initialize();
	  }
	});	
}

function likeSong(song_id) {
	new Ajax.Request('/like/'+song_id, {
		evalScripts:true,
		method: 'post',
		evalJSON: 'force',
		onComplete: function(transport) {
			var el = document.getElementById('like_'+song_id);
			if (transport.responseText!=0) {
				el.innerHTML = transport.responseJSON[0];
				if (transport.responseJSON[1]==1) {
					fbPublish(song_id,transport.responseJSON[2],transport.responseJSON[3]);
				}
			} else{
				alert('You\'ve already liked this song!');
			}
		}
	});
}

function fbPublish(song_id,song_name,artist_name) {
	var lnk = 'http://sandmusic.ca/songs/index/song:'+song_id;
	var msg = 'likes '+song_name+' by '+artist_name+' on SandMusic';
	FB.api('/me/links', 'post', { link: lnk, message: msg, picture: 'http://sandmusic.ca/img/sandmusic_logo.png' }, function(response) {});
}

function getTwitter(song_id) {
	new Ajax.Request("/songs/get_twitter_feed/"+song_id, {
		evalScripts:true,
		evalJSON:'force',
		onComplete:function (transport) {
			var i = 0;
			var el = document.getElementById('comments');
			var pos = -1;
			var end = -1;
			var link;
			var name;
			el.innerHTML = '';
			while (i<5) {
				//find any links
				pos = transport.responseJSON[i].text.search(/http:/i);
				var myLinks = new Array();
				var link_num = 0;
				while(pos!=-1) {
					end = pos+1;
					while(end<transport.responseJSON[i].text.length) {
						if (transport.responseJSON[i].text.charAt(end)==" ") {
							break;
						}
						end++;
					}
					myLinks[link_num] = transport.responseJSON[i].text.substr(pos,end-pos);
					transport.responseJSON[i].text = transport.responseJSON[i].text.replace(myLinks[link_num],'ah8934hf_'+link_num);
					pos = transport.responseJSON[i].text.search(/http:/i);
					link_num++;
				}
				
				//now replace links
				link_num=0;
				while(link_num<myLinks.length) {
					transport.responseJSON[i].text = transport.responseJSON[i].text.replace('ah8934hf_'+link_num,'<span class="blue"><a href="'+myLinks[link_num]+'" class="plain" target="_blank">'+myLinks[link_num]+'</a></span>');
					link_num++;
				} 
				
				//find any twitter users
				pos = transport.responseJSON[i].text.search(/\@/i);
				myLinks = new Array();
				var myNames = new Array();
				link_num = 0;
				while(pos!=-1) {
					end = pos+1;
					while(end<transport.responseJSON[i].text.length) {
						if (transport.responseJSON[i].text.charAt(end)==" ") {
							break;
						}
						end++;
					} 
					myLinks[link_num] = transport.responseJSON[i].text.substr(pos+1,end-pos-1);
					myNames[link_num] = transport.responseJSON[i].text.substr(pos,end-pos);
					transport.responseJSON[i].text = transport.responseJSON[i].text.replace(myNames[link_num],'ah8934hf_'+link_num);
					pos = transport.responseJSON[i].text.search(/\@/i);
					link_num++;
				}
				
				//now replace users
				link_num=0;
				while(link_num<myLinks.length) {
					transport.responseJSON[i].text = transport.responseJSON[i].text.replace('ah8934hf_'+link_num,'<span class="blue"><a href="http://twitter.com/'+myLinks[link_num]+'" class="plain" target="_blank">'+myNames[link_num]+'</a></span>');
					link_num++;
				}
				
				el.innerHTML = el.innerHTML + '<div class="comments_top">@'+transport.responseJSON[i].screen_name+' WROTE</div>';
				el.innerHTML = el.innerHTML + '<div class="comment_middle"><span class="tweet_date">'+transport.responseJSON[i].date+'</span><br/>'+transport.responseJSON[i].text+'</div>';
				el.innerHTML = el.innerHTML + '<div class="comment_bottom"></div>';
				i++
			}
		}
	});
}

function changeCommentMode(mode) {
	document.getElementById('comments').innerHTML = '<img src="/img/loading.gif" />';
	if (mode==0) {
		comment_mode = 0;
		document.getElementById('conversation_place').innerHTML = '@TWITTER';
		getTwitter(current_song);
	} else if (mode==1) {
		comment_mode = 1;
		document.getElementById('conversation_place').innerHTML = '@SANDMUSIC';
		getComments(current_song);
	} else if (mode==2) {
		comment_mode = 2;
		document.getElementById('conversation_place').innerHTML = '@SANDMUSIC';
		getLikes(current_song);
	}
}

function buttonUp(id) {
	var images = new Array();
	images['previous_btn'] = '/img/buttons/previous.png';
	images['play_btn'] = '/img/buttons/play.png';
	images['pause_btn'] = '/img/buttons/pause.png';
	images['next_btn'] = '/img/buttons/next.png';
	images['shuffle_btn'] = '/img/buttons/shuffle.png';
	images['now_playing_twitter_image'] = '/img/buttons/now_playing/twitter.png';
	images['now_playing_facebook_image'] = '/img/buttons/now_playing/facebook.png';
	images['now_playing_bandcamp_image'] = '/img/buttons/now_playing/bandcamp.png';
	images['now_playing_likes'] = 'background-image:url(\\\'/img/buttons/now_playing/likes.png\\\');';
	images['now_playing_comments'] = 'background-image:url(\\\'/img/buttons/now_playing/comments.png\\\');';
	var attribs = new Array();
	attribs['previous_btn'] = 'src';
	attribs['play_btn'] = 'src';
	attribs['pause_btn'] = 'src';
	attribs['next_btn'] = 'src';
	attribs['shuffle_btn'] = 'src';
	attribs['now_playing_twitter_image'] = 'src';
	attribs['now_playing_facebook_image'] = 'src';
	attribs['now_playing_bandcamp_image'] = 'src';
	attribs['now_playing_likes'] = 'style';
	attribs['now_playing_comments'] = 'style';	
	setTimeout("document.getElementById('"+id+"').setAttribute('"+attribs[id]+"','"+images[id]+"')",250);
}