<?php if (!isset($ajax)) { ?>
<?php echo $this->Html->css('lightbox'); ?>
<?php echo $this->Html->script('songs/songs'); ?>
<?php echo $this->Html->script('songs/playback'); ?>
<?php echo $this->Html->script('playlists/playlists'); ?>
<?php echo $this->Html->script('comments/comments'); ?>
<?php echo $this->Html->script('likes/likes'); ?>
<?php echo $this->Html->script('follows/follows'); ?>
<?php echo $this->Html->script('lightbox'); ?>

<script>
	var comment_mode = 0;
	var current_song = 0;
	var repeat = 0;
	var durCheck = 1;
	var songs_info;
</script>

<div class="clear"></div>

<?php if ($info['signed_in']) { ?>
<div class="grid_8 push_16" id="user_controls">
	<ul id="user_controls_list">
		<li>
			<a href="#" id="show_hide_hidden_playlist_add" rel="nofollow" onclick="showHideForm('hidden_playlist_add','show_hide_hidden_playlist_add','show','ADD PLAYLIST');return false;">ADD PLAYLIST</a>
		</li>
		<li>
			<a href="#" id="show_hide_hidden_playlist_edit" rel="nofollow" onclick="showHideForm('hidden_playlist_edit','show_hide_hidden_playlist_edit','show','EDIT PLAYLIST');return false;">EDIT PLAYLIST</a>
		</li>
		<li>
			<a href="#" id="show_hide_hidden_song_add" rel="nofollow" onclick="showHideForm('hidden_song_add','show_hide_hidden_song_add','show','ADD SONG');return false;">ADD SONG</a>
		</li>						
	</ul>
</div>

<div class="clear"></div>

<?php } //close if signed in ?>

<div class="grid_24" id="nav_anchor">
	<?php echo $this->Html->image('navbar_anchor.jpg', array('alt' => '')); ?>
</div>

<div id="hidden_playlist_add" class="hidden_form_wrapper" style="display:none;">
<div class="grid_7 hidden_form">
	<?php echo $this->Form->create('Playlist', array('default' => false, 'action' => 'add')); ?>
	 
    <?php
        echo $this->Form->input('name', array('label' => 'Playlist Name', 'id' => 'playlist_name', 'size' => 35, 'between' => '<br/>', 'after' => '&nbsp;<span id="valid_playlist_name" class="red">&#x2717</span><br/><br/>', 'onkeyup' => 'notEmpty(\'playlist_name\',\'valid_playlist_name\');'));
     ?>   
	<?php
		echo $this->Js->submit('Add Playlist', array('url' => '/playlists/add', 'success' => 'document.getElementById(\'PlaylistAddForm\').reset();resetValid(\'valid_playlist_name\');showHideForm(\'hidden_playlist_add\',\'show_hide_hidden_playlist_add\',\'hide\',\'ADD PLAYLIST\');getPlaylists('.$info['user_id'].');goToPlaylistMode(response.responseText);'));
	?>
	 
	<?php echo $this->Form->end(); ?>
</div>
</div>

<div id="hidden_playlist_edit" class="hidden_form_wrapper" style="display:none;">
<div class="grid_7 hidden_form" id="playlist_edit_list">
	Choose Playlist to Edit<br/>
	<?php foreach($playlists as $playlist) { ?>
	<div id="playlist_<?php echo $playlist['Playlist']['id']; ?>">
		<div class="grid_3">
<?php echo $this->Js->link($playlist['Playlist']['name'], '/songs/index/edit_playlist:'.$playlist['Playlist']['id'], array('method' => 'post', 'update' => '#content', 'before' => 'new Effect.Opacity(\'content\', { from: 1, to: 0.3, duration: 0.5});', 'evalScripts' => true, 'complete' => 'showHideForm(\'hidden_playlist_edit\',\'show_hide_hidden_playlist_edit\',\'hide\',\'EDIT PLAYLIST\');setTimeout("new Effect.Opacity(\'content\', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true)')); ?>
		</div>
		<div class="grid_1">
			<a href="#" rel="nofollow" title="Delete Playlist" onclick="deletePlaylist(<?php echo $playlist['Playlist']['id']; ?>);return false;" class="red">&#x2717</a>
		</div>
		<div class="clear"></div>
	</div>
	<?php } ?>
</div>
</div>

<div id="hidden_song_add" class="hidden_form_wrapper" style="display:none;">
<div class="grid_7 hidden_form">
	<?php echo $this->Form->create('Song', array('default' => false, 'action' => 'add')); ?>
	 
    <?php
        echo $this->Form->input('name', array('label' => 'Song Name', 'id' => 'name', 'size' => 35, 'between' => '<br/>', 'after' => '&nbsp;<span id="valid_name" class="red">&#x2717</span><br/>', 'onkeyup' => 'notEmpty(\'name\',\'valid_name\');'));
        echo $this->Form->input('artist', array('id' => 'artists', 'size' => 35, 'between' => '<br/>', 'after' => '&nbsp;<span id="valid_artist" class="red">&#x2717</span><br/>', 'onkeyup' => 'notEmpty(\'artists\',\'valid_artist\');'));
     ?>   
        <div id="artist_choices" class="autocomplete"></div>
        <script type="text/javascript">
        	new Ajax.Autocompleter("artists", "artist_choices", "/artists/autocomplete", {});
        </script>
    <?php
        echo $this->Form->input('album', array('id' => 'albums', 'size' => 35, 'between' => '<br/>', 'after' => '&nbsp;<span id="valid_album" class="green">&#x2713</span><br/>'));
    ?>
        <div id="album_choices" class="autocomplete"></div>
        <script type="text/javascript">
        	new Ajax.Autocompleter("albums", "album_choices", "/albums/autocomplete", {});
        </script>
    <?php
        echo $this->Form->input('link', array('label' => 'Link <span class="small demo">(must be audio file)<span>', 'between' => '<br/>', 'size' => 35, 'id' => 'link', 'after' => '&nbsp;<span id="valid_link" class="red">&#x2717</span><br/>', 'onkeyup' => 'notEmpty(\'link\',\'valid_link\');'));
    ?>
    Genre(s)<br/>
	<?php
		foreach($genres as $genre) {
			echo '<div class="form_column">'.$this->Form->checkbox('genre_'.$genre['Genre']['id'], array('value' => $genre['Genre']['id'])).'&nbsp;'.$genre['Genre']['name'].'&nbsp;</div>';
		}
	?>
	<div class="clear"></div>
	<br/>
	<?php
		echo $this->Js->submit('Add Song', array('url' => '/add', 'success' => 'document.getElementById(\'SongAddForm\').reset();resetValid(\'valid_name\');resetValid(\'valid_artist\');resetValid(\'valid_album\');resetValid(\'valid_link\');showHideForm(\'hidden_song_add\',\'show_hide_hidden_song_add\',\'hide\',\'ADD SONG\');getSongs();initialize();'));
	?>
	 
	<?php echo $this->Form->end(); ?>
</div>
</div>


<div class="clear"></div>

<div class="grid_16 section_header">
	NEW MUSIC
</div>

<div class="grid_7 push_1 section_header">
	NOW PLAYING
</div>

<div class="clear"></div>

<div id="playback_filter">
	<div class="grid_5 medium blue top_buffer">
		PLAYBACK CONTROLS<br/>
		<a href="#" onclick="YAHOO.MediaPlayer.previous();return false;"><?php echo $this->Html->image('buttons/previous.png', array('id' => 'previous_btn', 'alt' => 'Previous', 'title' => 'Previous', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/previous_over.png\');', 'onmouseup' => 'buttonUp(\'previous_btn\');')); ?></a>
		<a href="#" onclick="play();return false;"><?php echo $this->Html->image('buttons/play.png', array('id' => 'play_btn', 'alt' => 'Play', 'title' => 'Play', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/play_over.png\');', 'onmouseup' => 'buttonUp(\'play_btn\');')); ?></a>
		<a href="#" onclick="YAHOO.MediaPlayer.pause();return false;"><?php echo $this->Html->image('buttons/pause.png', array('id' => 'pause_btn', 'alt' => 'Pause', 'title' => 'Pause', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/pause_over.png\');', 'onmouseup' => 'buttonUp(\'pause_btn\');')); ?></a>
		<a href="#" onclick="YAHOO.MediaPlayer.next();return false;"><?php echo $this->Html->image('buttons/next.png', array('id' => 'next_btn', 'alt' => 'Next', 'title' => 'Next', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/next_over.png\');', 'onmouseup' => 'buttonUp(\'next_btn\');')); ?></a>
		<a href="/songs/index/shuffle:true/"><?php echo $this->Html->image('buttons/shuffle.png', array('id' => 'shuffle_btn', 'alt' => 'Shuffle', 'title' => 'Shuffle', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/shuffle_over.png\');', 'onmouseup' => 'buttonUp(\'shuffle_btn\');')); ?></a>
		<a href="#" onclick="return false;"><?php echo $this->Html->image('buttons/repeat.png', array('id' => 'repeat_btn', 'alt' => 'Repeat', 'title' => 'Repeat', 'onclick' => 'doRepeat();return false;')); ?></a>
		<div class="grid_2 small blue">VOLUME</div>
		<div class="clear"></div>
		<div class="grid_4 slider" id="volume_slider">
			<div class="handle" id="volume_handle"></div>
		</div>
	</div>
	
	<div class="grid_9 push_2 top_buffer">
		<span class="medium blue">
			FILTERS
		</span>
	<?php
	echo $this->Js->link(
		'RESET',
		'/',
		array(
			'class' => 'plain',
			'update' => '#content',
			'evalScripts' => true,
			'before' => 'new Effect.Opacity(\'content\', { from: 1, to: 0.3, duration: 0.5});',
			'complete' => 'setTimeout("new Effect.Opacity(\'content\', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);initialize();'
		)
	);
	?>
		<br/>
		<select name="playlist_id" id="playlist_filter" style="width:165px;" onchange="filter();">
			<option value="-1">Playlist</option>
			<option value="0">Songs I Like</option>
			<?php foreach($playlist_filter as $playlist) { ?>
			<option value="<?php echo $playlist['Playlist']['id']; ?>"><?php echo $playlist['Playlist']['name']; ?></option>
			<?php } ?>
		</select>
		<select name="artist_id" id="artist_filter" style="width:165px;" onchange="filter();">
			<option value="0">Artist</option>
			<?php foreach($artists_filter as $artist) { ?>
			<option value="<?php echo $artist['Artist']['id']; ?>"><?php echo $artist['Artist']['name']; ?></option>
			<?php } ?>
		</select>
		<select name="genre_id" id="genre_filter" style="width:165px;" onchange="filter();">
			<option value="0">Genre</option>
			<?php foreach($genres as $genre) { ?>
				<option value="<?php echo $genre['Genre']['id']; ?>"><?php echo $genre['Genre']['name']; ?></option>
			<?php } ?>
		</select>
		<select name="follower_id" id="follows_filter" style="width:165px;" onchange="filter();" <?php if (!$info['signed_in']) { ?>disabled="disabled" <?php } ?>>
			<option value="-2">Follow</option>
			<option value="-1">Me</option>
			<option value="0">All</option>
			<?php foreach($follows_filter as $follow) { ?>
			<option value="<?php echo $follow['User']['id']; ?>"><?php echo $follow['User']['first_name'].' '.$follow['User']['last_name']; ?></option>
			<?php } ?>
		</select>
	</div>
	
	<div class="grid_7 push_3">
		<div id="now_playing">
		</div>
		<div id="now_playing_time"><div id="now_playing_elapsed" class="left" style="width:35px;"></div><div id="now_playing_total" class="left" style="width:40px;"></div><div class="clear"></div></div>
		<div id="now_playing_stats">
			<div id="now_playing_likes" class="stats_icon" style="display:none;" onmousedown="this.setAttribute('style','background-image:url(\'/img/buttons/now_playing/likes_over.png\');');" onmouseup="buttonUp('now_playing_likes');"></div>
			<div id="now_playing_comments" class="stats_icon" style="display:none;" onmousedown="this.setAttribute('style','background-image:url(\'/img/buttons/now_playing/comments_over.png\');');" onmouseup="buttonUp('now_playing_comments');"></div>			
			<div id="now_playing_twitter" class="stats_icon"><a id="now_playing_twitter_link" href="#" onclick="changeCommentMode(0);return false;" title="View Tweets"><?php echo $this->Html->image('buttons/now_playing/twitter.png', array('id' => 'now_playing_twitter_image', 'alt' => 'Twitter', 'style' => 'display:none;', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/now_playing/twitter_over.png\');', 'onmouseup' => 'buttonUp(\'now_playing_twitter_image\');')); ?></a></div>
			<div id="now_playing_facebook" class="stats_icon"><?php echo $this->Html->image('buttons/now_playing/facebook.png', array('id' => 'now_playing_facebook_image', 'alt' => 'Facebook', 'title' => 'Share on Facebook', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/now_playing/facebook_over.png\');', 'onclick' => '', 'onmouseup' => 'buttonUp(\'now_playing_facebook_image\');', 'style' => 'display:none;')); ?></div>
			<div id="now_playing_tweet" class="stats_icon" style="display:none;"><?php echo $this->Html->image('buttons/now_playing/tweetn.png', array('id' => 'now_playing_tweet_image', 'alt' => 'Tweet', 'onclick' => '')); ?></div>
			<div id="now_playing_bandcamp" class="stats_icon"><a id="now_playing_bandcamp_link" href="#" target="_blank"><?php echo $this->Html->image('buttons/now_playing/bandcamp.png', array('id' => 'now_playing_bandcamp_image', 'alt' => 'Bandcamp', 'title' => 'Visit Artist\'s Bandcamp', 'onmousedown' => 'this.setAttribute(\'src\',\'/img/buttons/now_playing/bandcamp_over.png\');', 'onclick' => '', 'onmouseup' => 'buttonUp(\'now_playing_bandcamp_image\');', 'style' => 'display:none;')); ?></a></div>
		</div>
	</div>
	
	<div class="clear"></div>

</div>

<div class="clear"></div>

<?php } // End !isset($ajax) ?>

