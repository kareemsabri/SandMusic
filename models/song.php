<?php
class Song extends AppModel {
	var $name = 'Song';
	var $belongsTo = array(
		'Album',
		'Artist',
		'Host',
		'User'
	);
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'song_name',
		),
		'link' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'song_link'
			),
			'rule2' => array(
				'rule' => array('isAudio'),
				'message' => 'song_link'
			)
		)
	);
	
	function isAudio($check) {
		$link = $check['link'];
		$type = substr($link, strlen($link)-3);
		if ($type!='mp3' && $type!='m4a') {
			return false;
		}
		return true;
	}
}
?>