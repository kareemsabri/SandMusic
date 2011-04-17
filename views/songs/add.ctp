<?php
	if (isset($error_message)) {
		foreach($error_message as $error) {
			echo $error.'.';
		}
	}
?>