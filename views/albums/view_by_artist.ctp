<?php
	if (isset($ajax)) {
		echo '<option value="0">Album</option>';
		foreach($albums as $album) {
			echo '<option value="'.$album['Album']['id'].'">'.$album['Album']['name'].'</option>';
		}
	}
?>