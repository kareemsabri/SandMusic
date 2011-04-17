<?php
class Group extends AppModel {
	var $name = 'Group';
	var $order = 'name';
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'group_name',
		)
	);
}
?>