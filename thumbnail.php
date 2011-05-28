<?php
	require('header.php');
	# showTextAsImage('temporary disabled for performance');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		showTextAsImage("Missing parameter");
	}
	$row = getUnitByFullPath($_GET['foto'], UNIT_BOTH);
	if(!$row) {
		header('HTTP/1.1 404 Not Found');
		showTextAsImage('Photo/album not found');
	}

	if(!in_array($row['visibility'], getVisibleVisibilities()) || !isPathVisible($row['path'])) {
		header('HTTP/1.1 403 Access denied');
		die('Access denied');
	}

	if($row['type'] == 'album') {
		$res = mysql_query("SELECT * FROM fa_photos WHERE path LIKE '". addslashes($row['path'] . $row['name'] .'/') ."%' AND FIND_IN_SET('thumb', cached) LIMIT 1");
		if(!$row = mysql_fetch_assoc($res)) { 
			header('HTTP/1.1 404 Not Found');
			showTextAsImage('No photo with thumbnail found for album');
		}
	}

	if(!$mime = $extensions[getext($row['name'])]) {
		showTextAsImage("Unknown extension");
	}

	$cachepath = $cachedir . $row['path'] . $row['name'] .'_thumb';

	if(!is_file($cachepath)) {
		header('HTTP/1.1 404 Not Found');
		showTextAsImage("No thumbnail");
	}
	header('Content-type: image/'. $mime);
	header('Pragma: public');
	header('Cache-Control: public');
	header('Last-Modified: '. gmdate('D, d M Y H:i:s', $fmt = filemtime($cachepath)) .' GMT');
	header('Expires: '. gmdate('D, d M Y H:i:s', time()+60*60).' GMT');
	header('Etag: '. md5($cachepath . $fmt));
	readfile($cachepath);
?>
