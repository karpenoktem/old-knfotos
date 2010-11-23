<?php
	require('header.php');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		showTextAsImage("Missing parameter");
	}
	$res = mysql_query("SELECT * FROM fa_photos WHERE CONCAT(path, name)='". addslashes($_GET['foto']) ."'");
	if(!$row = mysql_fetch_assoc($res)) { 
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
