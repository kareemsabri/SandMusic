<?php

/**************************************************************
Name: Users Controller
File: /app/controllers/users_controller.php
Created: September 25, 2010
Author: Kareem Sabri
**************************************************************/

Class UsersController extends AppController {
	var $components = array('SwiftMailer');
	var $paginate = array(
		'limit' => 30,
		'conditions' => array(
			'privacy' => 0
		),
		'order' => array(
			'User.handle'
		)
	);
	
	function _makePassword($password) {
		$salt = 'XXXXXXXXXXXXXXXXXXXXX'; // DO NOT EVER CHANGE THIS!!!!!!!
		return sha1($password.'~'.$salt);
	}
		
	function settings() {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			$this->redirect('/');
		}
		
		if (empty($this->data)) {
			$this->data = $this->User->findById($this->Session->read('user_id'));
		} else {
			$this->User->set($this->data);
			if ($this->User->validates()) {
				$this->User->id = $this->Session->read('user_id');
				$this->User->save($this->data);
				$this->_doLogin($this->Session->read('user_id'));
			}
		}
	}
	
	function facebook_login() {
		App::import('Core', 'HttpSocket');
		$cookie = $this->_get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);	
		if ($cookie!=null) {
			$facebook_socket = new HttpSocket();
			$facebook_info = $facebook_socket->request('https://graph.facebook.com/'.$cookie['uid'].'?access_token='.$cookie['access_token']);
			$fb_user = json_decode($facebook_info);
			
			//check if user has previously logged in with facebook
			$user = $this->User->findByFbId($fb_user->id);
			
			if ($user!=null) {
				if ($this->Session->read('user_id')!=$user['User']['id']) {
					$this->Session->destroy();
					$this->_doLogin($user['User']['id']);
					$this->set('response', 1);				
				} else {
					$this->set('response', 0);
				}
			} else {
				//create new account
				$data = array(
					'User' => array(
						'username' => $fb_user->email,
						'first_name' => $fb_user->first_name,
						'last_name' => $fb_user->last_name,
						'handle' => $fb_user->first_name.substr($fb_user->last_name,0,1),
						'fb_id' => $fb_user->id,
						'logged_in' => date('Y-m-d H:i:s'),
						'verified' => date('Y-m-d')
					)
				);
				
				$this->User->create();
				$this->User->save($data);
				$this->_doLogin($this->User->id);
				$this->set('response', 3);
			}
		} else {
			$this->set('response', 0);
		}
	}
	
	function facebook_logout() {
		$this->Session->destroy();
		$this->Cookie->destroy();
		$this->set('response', 1);
	}
	
}

?>