<?php

/**************************************************************
Name: Playlists Controller
File: /app/controllers/playlists_controller.php
Created: September 26, 2010
Author: Kareem Sabri
**************************************************************/

Class PlaylistsController extends AppController {

	function add() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - New Playlist');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if (!empty($this->data)) {
			$this->data['Playlist']['user_id'] = $this->Session->read('user_id');
			$this->data['Playlist']['created'] = date('Y-m-d H:i:s');
			$this->data['Playlist']['modified'] = date('Y-m-d H:i:s');
			
			$this->Playlist->set($this->data);
			if ($this->Playlist->validates()) {
				$this->Playlist->create();
				$this->Playlist->save($this->data);
				$this->set('response', $this->Playlist->id);
				$this->Session->write('playlist_id', $this->Playlist->id);
			} else {
				$this->set('response', 0);
			}
		}
	}
	
	function add_song() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Add to Playlist');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if (isset($this->params['named']['playlist']) && isset($this->params['named']['song'])) {
			$playlist_id = $this->params['named']['playlist'];
			$playlist = $this->Playlist->findById($playlist_id);
			if ($playlist['Playlist']['user_id']==$this->Session->read('user_id')) {
				$this->loadModel('PlaylistsSong');
				$find_params = array(
					'conditions' => array(
						'playlist_id' => $playlist_id,
						'song_id' => $song_id
					),
					'fields' => array(
						'id'
					)
				);
				$song = $this->PlaylistsSong->find('first', $find_params);
				if ($song==null) {
					$this->loadModel('Song');
					$song = $this->Song->findById($this->params['named']['song']);
					if ($song!=null) {
						$data = array(
							'PlaylistsSong' => array(
								'playlist_id' => $playlist_id,
								'song_id' => $song['Song']['id'],
								'created' => date('Y-m-d H:i:s')
							)
						);
						
						$this->PlaylistsSong->create();
						$this->PlaylistsSong->save($data);
						
						echo $this->PlaylistsSong->id;
					}
				}
			}
			
		}
		
	}

	function remove_song() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Remove From Playlist');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if (isset($this->params['named']['playlist']) && isset($this->params['named']['song'])) {
			$playlist_id = $this->params['named']['playlist'];
			$song_id = $this->params['named']['song'];
			$playlist = $this->Playlist->findById($playlist_id);
			if ($playlist['Playlist']['user_id']==$this->Session->read('user_id')) {
				$this->loadModel('PlaylistsSong');
				$find_params = array(
					'conditions' => array(
						'playlist_id' => $playlist_id,
						'song_id' => $song_id
					),
					'fields' => array(
						'id'
					)
				);
				$song = $this->PlaylistsSong->find('first', $find_params);
				if ($song!=null) {
					$this->PlaylistsSong->delete($song['PlaylistsSong']['id']);
				}
			}
		}
	}
	
	function delete($id=null) {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Delete Playlist');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if ($id!=null) {
			$playlist = $this->Playlist->findById($id);
			if ($playlist['Playlist']['user_id']==$this->Session->read('user_id')) {
				$this->Playlist->delete($id);
				$this->loadModel('PlaylistsSong');
				$this->PlaylistsSong->deleteAll(array('playlist_id' => $id));
			}
		}
		
	}
	
	function get_playlists_by_user_id($user_id=null) {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Get Playlists');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		if ($user_id!=null && $user_id==$this->Session->read('user_id')) {
			$playlists = $this->Playlist->findAllByUserId($user_id);
			$response = json_encode($playlists);
			$this->set('response', $response);
		}	
	}
	
	function get_playlists() {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Get Playlists');
		
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
		}
		
		$playlists = $this->Playlist->find('all');
		$response = json_encode($playlists);
		$this->set('response', $response);
	}

}

?>