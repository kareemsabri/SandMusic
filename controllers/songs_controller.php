<?php

/**************************************************************
Name: Songs Controller
File: /app/controllers/songs_controller.php
Created: September 25, 2010
Author: Kareem Sabri
**************************************************************/

Class SongsController extends AppController {
	var $components = array('Twitter');
	var $paginate = array(
		'limit' => 30,
		'conditions' => array(
			'dead' => 0
		),
		'order' => array(
			'Song.created' => 'DESC',
			'Artist.name'
		)
	);
	
	function index() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix']);
		$this->set('search', true);
		//set notification message for top of page here
		$messages = array('Like <a href="http://www.facebook.com/pages/SandMusic/166924103349516" target="_blank">SandMusic</a> on Facebook to get songs in your news feed!<br/><iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FSandMusic%2F166924103349516&amp;width=292&amp;colorscheme=light&amp;show_faces=false&amp;stream=false&amp;header=true&amp;height=62" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>', 'NEW! Check out <a href="/the_playlist">The SandMusic Playlist</a>, a weekly collection of great tracks.', '<br/><a href="http://twitter.com/SandMusicCA" target="_blank"><img src="http://twitter-badges.s3.amazonaws.com/follow_us-c.png" /></a>');
		$this->set('message', htmlentities($messages[rand(0,count($messages)-1)]));
		$this->set('overlay', true);
		
		if ($this->RequestHandler->isAjax()) {
			$this->set('ajax', true);
		}
		
		if ($this->params['url']['more']) {
			$this->set('more', true);
		}
		
		$find_params = array(
			'conditions' => array(
				'dead' => 0
			)
		);
		
		$count = $this->Song->find('count', $find_params);
		$page_count = $count/30;
		if ($count%30!=0) {
			$page_count++;
		}
		$this->set('page_count', $page_count);
		
		$conditions = array();
		
		if (isset($this->params['named']['shuffle'])) {
			$this->paginate['order'] = 'rand()';
		} else if (isset($this->params['named']['edit_playlist'])) {
			$this->set('playlist', true);
			$this->loadModel('Playlist');
			$playlist = $this->Playlist->findById($this->params['named']['edit_playlist']);
			if ($playlist!=null && $playlist['Playlist']['user_id']==$this->Session->read('user_id')) {
				$this->set('playlist_id', $this->params['named']['edit_playlist']);
				$this->loadModel('PlaylistsSong');
				$find_params = array(
					'conditions' => array(
						'playlist_id' => $this->params['named']['edit_playlist']
					),
					'fields' => array(
						'song_id'
					)
				);
				
				$playlist_song_ids = $this->PlaylistsSong->find('list', $find_params);
			} else {
				$this->redirect('/');
			}
		} else {
			if (isset($this->params['named']['playlist']) && $this->params['named']['playlist']!=-1 || isset($this->params['playlist'])) {
				if (isset($this->params['playlist'])) {
					$this->params['named']['playlist'] = $this->params['playlist'];
					$this->passedArgs['playlist'] = $this->params['playlist'];
				}
				if ($this->params['named']['playlist']==0) {
					$this->loadModel('Like');
					$find_params = array(
						'conditions' => array(
							'user_id' => $this->Session->read('user_id')
						),
						'fields' => array(
							'song_id'
						)
					);
					
					$liked_songs = $this->Like->find('list', $find_params);
					$conditions['Song.id'] = $liked_songs;
				} else {
					$this->loadModel('Playlist');
					$pl = $this->Playlist->findById($this->params['named']['playlist']);
					if ($pl['Playlist']['user_id']!=$this->Session->read('user_id') && $pl['Playlist']['public']==0) {
						$this->redirect('/');
					}
					$this->set('title_for_layout', $info['title_prefix'].' - '.$pl['Playlist']['name']);
					$this->loadModel('PlaylistsSong');
					$find_params = array(
						'conditions' => array(
							'playlist_id' => $this->params['named']['playlist']
						),
						'fields' => array(
							'song_id'
						)
					);
					
					$playlist_songs = $this->PlaylistsSong->find('list', $find_params);
					$conditions['Song.id'] = $playlist_songs;				
				}
			}
			
			if (isset($this->params['named']['artist']) && $this->params['named']['artist']!=0) {
				$conditions['Song.artist_id'] = $this->params['named']['artist'];
			}
			
			if (isset($this->params['named']['genre']) && $this->params['named']['genre']!=0) {
				$this->loadModel('GenresSong');
				$find_params = array(
					'conditions' => array(
						'genre_id' => $this->params['named']['genre']
					),
					'fields' => array(
						'song_id'
					)
				);
				$genre_song_ids = $this->GenresSong->find('list', $find_params);
				$conditions['Song.id'] = (!empty($conditions['Song.id'])) ? array_intersect($genre_song_ids,$conditions['Song.id']) : $genre_song_ids;
			}

			if (isset($this->params['named']['user']) && $this->params['named']['user']!=-2 && $info['signed_in']) {
				if ($this->params['named']['user']==-1) {
					$follows = $info['user_id'];
				} else if ($this->params['named']['user']==0) {
						$this->loadModel('Follow');
						$find_params = array(
							'conditions' => array(
								'follower_id' => $info['user_id']
							),
							'fields' => array(
								'user_id'
							)
						);
						
						$follows = $this->Follow->find('list', $find_params);
					} else {
						$follows = $this->params['named']['user'];
					}
					$conditions['Song.user_id'] = $follows;
			}
			
		}
		
		if (isset($this->params['named']['song']) && $this->params['named']['song']!=0) {
			$conditions['Song.id'] = $this->params['named']['song'];
			$this->set('autoplay', true);
		}
		
		if (isset($this->params['url']['q'])) {
			$conditions['OR'] = array(
				array('Song.name LIKE' => '%'.$this->params['url']['q'].'%'),
				array('Artist.name LIKE' => '%'.$this->params['url']['q'].'%'),
				array('Album.name LIKE' => '%'.$this->params['url']['q'].'%')
			);
			$this->paginate['order'] = 'Artist.name';
			$this->set('q', $this->params['url']['q']);
		}
		
		$songs = $this->paginate('Song', $conditions);
		
		$song_ids = array();
		$songs_info = array();
		
		//get follower info
		if ($info['signed_in']) {
			$this->loadModel('Follow');
			$find_params = array(
				'conditions' => array(
					'follower_id' => $info['user_id']
				),
				'fields' => array(
					'user_id'
				)
			);
			$follows = $this->Follow->find('list', $find_params);
		}
		
		foreach($songs as $key=>$song) {
			$song_ids[$song['Song']['id']] = $key;
			$songs[$key]['Song']['created'] = str_ireplace(' ago','',$this->_ago($song['Song']['created']));
			$songs[$key]['Song']['in_playlist'] = 0;
			$songs_info[$song['Song']['id']] = array(
				'Song' => array(
					'name' => $song['Song']['name'],
					'likes' => $song['Song']['likes'],
					'comments' => $song['Song']['comments']
				),
				'Artist' => array(
					'id' => $song['Artist']['id'],
					'name' => $song['Artist']['name'],
					'twitter' => $song['Artist']['twitter'],
					'bandcamp' => $song['Artist']['bandcamp']
				),
				'Album' => array(
					'id' => $song['Album']['id'],
					'name' => $song['Album']['name']
				),
				'Host' => array(
					'name' => $song['Host']['name']=='' ? $song['Host']['url'] : $song['Host']['name'],
					'url' => $song['Host']['url']
				),
				'User' => array(
					'id' => $song['User']['id'],
					'first_name' => $song['User']['first_name'],
					'last_name' => $song['User']['last_name']
				),
				'Follow' => array(
					'follow' => !$info['signed_in'] ? -1 : $info['user_id']==$song['User']['id'] ? -1 : in_array($song['User']['id'], $follows ? 0 : 1)
				)
			);
		}
		$this->set('songs_info', json_encode($songs_info));
		
		if (isset($this->params['named']['song']) && count($songs)==1) {
			$this->set('title_for_layout', $info['title_prefix'].' - '.$songs[0]['Song']['name'].' by '.$songs[0]['Artist']['name']);
		}
		
		if (isset($playlist_song_ids)) {
			foreach($playlist_song_ids as $pl) {
				if (array_key_exists($song_ids[$pl], $songs)) {
					$songs[$song_ids[$pl]]['Song']['in_playlist'] = 1;
				}
			}
		}
		
		$this->set('songs', $songs);
		
		$this->loadModel('Artist');
		
		$find_params = array(
			'fields' => array(
				'DISTINCT artist_id'
			)
		);
		
		$artists = $this->Song->find('all', $find_params);
		
		$artist_ids = array();
		
		foreach($artists as $artist) {
			$artist_ids[] = $artist['Song']['artist_id'];
		}
		
		$find_params = array(
			'conditions' => array(
				'id' => $artist_ids
			),
			'order' => array(
				'name'
			)
		);
		
		$this->set('artists_filter', $this->Artist->find('all', $find_params));
		
		$this->loadModel('Album');
		$this->set('albums_filter', $this->Album->find('all', array('order' => 'name')));
		
		$find_params = array(
			/*
			'conditions' => array(
				'NOT' => array(
					'id' => $blockers
				)
			),
			*/
			'fields' => array(
				'id',
				'first_name',
				'last_name'
			),
			'order' => array(
				'last_name'
			)
		);
		
		if ($info['signed_in']) {
			$this->loadModel('Follow');
			$this->loadModel('User');
			$find_params = array(
				'conditions' => array(
					'follower_id' => $info['user_id']
				),
				'fields' => array(
					'user_id'
				)
			);
			
			$follows = $this->Follow->find('list', $find_params);
			
			$find_params = array(
				'conditions' => array(
					'id' => $follows
				),
				'fields' => array(
					'id',
					'handle',
					'first_name',
					'last_name'
				),
				'order' => 'last_name'
			);
			
			$follows = $this->User->find('all', $find_params);
			
			$this->set('follows_filter', $follows);
		}
		
		$this->loadModel('Playlist');
		$find_params = array(
			'conditions' => array(
				'user_id' => $this->Session->read('user_id')
			)
		);
		$this->set('playlists', $this->Playlist->find('all', $find_params));
		
		$find_params = array(
			'conditions' => array(
				'OR' => array(
					'user_id' => $this->Session->read('user_id'),
					'public' => 1
				)
			)
		);
		$this->set('playlist_filter', $this->Playlist->find('all', $find_params));
		
		$this->loadModel('Genre');
		$genres = $this->Genre->find('all', array('order' => 'name'));
		$this->set('genres', $genres);
	}
	
	function add() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].'Add Song');
		
		if (!$info['signed_in']) {
			die('Not authorized');
		}		
		
		if (!empty($this->data)) {
			$error_message = null;
			
			//load twitter API
			App::import('Vendor', 'twitteroauth/twitteroauth');
			
			//get artist id
			$this->loadModel('Artist');
			$find_params = array(
				'conditions' => array(
					'name' => $this->data['Song']['artist']
				),
				'fields' => array(
					'id',
					'name',
					'twitter'
				)
			);
			$artist = $this->Artist->find('first', $find_params);
			
			if ($artist!=null) {
				$this->data['Song']['artist_id'] = $artist['Artist']['id'];
				unset($this->data['Song']['artist']);
			} else {
				$artist = array(
					'Artist' => array(
						'name' => trim($this->data['Song']['artist'])
					)
				);
				
				$this->Artist->set($artist);
				if ($this->Artist->validates()) {
					$this->Artist->create();
					$this->Artist->save($artist);
					$this->data['Song']['artist_id'] = $this->Artist->id;
					unset($this->data['Song']['artist']);
				} else {
					$error_message = $this->Artist->invalidFields();
				}
			}
			
			if (is_null($error_message)) {
				//get album id
				$this->loadModel('Album');
				$find_params = array(
					'conditions' => array(
						'artist_id' => $this->data['Song']['artist_id'],
						'name' => $this->data['Song']['album']
					),
					'fields' => array(
						'id'
					)
				);
				
				$album = $this->Album->find('first', $find_params);
				
				if ($album!=null) {
					$this->data['Song']['album_id'] = $album['Album']['id'];
					unset($this->data['Song']['album']);
				} else {
					if (strtolower($this->data['Song']['album'])=='untitled' || empty($this->data['Song']['album'])) {
						$album = $this->Album->findByName('Untitled');
						$this->data['Song']['album_id'] = $album['Album']['id'];
						unset($this->data['Song']['album']);
					} else {					
						$album = array(
							'Album' => array(
								'artist_id' => $this->data['Song']['artist_id'],
								'name' => trim($this->data['Song']['album'])
							)
						);
						
						$this->Album->set($album);
						if ($this->Album->validates()) {
							$this->Album->create();
							$this->Album->save($album);
							$this->data['Song']['album_id'] = $this->Album->id;
							unset($this->data['Song']['album']);
						} else {
							$error_message = $this->Album->invalidFields();
						}
					}
				}
			}
			
			//get host
			if (is_null($error_message)) {
				$this->loadModel('Host');
				$host = trim($this->data['Song']['link']);
				//parse url
				//strip out http://
				if (strpos($host, 'http://')===0) {
					$host = substr($host, 7);
				} else if (strpos($host, 'https://')===0) {
					$host = substr($host, 8);
				}
				//strip out www.
				if (strpos($host, 'www.')===0) {
					$host = substr($host, 3);
				}
				//strip out junk after root domain
				$pos = strpos($host, '/');
				if ($pos!==false) {
					$host = substr($host, 0, $pos);
				}
				//find root domain
				$host = explode('.', $host);
				$host = $host[count($host)-2].'.'.$host[count($host)-1];
				$our_host = $this->Host->findByUrl($host);
				if ($our_host!=null) {
					$this->data['Song']['host_id'] = $our_host['Host']['id'];
				} else {
					$data = array(
						'Host' => array(
							'url' => $host
						)
					);
					
					$this->Host->create();
					$this->Host->save($data);
					if ($this->Host->id!=null) {
						$this->data['Song']['host_id'] = $this->Host->id;
					} else {
						$error_message = 'An unknown error occurred.';
					}
				}
			}
			
			//get genres
			$song_genres = array();
			if (is_null($error_message)) {
				foreach($this->data['Song'] as $key=>$piece) {
					if (stristr($key, 'genre_')) {
						if ($piece!=0) {
							$song_genres[] = $piece;
						}
						unset($this->data['Song'][$key]);
					}
				}
			}
			
			if (is_null($error_message)) {
				$this->data['Song']['user_id'] = $this->Session->read('user_id');
				$this->data['Song']['created'] = date('Y-m-d H-i-s');
				$this->data['Song']['name'] = trim($this->data['Song']['name']);
				$this->data['Song']['link'] = trim($this->data['Song']['link']);
				$this->Song->set($this->data);
				if ($this->Song->validates()) {
					$this->Song->create();
					$this->Song->save($this->data, array('validate' => false));
					
					//add genres
					if (!empty($this->Song->id)) {
						$this->loadModel('GenresSong');
						foreach($song_genres as $genre_id) {
							$data = array(
								'GenresSong' => array(
									'song_id' => $this->Song->id,
									'genre_id' => $genre_id
								)
							);
							$this->GenresSong->create();
							$this->GenresSong->save($data);
						}
					}
					
					//update twitter
					
					//shorten link with bit.ly
					$bitly_socket = new HttpSocket();
					$short_url = $bitly_socket->get('http://api.bit.ly/v3/shorten?login=sandmusicca&apiKey=XXXXXXXXXXXXXXXX&longUrl=http://sandmusic.ca/songs/index/song:'.$this->Song->id.'&format=txt');
					
					if (!empty($artist['Artist']['twitter'])) {
						$twitter_artist = '@'.$artist['Artist']['twitter'];
					} else {
						$twitter_artist = $artist['Artist']['name'];
					}
					
					$twitter_status = $this->data['Song']['name'].' by '.$twitter_artist.' '.$short_url;
					 
					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
					$content = $connection->get('account/verify_credentials');
					$connection->post('statuses/update', array('status' => $twitter_status));
				} else {
					$this->set('error_message', $this->Song->invalidFields());
				}
			} else {
				$this->set('error_message', $error_message);
			}
		}
		
	}
	
	function like_song($song_id) {
		$info = $this->_init();
		$this->set('page_title', $info['title_prefix'].' - Like Song');

		if (!$info['signed_in']) {
			die('Not authorized');
		}
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if (isset($song_id) && $song_id!=0) {
			$user_id = $this->Session->read('user_id');
			
			$this->loadModel('Like');
			
			$find_params = array(
				'conditions' => array(
					'user_id' => $user_id,
					'song_id' => $song_id
				),
				'fields' => array(
					'id'
				)
			);
			
			$like = $this->Like->find('first', $find_params);
			
			if ($like==null) {
				$song = $this->Song->findById($song_id);
				if ($song!=null) {
					$likes = $song['Song']['likes'];
					$likes++;
					$data = array(
						'Song' => array(
							'likes' => $likes
						)
					);
					
					$this->Song->id = $song_id;
					$this->Song->save($data);
					
					$data = array(
						'Like' => array(
							'user_id' => $user_id,
							'song_id' => $song_id
						)
					);
					
					$this->Like->create();
					$this->Like->save($data);
					$fb_share = $this->Session->read('fb_share');
					$this->set('response', json_encode(array($likes,$fb_share,$song['Song']['name'],$song['Artist']['name'])));
				} else {
					$this->set('response', 0);
				}
			} else {
				$this->set('response', 0);
			}
		} else {
			$this->set('response', 0);
		}
	}
	
	function mark_as_dead($id=null) {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			die('Not authorized');
		}
		
		$this->layout = 'ajax';
		if ($id!=null) {
			$this->Song->id = $id;
			$data = array(
				'Song' => array(
					'dead' => 1
				)
			);
			
			$this->Song->save($data);			
		}
	}
	
	function update_play_count($id=null) {
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		} else {
			die('Not authorized');
		}
		
		if ($id!=null) {
			$song = $this->Song->findById($id);
			if ($song!=null) {
				$plays = $song['Song']['plays'];
				$this->Song->id = $id;
				$this->Song->saveField('plays', ++$plays);
				$this->set('response', $plays);
				
				//update twitter
				$freq = 10;
				$age = (time() - strtotime($song['Song']['created']))/86400;
				if ($age>14) {
					$freq = $freq*3-$age;
					if ($freq<0) {
						$freq = 0;
					}
				}
				
				if ($plays%$freq==0) {
					//load twitter API
					App::import('Vendor', 'twitteroauth/twitteroauth');

					//shorten link with bit.ly
					$bitly_socket = new HttpSocket();
					$short_url = $bitly_socket->get('http://api.bit.ly/v3/shorten?login=sandmusicca&apiKey=XXXXXXXXXXXXXXXXXXXX&longUrl=http://sandmusic.ca/songs/index/song:'.$id.'&format=txt');
					
					if (!empty($song['Artist']['twitter'])) {
						$twitter_artist = '@'.$song['Artist']['twitter'];
					} else {
						$twitter_artist = $song['Artist']['name'];
					}
					
					$twitter_status = '#NP '.$song['Song']['name'].' by '.$twitter_artist.' '.$short_url;
					 
					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
					$content = $connection->get('account/verify_credentials');
					$response = $connection->get('statuses/user_timeline', array('screen_name' => 'SandMusicCA', 'count' => 1));
					$r = get_object_vars($response[0]);
					if ((time()-strtotime($r['created_at']))>20*60) {
						$connection->post('statuses/update', array('status' => $twitter_status));
					}
				}
			}
			
		}
	}
	
	function get_info($id=null) {
		if (!$id) {
			$this->set('response', 0);
		} else {
			$bind_params = array(
				'belongsTo' => array(
					'Host',
					'User'
				)
			);
			
			$this->Song->bindModel($bind_params);
			
			$find_params = array(
				'conditions' => array(
					'Song.id' => $id
				),
				'fields' => array(
					'Song.name',
					'Song.likes',
					'Song.comments',
					'Artist.id',
					'Artist.name',
					'Artist.twitter',
					'Album.id',
					'Album.name',
					'Host.name',
					'Host.url',
					'User.id',
					'User.first_name',
					'User.last_name'
				)
			);
			$song = $this->Song->find('first', $find_params);
			
			//get follower info
			if (!$this->_signedIn()) {
				$song['Follow']['follow'] = -1;
			} else if ($song['User']['id']==$this->Session->read('user_id')) {
				$song['Follow']['follow'] = -1;
			} else {
				$this->loadModel('Follow');
				$find_params = array(
					'conditions' => array(
						'follower_id' => $this->Session->read('user_id'),
						'user_id' => $song['User']['id']
					)
				);
				$follows = $this->Follow->find('first', $find_params);
				if ($follows==null) {
					$song['Follow']['follow'] = 0;
				} else {
					$song['Follow']['follow'] = 1;
				}
			}
			
			if ($song==null) {
				$this->set('response', 0);
			} else {
				if ($song['Host']['name']=='') {
					$song['Host']['name']=$song['Host']['url'];
				}
				$this->set('response', json_encode($song));
			}
		}
	}
	
	function get_twitter_feed($id=null) {
		if (!$id) {
			$this->set('response', 0);
		} else {
			$song = $this->Song->findById($id);
			if ($song!=null) {
				$this->loadModel('Artist');
				$artist = $this->Artist->findById($song['Song']['artist_id']);
				$twitter = $artist['Artist']['twitter'];
			}
			
			if (!empty($twitter)) {
				//load twitter API
				App::import('Vendor', 'twitteroauth/twitteroauth');
				
				$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
				$content = $connection->get('account/verify_credentials');
				$max = 5;
				$response = $connection->get('statuses/user_timeline', array('screen_name' => $twitter, 'count' => $max));
				$tweets = array();
				$i=0;
				for($i=0; $i<=$max; $i++) {
					$r = get_object_vars($response[$i]);
					if ($r['text']==null) {
						break;
					}
					$tweets[$i]['text'] = $r['text'];
					$date = $this->_ago(strtotime($r['created_at']),-1,true);
					$tweets[$i]['screen_name'] = $twitter;
					
					$tweets[$i]['date'] = $date;
				}
				$this->set('response', json_encode($tweets));
			} else {
				$this->set('response', 0);
			}
		}
	}

}

?>