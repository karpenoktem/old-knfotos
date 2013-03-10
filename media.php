<?php

	/* Get current unit (album/photo/video),
	   wrapper for getUnitByFullPath().
	 */
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

	function sendDispositionHeader ($disposition, $name) {
		// see http://stackoverflow.com/questions/93551/how-to-encode-the-filename-parameter-of-content-disposition-header-in-http
		if ($name)
			// send real filename instead of php file name
			// In standards-compilant browsers, this gives the right filename.
			// In other browsers (IE < 9, Safari), it gives %-escaped characters in the URL (or just zip.php)
			// So, I don't think this is a real issue (browsers will be updated)
			header("Content-disposition: $disposition; filename=". urlencode($name) ."; filename*=UTF-8''". urlencode($name));
		else if ($disposition == 'attachment') // don't send with disposition == 'inline'
			header("Content-disposition: attachment");
	}

	/* Send a file to client.
	   $path is the path of the file, $name is the original name of the file.
	 */
	function output ($path, $name=NULL) {
		if(!is_file($path)) {
			header('HTTP/1.1 404 Not Found');
			showTextAsImage("Image not cached", 600);
		}

		$mime = finfo_file(finfo_open(), $path, FILEINFO_MIME_TYPE);
		// hack, as long as webm isn't supported by the magic database, this is needed
		if (isset($_GET['codec']) && $_GET['codec'] == 'webm') {
			$mime = 'video/webm';
		}

		sendDispositionHeader('inline', $name);
		header('Content-type: '. $mime);
		header('X-Sendfile: '. $path);
	}
