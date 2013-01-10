<?php
	require('header.php');
	require('media.php');

	// $photo may be an album
	$photo = getUnit(UNIT_BOTH);

	// if the thumbnail ($photo) turns out to be an album, get a random image from the albums
	if($photo['type'] == 'album') {
		$photo = getPhotoFromAlbum($photo);
	}
	// now $photo is really a photo

	$path = $cachedir . $photo['path'] . $photo['name'] .'_thumb';

	output($path);

	require('footer.php');

	function getPhotoFromAlbum ($album) {
		$res = sql_query("SELECT path, name FROM fa_albums WHERE path LIKE %s AND visibility IN (%S)", $album['path'] . $album['name'] .'/%', getVisibleVisibilities());
		$subalbums = array($album['path'] . $album['name'] .'/');
		while($subalbum = mysql_fetch_assoc($res)) {
			$subalbums[] = $subalbum['path'] . $subalbum['name'] .'/';
		}
		$res = sql_query("SELECT * FROM fa_photos WHERE path IN(%S) AND FIND_IN_SET('thumb', cached) AND visibility IN (%S) ORDER BY RAND() LIMIT 1", $subalbums, getVisibleVisibilities());
		if(!$photo= mysql_fetch_assoc($res)) { 
			header('HTTP/1.1 404 Not Found');
			showTextAsImage('No photo with thumbnail found for album');
		}
		return $photo;
	}
?>
