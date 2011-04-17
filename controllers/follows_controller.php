<?php

/**************************************************************
Name: Follows Controller
File: /app/controllers/follows_controller.php
Created: December 23, 2010
Author: Kareem Sabri
**************************************************************/

Class FollowsController extends AppController {
	
	function follow($followee_id=null) {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			die(0);
		}
		
		if (empty($followee_id)) {
			die(0);
		}
		
		$follower_id = $this->Session->read('user_id');
		
		if ($follower_id==$followee_id) {
			die(0);
		}
		
		//check if they already follow
		$find_params = array(
			'conditions' => array(
				'follower_id' => $follower_id,
				'user_id' => $followee_id
			)
		);
		
		$follow = $this->Follow->find('first', $find_params);		
		
		if ($follow==null) {
			$data = array(
				'Follow' => array(
					'follower_id' => $follower_id,
					'user_id' => $followee_id
				)
			);
			
			$this->Follow->create();
			$this->Follow->save($data);
			
			$this->set('response', 1);
			
		} else {
			$this->set('response', 1);
		}
	}
	
	function unfollow($followee_id=null) {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			die(0);
		}
		
		if (empty($followee_id)) {
			die(0);
		}
		
		$follower_id = $this->Session->read('user_id');
		
		$find_params = array(
			'conditions' => array(
				'follower_id' => $follower_id,
				'user_id' => $followee_id
			)
		);
		
		$follow = $this->Follow->find('first', $find_params);
		
		if ($follow!=null) {
			$this->Follow->delete($follow['Follow']['id']);
		}
	
		$this->set('response', 1);
	}
	
	function get_followers() {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			die(0);
		}
		
		$find_params = array(
			'conditions' => array(
				'follower_id' => $info['user_id']
			),
			'fields' => array(
				'user_id'
			)
		);
		
		$follows = $this->Follow->find('list', $find_params);
		
		$this->loadModel('User');
		
		$find_params =array(
			'conditions' => array(
				'id' => $follows
			),
			'fields' => array(
				'id',
				'handle',
				'first_name',
				'last_name'
			),
			'order' => 'handle'
		);
		
		$follows = $this->User->find('all', $find_params);
		
		$this->set('response', json_encode($follows));
	}
		
}

?>