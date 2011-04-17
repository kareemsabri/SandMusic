<?php
class Comment extends AppModel {
	var $name = 'Comment';
	
	var $validate = array(
		'song_id' => array(
			'rule' => 'notEmpty',
			'message' => 'song_id',
		),		
		'comment' => array(
			'user_id' => 'notEmpty',
			'message' => 'user_id',
		),
		'comment' => array(
			'rule' => 'notEmpty',
			'message' => 'comment',
		)
	);
}
?>