<?php

	function getUnit ($flags) {
		if(!isset($_GET['foto'])) {
			header('HTTP/1.1 400 Bad Request');
			showTextAsImage("Missing parameter");
		}
		$row = getUnitByFullPath($_GET['foto'], $flags);
		if(!$row) {
			header('HTTP/1.1 404 Not Found');
			showTextAsImage('Photo not found');
		}

		if(!in_array($row['visibility'], getVisibleVisibilities()) || !isPathVisible($row['path'])) {
			header('HTTP/1.1 403 Access denied');
			showTextAsImage('Access denied');
		}
		return $row;
	}

	function output ($path) {
		if(!is_file($path)) {
			header('HTTP/1.1 404 Not Found');
			showTextAsImage("Image not cached", 600);
		}
		header('Content-type: '. finfo_file(finfo_open($path), FILEINFO_MIME));
		header('X-Sendfile: '. $path);
	}
