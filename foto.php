<?php
	// this file is almost the same as large.php
	require('header.php');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		showTextAsImage("Missing parameter");
	}
	$row = getUnitByFullPath($_GET['foto'], UNIT_PHOTO);
	if(!$row) {
		header('HTTP/1.1 404 Not found');
		showTextAsImage('Photo not found');
	}

	if(!in_array($row['visibility'], getVisibleVisibilities()) || !isPathVisible($row['path'])) {
		header('HTTP/1.1 403 Access denied');
		showTextAsImage('Access denied');
	}

	if(!$mime = $photoExtensions[getext($row['name'])]) {
		if(!$mime = $videoExtensions[getext($row['name'])]) {
			showTextAsImage("Unknown extension");
		}
	}

	$path = $fotodir . $row['path'] . $row['name'];

	if(!is_file($path)) {
		header('HTTP/1.1 404 Not found');
		showTextAsImage("Image not found");
	}

	// set filename when downloading
	header('Content-disposition: inline; filename='. $row['name']);

	if ($row['type'] == 'photo') {
		header('Content-type: image/'. $mime);
		header('Pragma: public');
		header('Cache-Control: public');
		header('Last-Modified: '. gmdate('D, d M Y H:i:s', $fmt = filemtime($path)) .' GMT');
		header('Expires: '. gmdate('D, d M Y H:i:s', time()+60*60).' GMT');
		header('Etag: '. md5($path . $fmt));
		readfile($path);
	} else {
		// video
		// This might not work when X-Sendfile is disabled
		header('Content-Type: video/'. $mime);
		header('X-Sendfile: '. $path);
	}
	require('footer.php');
?>
