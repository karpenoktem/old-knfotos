<?php
	require('header.php');
	require('media.php');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		showTextAsImage("Missing parameter");
	}


	if ($row['type'] == 'photo') {
		$path = $cachedir . $row['path'] . $row['name'] .'_large';
	} else {
		if(!$mime = $videoExtensions[$_GET['codec']]) {
			header('HTTP/1.1 400 Bad Request');
		}
		$path = $cachedir . $row['path'] . $row['name'] .'_'. intval($_GET['res']) .'p.'. $mime;
	}

	output($path);

	require('footer.php');
?>