<?php
$this->Paginator->options(
	array(
		'update' => '#content',
		'url' => $this->passedArgs,
		'evalScripts' => true,
		'before' => 'new Effect.Opacity(\'content\', { from: 1, to: 0.3, duration: 0.5});',
		'complete' => 'setTimeout("new Effect.Opacity(\'content\', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);initialize();'
		)
	);
?>

<?php if (!isset($ajax)) { ?>
<div class="grid_16" id="content">
<?php } ?>
	<table id="songs_table">
		<tr>
			<th></th>
			<th><?php echo $this->Paginator->sort('SONG', 'Song.name', array('rel' => 'nofollow')); ?></th>
			<th><?php echo $this->Paginator->sort('ARTIST', 'Artist.name', array('rel' => 'nofollow')); ?></th>
			<th><?php echo $this->Paginator->sort('AGE', 'Song.created', array('rel' => 'nofollow')); ?></th>
			<th><?php echo $this->Paginator->sort('PLAYS', 'Song.plays', array('rel' => 'nofollow')); ?></th>
			<th><?php echo $this->Paginator->sort('LIKES', 'Song.likes', array('rel' => 'nofollow')); ?></th>
			<th><?php echo $this->Paginator->sort('HYPE', 'Song.comments', array('rel' => 'nofollow')); ?></th>
			<th></th>
		</tr>
		<?php $i=0; foreach($songs as $song) { ?>
			<tr class="<?php echo $i%2==0 ? ' even' : ' odd'; ?>" id="song_row_<?php echo $song['Song']['id']; ?>">
				<td width="20" id="song_<?php echo $song['Song']['id']; ?>"><a id="<?php echo $song['Song']['id']; ?>" href="<?php echo $song['Song']['link']; ?>"></a></td>
				<td width="165" id="name_<?php echo $song['Song']['id']; ?>" <?php if (strlen($song['Song']['name'])>20) { echo 'title="'.$song['Song']['name'].'"'; } ?>><?php echo strlen($song['Song']['name'])>20 ? strtoupper(substr($song['Song']['name'],0,19)).'...' : strtoupper($song['Song']['name']); ?></td>
				<td width="185" id="artist_<?php echo $song['Song']['id']; ?>"><a href="#" class="plain" title="More by <?php echo $song['Artist']['name']; ?>" onclick="filterByArtist(<?php echo $song['Artist']['id']; ?>);return false;"><?php echo strtoupper($song['Artist']['name']); ?></a><?php if (!empty($song['Artist']['twitter'])) { ?>&nbsp;<a href="http://twitter.com/<?php echo $song['Artist']['twitter']; ?>" target="_blank"><?php echo $this->Html->image('buttons/artisttwitter.png', array('alt' => 'Twitter')); ?><?php } ?></a></td>
				<td width="70"><?php echo strtoupper($song['Song']['created']); ?></td>
				<span id="album_<?php echo $song['Song']['id']; ?>" style="display:none;"><?php echo $song['Album']['name']; ?></span>
				<td width="35"><span id="plays_<?php echo $song['Song']['id']; ?>"><?php echo $song['Song']['plays']; ?></span></td>
				<td width="35" class="like_col"><a href="http://sandmusic.ca/songs/index/song:<?php echo $song['Song']['id']; ?>" title="Like" <?php if ($info['signed_in']) { ?> onclick="likeSong('<?php echo $song['Song']['id']; ?>');return false;" <?php } else { ?> onclick="alert('You are listening as a guest. You can\'t Like songs without an account.');return false;" <?php } ?>><?php echo $this->Html->image('buttons/addanotherlike.png', array('alt' => 'Like', 'width' => 22.5, 'height' => 15)); ?></a><span id="like_<?php echo $song['Song']['id']; ?>"><?php echo $song['Song']['likes']; ?></span></td>				
				<td width="35"><span id="comment_count_<?php echo $song['Song']['id']; ?>"><?php echo $song['Song']['comments']; ?></span><a href="<?php if (!$info['signed_in']) { echo "#"; } else { echo "/comments/add/".$song['Song']['id']; } ?>" <?php if ($info['signed_in']) { ?> class="lbOn" <?php } ?> title="Comment" <?php if (!$info['signed_in']) { ?> onclick="alert('You are listening as a guest. You can\'t comment on songs without an account.');return false;" <?php } ?>><?php echo $this->Html->image('buttons/addanothercomment.png', array('alt' => 'Like', 'width' => 22.5, 'height' => 15)); ?></a></td>
				<?php if (isset($playlist) && !empty($playlist_id) && !$song['Song']['in_playlist']) { ?>
				<td width="35"><a href="#" class="add" title="Add to Playlist" id="add_<?php echo $song['Song']['id']; ?>" onclick="addNewSongToPlaylist(<?php echo $playlist_id; ?>,<?php echo $song['Song']['id']; ?>);return false;">+</a></td>
				<?php } else if (isset($playlist) && !empty($playlist_id) && $song['Song']['in_playlist']) { ?>
				<td width="35"><a href="#" class="red" title="Remove from Playlist" id="add_<?php echo $song['Song']['id']; ?>" onclick="removeSongFromPlaylist(<?php echo $playlist_id; ?>,<?php echo $song['Song']['id']; ?>);return false;">&#x2717</a></td>
				<?php } else { ?>
				<td width="10"><a href="#" title="Dead Link" id="dead_<?php echo $song['Song']['id']; ?>" <?php if ($info['signed_in']) { ?> onclick="deadSong(<?php echo $song['Song']['id']; ?>);return false;" <?php } else { ?> onclick="alert('You are listening as a guest. You can\'t remove songs without an account.');return false;" <?php } ?>><?php echo $this->Html->image('buttons/deadlink.png', array('alt' => 'Dead', 'width' => '16', 'height' => '16')); ?></a></td>
				<?php } ?>
			</tr>
		<?php $i++; } ?>
	</table>
	<div class="paging">
		<div class="grid_3 push_13">
		<?php echo $this->Paginator->prev('Previous', array('rel' => 'nofollow'), null, array('class' => 'disabled')); ?>&nbsp;<?php echo $this->Paginator->next('Next', array('rel' => 'nofollow'), null, array('class' => 'disabled')); ?>
		</div>
	</div>
