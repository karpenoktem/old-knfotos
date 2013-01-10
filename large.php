<?php
	require('header.php');
	require('media.php');

	$media = getUnit(UNIT_PHOTO);

	if ($media['type'] == 'photo') {
		$path = $cachedir . $media['path'] . $media['name'] .'_large';
	} else {
		if(!$mime = $videoExtensions[$_GET['codec']]) {
			header('HTTP/1.1 400 Bad Request');
		}
		$path = $cachedir . $media['path'] . $media['name'] .'_'. intval($_GET['res']) .'p.'. $mime;
	}

	output($path);

	require('footer.php');
?>
