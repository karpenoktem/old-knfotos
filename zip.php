<?php

	require('header.php');
	require('media.php');

	if (!isset($_GET['album'])) {
		die('No album specified.');
	}
	$album = $_GET['album'];
	if (substr($album, -1) != '/') {
		$album .= '/';
	}

	$dbname = basename($album);
	$dbpath = dirname($album);
	if (substr($dbpath, -1) != '/')
		$dbpath .= '/';
	if ($dbpath == './')
		$dbpath = '';

	// check if it's allowed to download this album
	$visibilities = getVisibleVisibilities();
	$res = sql_query("SELECT id,humanname FROM fa_albums WHERE path=%s
		AND name=%s AND visibility IN (%S)
		", $dbpath, $dbname, $visibilities);
	$row_album = mysql_fetch_assoc($res);
	if (!$row_album)
		die("Album not found (or you're not allowed to see it)");

	// give the album a good name, even if it's not there
	$humanname = $row_album['humanname'];
	if (!$humanname)
		$humanname = trim($album, '/.');
	$humanname = str_replace('/', '.', $humanname);

	// zip source location
	$srclocation   = realpath($fotodir  .'/'. $album);
	$cachelocation = realpath($cachedir .'/'. $album);

	// get paths of all (sub-)albums
	$albums = array($album);
	for ($i=0; $i<count($album); $i++) {
		$path = $albums[$i];
		$res = sql_query("SELECT name FROM fa_albums WHERE path=%s
			 AND visibility IN (%S)
			", $path, $visibilities);
		while ($row = mysql_fetch_assoc($res)) {
			$albums[] = $path . $row['name'] .'/';
		}
	}

	// get all image files
	$files = array();
	foreach ($albums as $path) {
		$res = sql_query("SELECT path,name FROM fa_photos WHERE path=%s
			AND visibility IN (%S)", $path, $visibilities);
		while ($row = mysql_fetch_assoc($res)) {
			$files[] = substr($path, strlen($album)) . $row['name'];
		}
	}

	// build the zip command for streaming the file
	$zipcmd = 'zip -r0 -';
	foreach ($files as $file) {
		$zipcmd .= ' '. escapeshellarg($file);
	}

	header('Content-type: application/zip');
	sendDispositionHeader('attachment', $humanname .'.zip');
	chdir($srclocation);
	passthru($zipcmd); // zip data and stream data