<?php if (!isset($ajax)) { ?>
</div>
<?php } ?>

<?php if (!isset($ajax)) { ?>
<div class="grid_7 push_1 column_header" id="right_content">
	CONVERSATION <span class="blue" id="conversation_place">@TWITTER</span>
</div>

<div class="grid_7 push_1" id="comments"></div>

<?php } ?>

<a href="#" id="current_track" style="display:none;"></a>
<script type="text/javascript">
    songs_info = <?php echo $songs_info; ?>;
    var YMPParams = 
    {
        displaystate:-1,
        volume:0.5,
        autoplay:<?php echo ($autoplay) ? "true" : "false"; ?>
    }
</script>
<script type="text/javascript" src="http://mediaplayer.yahoo.com/js"></script>
<script type="text/javascript">
    var currently_playing_track;
    var sk_sl; //seek slider
    var apiReadyHandler = function ()
    {
        /* Once API ready handler is invoked, YAHOO.MediaPlayer class can be accessed safely */
        /* For example: Add other event listeners **/
        YAHOO.MediaPlayer.onTrackPause.subscribe(onTrackPauseHandler);
        YAHOO.MediaPlayer.onTrackStart.subscribe(onTrackPlayHandler);
        YAHOO.MediaPlayer.onTrackComplete.subscribe(onTrackCompleteHandler);
        YAHOO.MediaPlayer.onProgress.subscribe(onTrackProgressHandler);
    }

    var onTrackPauseHandler = function ()
    {
		//minimize player if it's showing
		if (YAHOO.MediaPlayer.getPlayerViewState()!=-1) {
			setTimeout("YAHOO.MediaPlayer.setPlayerViewState(-1)",1000);
		}
    }

    var onTrackPlayHandler = function (media)
    {
        /* Handler for onTrackPlay event */
        //document.getElementById('play_button').innerHTML = 'Pause';
        //document.getElementById('play_button').setAttribute('onclick', 'YAHOO.MediaPlayer.pause();return false;');
        //currently_playing_track = track;
        document.getElementById('comments').innerHTML = '<img src="/img/loading.gif" alt="Loading" />';
        var track = media['mediaObject']['anchor']['id'];
        current_song = track;
        document.getElementById('current_track').innerHTML = track;
		var song_info = songs_info[track];
		el = document.getElementById('now_playing');
		el.innerHTML = '<span id="now_playing_song"><span class="blue">'+song_info.Song.name+'</span> BY <span class="blue"><a href="#" class="plain" title="More by '+song_info.Artist.name+'" onclick="filterByArtist('+song_info.Artist.id+');return false;">'+song_info.Artist.name+'</a></span><br/>FROM <span class="blue"><a href="#" class="plain" title="More from '+song_info.Album.name+'" onclick="filterByAlbum('+song_info.Album.id+');return false;">'+song_info.Album.name+'</a></span></span><br/><span id="now_playing_added_info">HOSTED BY <span class="blue"><a class="plain" href="http://www.'+song_info.Host.url+'" target="_blank">'+song_info.Host.name+'</a></span><br/>ADDED BY <span class="blue">'+song_info.User.first_name+' '+song_info.User.last_name+'</span>';
		if (song_info.Follow.follow==0) {
			el.innerHTML = el.innerHTML + '&nbsp;<span class="blue"><a id="follow" href="#" class="plain tiny" onclick="follow('+song_info.User.id+');return false;">Follow</a></span>';
		} else if (song_info.Follow.follow==1) {
			el.innerHTML = el.innerHTML + '&nbsp;<span class="blue"><a id="follow" href="#" class="plain tiny" onclick="unfollow(\''+song_info.User.id+'\');return false;">Unfollow</a></span>';
		}
		el.innerHTML = el.innerHTML + '</span>';
		var dur = parseInt(YAHOO.MediaPlayer.getTrackDuration());
		var mins = parseInt(dur/60);
		var secs = dur - mins*60;
		if (mins!=0 || secs!=0) {
			if (secs<10) {
				secs = '0' + secs;
			}
			document.getElementById('now_playing_total').innerHTML = '/ '+mins+':'+secs;
			durCheck = 0;
		} else {
			document.getElementById('now_playing_total').innerHTML = '';
			durCheck = 1;
		}
		el = document.getElementById('now_playing_likes');
		if (el.getAttribute('style').search('display')!=-1) {
			el.setAttribute('style', '');
		}
		el.innerHTML = '<a href="#" class="plain" onclick="changeCommentMode(2);return false;" title="View Likes">'+song_info.Song.likes+'</a>';
		el = document.getElementById('now_playing_comments');
		if (el.getAttribute('style').search('display')!=-1) {
			el.setAttribute('style', '');
		}
		el.innerHTML = '<a href="#" class="plain" onclick="changeCommentMode(1);return false;" title="View Comments">'+song_info.Song.comments+'</a>';
		el = document.getElementById('now_playing_twitter_image');
		if (el.getAttribute('style').search('display')!=-1) {
			el.setAttribute('style', '');
		}
		el = document.getElementById('now_playing_facebook_image');
		if (el.getAttribute('style').search('display')!=-1) {
			el.setAttribute('style', '');
		}
		el.setAttribute('onclick', 'window.open ("http://www.facebook.com/sharer.php?u=http://sandmusic.ca/songs/index/song:'+track+'","myWindow","width=650,height=450");');
		el = document.getElementById('now_playing_tweet');
		if (el.getAttribute('style').search('display')!=-1) {
			el.setAttribute('style','');
		}
		el = document.getElementById('now_playing_tweet_image');
		var artistInTweet;
		song_info.Artist.twitter==null ? artistInTweet = song_info.Artist.name.replace(/ /g,"+") : artistInTweet = '@'+song_info.Artist.twitter;
		el.setAttribute('onclick', 'window.open ("http://www.twitter.com/share?url=http://sandmusic.ca/songs/index/song:'+track+'&text=Now+playing+'+song_info.Song.name.replace(/ /g,"+")+'+by+'+artistInTweet+'+on+@SandMusicCA&related=@SandMusicCA","myWindow","width=650,height=450");');
		
		el = document.getElementById('now_playing_bandcamp_image');
		if (song_info.Artist.bandcamp!=null) {
			if (el.getAttribute('style').search('display')!=-1) {
				el.setAttribute('style', '');
			}
			el = document.getElementById('now_playing_bandcamp_link');
			el.setAttribute('href', 'http://'+song_info.Artist.bandcamp+'.bandcamp.com');
		} else {
			el.setAttribute('style', 'display:none;');
		}
		/*				
		new Ajax.Request("/songs/get_info/"+track, {
			evalScripts:true,
			evalJSON:'force',
			onComplete:function (transport) {
				el = document.getElementById('now_playing');
				el.innerHTML = '<span id="now_playing_song"><span class="blue">'+transport.responseJSON.Song.name+'</span> BY <span class="blue"><a href="#" class="plain" title="More by '+transport.responseJSON.Artist.name+'" onclick="filterByArtist('+transport.responseJSON.Artist.id+');return false;">'+transport.responseJSON.Artist.name+'</a></span><br/>FROM <span class="blue"><a href="#" class="plain" title="More from '+transport.responseJSON.Album.name+'" onclick="filterByAlbum('+transport.responseJSON.Album.id+');return false;">'+transport.responseJSON.Album.name+'</a></span></span><br/><span id="now_playing_added_info">HOSTED BY <span class="blue"><a class="plain" href="http://www.'+transport.responseJSON.Host.url+'" target="_blank">'+transport.responseJSON.Host.name+'</a></span><br/>ADDED BY <span class="blue">'+transport.responseJSON.User.first_name+' '+transport.responseJSON.User.last_name+'</span>';
				if (transport.responseJSON.Follow.follow==0) {
					el.innerHTML = el.innerHTML + '&nbsp;<span class="blue"><a id="follow" href="#" class="plain tiny" onclick="follow('+transport.responseJSON.User.id+');return false;">Follow</a></span>';
				} else if (transport.responseJSON.Follow.follow==1) {
					el.innerHTML = el.innerHTML + '&nbsp;<span class="blue"><a id="follow" href="#" class="plain tiny" onclick="unfollow(\''+transport.responseJSON.User.id+'\');return false;">Unfollow</a></span>';
				}
				el.innerHTML = el.innerHTML + '</span>';
				var dur = parseInt(YAHOO.MediaPlayer.getTrackDuration());
				var mins = parseInt(dur/60);
				var secs = dur - mins*60;
				if (mins!=0 || secs!=0) {
					if (secs<10) {
						secs = '0' + secs;
					}
					document.getElementById('now_playing_total').innerHTML = '/ '+mins+':'+secs;
					durCheck = 0;
				} else {
					document.getElementById('now_playing_total').innerHTML = '';
					durCheck = 1;
				}
				el = document.getElementById('now_playing_likes');
				if (el.getAttribute('style').search('display')!=-1) {
					el.setAttribute('style', '');
				}
				el.innerHTML = '<a href="#" class="plain" onclick="changeCommentMode(2);return false;" title="View Likes">'+transport.responseJSON.Song.likes+'</a>';
				el = document.getElementById('now_playing_comments');
				if (el.getAttribute('style').search('display')!=-1) {
					el.setAttribute('style', '');
				}
				el.innerHTML = '<a href="#" class="plain" onclick="changeCommentMode(1);return false;" title="View Comments">'+transport.responseJSON.Song.comments+'</a>';
				el = document.getElementById('now_playing_twitter_image');
				if (el.getAttribute('style').search('display')!=-1) {
					el.setAttribute('style', '');
				}
				el = document.getElementById('now_playing_facebook_image');
				if (el.getAttribute('style').search('display')!=-1) {
					el.setAttribute('style', '');
				}
				el.setAttribute('onclick', 'window.open ("http://www.facebook.com/sharer.php?u=http://sandmusic.ca/songs/index/song:'+track+'","myWindow","width=650,height=450");');
			}
		});
		*/
		//minimize player if it's showing
		if (YAHOO.MediaPlayer.getPlayerViewState()!=-1) {
			setTimeout("YAHOO.MediaPlayer.setPlayerViewState(-1)",1000);
		}
		
		/* Comment Modes
		0 = twitter
		1 = user comments
		2 = likes
		*/
		if (comment_mode==0) {
			getTwitter(track);
		} else if (comment_mode==1) {
			getComments(track);
		} else if (comment_mode==2) {
			getLikes(track);
		}
        //console.log(media);
    }

    var onTrackCompleteHandler = function (media)
    {
        /* Handler for onTrackComplete event */
        /*
        var el = document.getElementById('repeat');
        if (el.innerHTML=='No Repeat') {
        	YAHOO.MediaPlayer.play();
        }
        */
        var track = media['mediaObject']['anchor']['id'];
		new Ajax.Request("/songs/update_play_count/"+track, {
			evalScripts:true,
			//evalJSON:'force',
			onComplete:function (transport) {
				el = document.getElementById('plays_'+track);
				el.innerHTML = transport.responseText;				
			}
		});
		
		if (repeat==1) {
			YAHOO.MediaPlayer.play();
		} else if (YAHOO.MediaPlayer.getPlayerState()==7) {
			new Ajax.Updater("content", document.getElementsByClassName('next')[0], {
				evalScripts:true,
				method: 'post',
				onCreate:function (transport) {new Effect.Opacity('content', { from: 1, to: 0.3, duration: 0.5});},
				onComplete: function(transport) {
			    	setTimeout("new Effect.Opacity('content', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);
			    	initialize();
			    	setTimeout("YAHOO.MediaPlayer.play()", 1000);
			  	}
			});
		}
    }
    
    var onTrackProgressHandler = function (prog) {
    	var mins = parseInt(prog['elapsed']/60000);
		var secs = parseInt(prog['elapsed']/1000) - mins*60;
		if (secs<10) {
			secs = '0'+secs;
		}
    	document.getElementById('now_playing_elapsed').innerHTML = mins+':'+secs+' ';
		
		if (durCheck==1) {
			var dur = parseInt(YAHOO.MediaPlayer.getTrackDuration());
			if (dur!=0) {	
				var mins = parseInt(dur/60);
				var secs = dur - mins*60;
				if (secs<10) {
					secs = '0' + secs;
				}
				document.getElementById('now_playing_total').innerHTML = '/ '+mins+':'+secs;
    		}
    	}
    }

    YAHOO.MediaPlayer.onAPIReady.subscribe(apiReadyHandler);
    
    function play() {
    	if (YAHOO.MediaPlayer.getPlayerState()!=1) {
    		YAHOO.MediaPlayer.play();
    	} else {
    		YAHOO.MediaPlayer.play(null, 1000*YAHOO.MediaPlayer.getTrackPosition());
       	}
    }
    
    function doRepeat() {
    	if (repeat==0) {
    		repeat = 1;
    		document.getElementById('repeat_btn').setAttribute('src', '/img/buttons/repeat_locked.png');
    		YAHOO.MediaPlayer.addTracks(document.getElementById('song_'+current_song), null, true);
    	} else {
    		repeat = 0;
    		document.getElementById('repeat_btn').setAttribute('src', '/img/buttons/repeat.png');
    		YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"), null, true);
    	}
    }
    
    function doShuffle() {
    	var e1 = document.getElementById('shuffle');
    	if (e1.innerHTML == 'Shuffle') {
    		e1.innerHTML = 'No Shuffle';
    	} else {
    		e1.innerHTML = 'Shuffle';
    	}
    }
</script>

<script type="text/javascript">
  (function() {
    var volume_slider = $('volume_slider');

    new Control.Slider(volume_slider.down('.handle'), volume_slider, {
      range: $R(0, 50),
      sliderValue: 25,
      onSlide: function(value) {
        YAHOO.MediaPlayer.setVolume(value/50);
      },
      onChange: function(value) { 
        YAHOO.MediaPlayer.setVolume(value/50);
      }
    });
  })();
</script>

<?php echo $this->Js->writeBuffer(); ?>