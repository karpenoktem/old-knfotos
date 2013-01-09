<?php
	require('header.php');

	if(!isset($_GET['foto'])) {
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
		showTextAsImage("Unknown extension");
	}

	$path = $fotodir . $row['path'] . $row['name'];

	if(!is_file($path)) {
		header('HTTP/1.1 404 Not found');
		showTextAsImage("Image not found");
	}
	header('Content-type: image/'. $mime);
	header('Pragma: public');
	header('Cache-Control: public');
	header('Last-Modified: '. gmdate('D, d M Y H:i:s', $fmt = filemtime($path)) .' GMT');
	header('Expires: '. gmdate('D, d M Y H:i:s', time()+60*60).' GMT');
	header('Etag: '. md5($path . $fmt));
	readfile($path);
	require('footer.php');
?>
