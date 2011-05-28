<?php
	require('header.php');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		showTextAsImage("Missing parameter");
	}
	$row = getUnitByFullPath($_GET['foto'], UNIT_PHOTO);
	if(!$row) {
		header('HTTP/1.1 404 Not Found');
		showTextAsImage('Photo not found');
	}

	if(!in_array($row['visibility'], getVisibleVisibilities()) || !isPathVisible($row['path'])) {
		header('HTTP/1.1 403 Access denied');
		die('Access denied');
	}

	if(!$mime = $extensions[getext($row['name'])]) {
		showTextAsImage("Unknown extension");
	}

	$cachepath = $cachedir . $row['path'] . $row['name'] .'_large';

	if(!is_file($cachepath)) {
		header('HTTP/1.1 404 Not Found');
		showTextAsImage("Image not cached", 600);
	}
	header('Content-type: image/'. $mime);
	header('Pragma: public');
	header('Cache-Control: public');
	header('Last-Modified: '. gmdate('D, d M Y H:i:s', $fmt = filemtime($cachepath)) .' GMT');
	header('Expires: '. gmdate('D, d M Y H:i:s', time()+60*60).' GMT');
	header('Etag: '. md5($cachepath . $fmt));
	readfile($cachepath);
?>
