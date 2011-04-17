<?php

/**************************************************************
Name: Artists Controller
File: /app/controllers/artists_controller.php
Created: September 25, 2010
Author: Kareem Sabri
**************************************************************/

Class ArtistsController extends AppController {

	function autocomplete() {
		$this->layout = 'ajax';
		
		$find_params = array(
			'conditions' => array(
				'name LIKE' => $this->data['Song']['artist'].'%'
			),
			'order' => array(
				'name'
			)
		);
		
		$this->set('artists', $this->Artist->find('list', $find_params));
		
	}
	
	function add_genre() {
		$artist_id = $this->params['named']['artist'];
		$genre_id = $this->params['named']['genre'];
		$artist = $this->Artist->findById($artist_id);
		$this->loadModel('Genre');
		$genre = $this->Genre->findById($genre_id);
		if ($genre!=null && $artist!=null) {
			$this->loadModel('GenresSong');
			$this->loadModel('Song');
			$songs = $this->Song->findAllByArtistId($artist['Artist']['id']);
			foreach($songs as $song) {
				//check if it already exists
				$find_params = array(
					'conditions' => array(
						'song_id' => $song['Song']['id'],
						'genre_id' => $genre['Genre']['id']
					)
				);
				
				$check = $this->GenresSong->find('first', $find_params);
				if ($check==null) {
					$data = array(
						'GenresSong' => array(
							'song_id' => $song['Song']['id'],
							'genre_id' => $genre['Genre']['id']
						)
					);
					$this->GenresSong->create();
					$this->GenresSong->save($data);
				}
			}
		}
	}

}

?>