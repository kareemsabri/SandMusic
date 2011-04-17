<?php
class Playlist extends AppModel {
	var $name = 'Playlist';
	var $order = 'name';
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'playlist_name',
		)
	);
}
?>