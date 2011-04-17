<?php

/**************************************************************
Name: Comments Controller
File: /app/controllers/comments_controller.php
Created: October 1, 2010
Author: Kareem Sabri
**************************************************************/

Class CommentsController extends AppController {
	var $components = array('SwiftMailer');
	
	function _sendCommentNotificationEmail($user_id, $song_id, $comment_body) {
		$find_params = array(
			'conditions' => array(
				'user_id <>' => $user_id,
				'song_id' => $song_id
			),
			'fields' => array(
				'DISTINCT user_id'
			)
		);
		
		$comments = $this->Comment->find('all', $find_params);
		
		$this->loadModel('User');
		$commenter = $this->User->findById($user_id);
		 
		$this->loadModel('Song');
		$song = $this->Song->findById($song_id);
		
		if ($song['Song']['user_id']==$user_id) {
			$song_owner = true;
		} else {
			$song_owner = false;
		}
		
		foreach($comments as $comment) {
	        $user = $this->User->findById($comment['Comment']['user_id']);
	        
	        if ($user['User']['id']==$song['Song']['user_id']) {
	        	$song_owner = true;
	        }
	        
	        if ($user!=null && $user['User']['notifications']==1) {
		        $this->SwiftMailer->smtpType = 'tls'; 
		        $this->SwiftMailer->smtpHost = 'smtp.gmail.com'; 
		        $this->SwiftMailer->smtpPort = 465;
		        $this->SwiftMailer->sendAs = 'both'; 
		        $this->SwiftMailer->from = 'notifications@sandmusic.ca'; 
		        $this->SwiftMailer->fromName = 'SandMusic'; 
		        $this->SwiftMailer->smtpUsername = 'notifications@sandmusic.ca'; 
		        $this->SwiftMailer->smtpPassword = 'XXXXXXXXXXXXXXXXXXXXX';	        
		        $this->SwiftMailer->to = $user['User']['username'];
		        $this->set('handle', $commenter['User']['handle']);
		        $this->set('song', $song['Song']['name']);
		        $this->set('comment', $comment_body);
		        $this->set('song_id', $song_id);
		        try {
		        	$this->SwiftMailer->send('new_comment', $commenter['User']['handle']. ' also commented on a song.');
		        } catch(Exception $e) {
		        	$this->log('Error sending email: '.$e->getMessage());
		        }
			}
		}
		
		if ($song_owner==false) {
			$user = $this->User->findById($song['Song']['user_id']);
	        
	        if ($user!=null && $user['User']['notifications']==1) {
		        $this->SwiftMailer->smtpType = 'tls'; 
		        $this->SwiftMailer->smtpHost = 'smtp.gmail.com'; 
		        $this->SwiftMailer->smtpPort = 465;
		        $this->SwiftMailer->sendAs = 'both'; 
		        $this->SwiftMailer->from = 'verification@sandmusic.ca'; 
		        $this->SwiftMailer->fromName = 'SandMusic'; 
		       	$this->SwiftMailer->smtpUsername = 'verification@sandmusic.ca'; 
		        $this->SwiftMailer->smtpPassword = 'XXXXXXXXXXXXXXXXXXXXX'; 	        
		        $this->SwiftMailer->to = $user['User']['username'];
		        $this->set('handle', $commenter['User']['handle']);
		        $this->set('song', $song['Song']['name']);
		        $this->set('comment', $comment_body);
		        $this->set('song_id', $song_id);
		        try {
		        	$this->SwiftMailer->send('new_comment', $commenter['User']['handle']. ' commented on your song.');
		        } catch(Exception $e) {
		        	$this->log('Error sending email: '.$e->getMessage());
		        }
			}			
		}
	}
	
	
	function get_comments($song_id=null) {
		$info = $this->_init();
		$this->set('title_for_layout', $info['title_prefix'].' - Comments');
		
		if ($this->RequestHandler->isAjax()) { 
			$this->layout = 'ajax';
			$this->set('ajax', true);
		} else {
			$this->set('ajax', false);
		}
		
		if ($this->params['named']['update']=='true') {
			$this->set('update', true);
		}
		
		if ($song_id!=null) {
			$this->loadModel('Song');
			$song = $this->Song->findById($song_id);
				if ($song!=null) {
					$this->set('song', $song);
					$this->set('song_id', $song_id);
					$bind_params = array(
						'belongsTo' => array(
							'User'
						)
					);
					
					$this->Comment->bindModel($bind_params);
					
					$find_params = array(
						'conditions' => array(
							'song_id' => $song_id
						),
						'fields' => array(
							'Comment.*',
							'User.first_name',
							'User.last_name'
						),
						'order' => array(
							'Comment.created'
						)
					);
					
					$comments = $this->Comment->find('all', $find_params);
					foreach($comments as $key=>$comment) {
						$comments[$key]['Comment']['created'] = $this->_ago($comment['Comment']['created']);
					}
					$this->set('comments', json_encode($comments));
			}
		}
	}
	
	function add($song_id=null) {
		$info = $this->_init();
		
		if (!$info['signed_in']) {
			die('Not authorized');
		}		
		
		if ($this->RequestHandler->isAjax()) { 
			$this->layout = 'ajax';
		} else {
			die('Not authorized');
		}		
		
		if (!empty($song_id)) {
			$this->set('song_id', $song_id);
		} else {
			die('Not authorized');
		}
		
		if (!empty($this->data)) {
			$this->set('submit', true);
			$this->data['Comment']['user_id'] = $this->Session->read('user_id');
			$this->Comment->set($this->data);
			if ($this->Comment->validates()) {
				$this->loadModel('Song');
				$song = $this->Song->findById($this->data['Comment']['song_id']);
				if ($song!=null) {
					$this->Comment->create();
					$this->Comment->save($this->data);
						$count = $song['Song']['comments']+1;
						$data = array(
							'Song' => array(
								'comments' => $count
							)
						);
				
						$this->Song->id = $song['Song']['id'];
						$this->Song->save($data);
						$this->set('response', $count);
						
						//notify other users of comment
						$this->_sendCommentNotificationEmail($this->data['Comment']['user_id'], $this->data['Comment']['song_id'], $this->data['Comment']['comment']);						
				}
			}
		} else {
			$this->set('submit', false);
		}
	}
	
}