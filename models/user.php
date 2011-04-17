<?php
class User extends AppModel {
	var $name = 'User';
	
	var $validate = array(
		'username' => array(
			'rule1' => array(
				'rule' => 'isUnique',
				'message' => 'That email address is already registered.'
			),
			'rule2' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid email address.'			
			)
		),
		'first_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter your first name.',
		),
		'last_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter your last name.',
		),
		'handle' => array(
			'rule1' => array(
				'rule' => 'isUnique',
				'message' => 'That username is already registered.'
			),
			'rule2' => array(
				'rule' => 'notEmpty',
				'message' => 'Please select a username.'			
			)
		)
	);
}
?>