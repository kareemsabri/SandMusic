<?php

/**************************************************************
Name: Likes Controller
File: /app/controllers/likes_controller.php
Created: December 22, 2010
Author: Kareem Sabri
**************************************************************/

Class LikesController extends AppController {
	
	function get_likes($song_id) {
		if (empty($song_id)) {
			$this->set('response', 0);
		} else {
			$bind_params = array(
				'belongsTo' => array(
					'User'
				)
			);
			
			$this->Like->bindModel($bind_params);
			
			$find_params = array(
				'conditions' => array(
					'song_id' => $song_id
				),
				'fields' => array(
					'Like.created',
					'User.first_name',
					'User.last_name'
				),
				'order' => 'Like.created'
			);
			
			$likes = $this->Like->find('all', $find_params);
			
			$this->set('response', json_encode($likes));
		}
	}
}

?>