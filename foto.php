<?php
	require('header.php');

	if(!isset($_GET['foto'])) {
		showTextAsImage("Missing parameter");
	}
	$res = mysql_query("SELECT * FROM fa_photos WHERE CONCAT(path, name)='". addslashes($_GET['foto']) ."'");
	if(!$row = mysql_fetch_assoc($res)) { 
		header('HTTP/1.1 404 Not found');
		showTextAsImage('Photo not found');
	}

	if(!in_array($row['visibility'], getVisibleVisibilities()) || !isPathVisible($row['path'])) {
		header('HTTP/1.1 403 Access denied');
		showTextAsImage('Access denied');
	}

	if(!$mime = $extensions[getext($row['name'])]) {
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
?>
