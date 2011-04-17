<?php

/**************************************************************
Name: Albums Controller
File: /app/controllers/albums_controller.php
Created: September 25, 2010
Author: Kareem Sabri
**************************************************************/

Class AlbumsController extends AppController {

	function autocomplete() {
		$this->layout = 'ajax';
		
		$find_params = array(
			'conditions' => array(
				'name LIKE' => $this->data['Song']['album'].'%'
			),
			'order' => array(
				'name'
			)
		);
		
		$this->set('albums', $this->Album->find('list', $find_params));
		
	}
	
	function view_by_artist($id=null) {
		if ($this->RequestHandler->isAjax()) {
			$this->layout = 'ajax';
			$this->set('ajax', true);
			if ($id==null) {
				$this->set('albums', array());
			} else {
				$find_params = array(
					'conditions' => array(
						'artist_id' => $id
					),
					'fields' => array(
						'id',
						'name'
					),
					'order' => array(
						'name'
					)
				);
				
				$this->set('albums', $this->Album->find('all', $find_params));
			}
		}
	}

}

?>