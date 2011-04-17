<?php
class Artist extends AppModel {
	var $name = 'Artist';

	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'artist_name',
		)
	);
}
?>