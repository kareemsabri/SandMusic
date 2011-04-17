<?php
class Album extends AppModel {
	var $name = 'Album';

	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'album_name',
		)
	);
}
?>