<?php
	$cli_mode = true;
	require('header.php');

	/* This script updates the database with photos in the $fotodir
	   It sets old albums/photos to lost, sets re-found albums/photos to hidden
	   and inserts new albums/photos with visibility hidden.
	 */

	$albums = array();
	$photos = array();
	scan_gallery('');
	// now the $albums and $photos contain all albums/photos in the $fotodir

	// check each album
	$res = sql_query('SELECT name, path, visibility FROM fa_albums');
	while($row = mysql_fetch_assoc($res)) {
		// if the album in the db doesn't exist anymore, set it to lost.
		if(!isset($albums[$row['path']]) || !in_array($row['name'], $albums[$row['path']])) {
			sql_query("UPDATE fa_albums SET visibility='lost' WHERE name=%s AND path=%s", $row['name'], $row['path']);
		} else {
			unset($albums[$row['path']][array_search($row['name'], $albums[$row['path']])]);
			if($row['visibility'] == 'lost') {
				sql_query("UPDATE fa_albums SET visibility='hidden' WHERE name=%s AND path=%s", $row['name'], $row['path']);
			}
		}
	}
	// $albums now only contains new albums (not yet in the db)

	// insert new albums
	foreach($albums as $path=>$dirs) {
		foreach($dirs as $album) {
			if(isset($photos[$path . $album .'/']) || isset($albums[$path . $album .'/'])) {
				sql_query("INSERT INTO fa_albums (name, path) VALUES (%s, %s)", $album, $path);
			}
		}
	}

	// check each photo
	$res = sql_query('SELECT name, path, visibility FROM fa_photos');
	while($row = mysql_fetch_assoc($res)) {
		// if the photo in the db isn't in $fotodir anymore, set it to lost
		if(!isset($photos[$row['path']]) || !in_array($row['name'], $photos[$row['path']])) {
			sql_query("UPDATE fa_photos SET visibility='lost' WHERE name=%s AND path=%s", $row['name'], $row['path']);
		} else {
			unset($photos[$row['path']][array_search($row['name'], $photos[$row['path']])]);
			if($row['visibility'] == 'lost') {
				sql_query("UPDATE fa_photos SET visibility='hidden' WHERE name=%s AND path=%s", $row['name'], $row['path']);
			}
		}
	}
	// $photos now only contains new photos

	// Insert the remaining images
	foreach($photos as $path=>$dirs) {
		foreach($dirs as $photo) {
			// Extract EXIF data to determine rotation
			$exif = exif_read_data($fotodir.$path.$photo, 'IFD0');
			$raw_or = isset($exif['Orientation']) ?
					$exif['Orientation'] : 0;
			if($raw_or == 1 or $raw_or == 0)
				$or = 0;
			elseif($raw_or == 3)
				$or = 180;
			elseif($raw_or == 6)
				$or = 90;
			elseif($raw_or == 8)
				$or = 270;
			else {
				echo "\n";
				echo "Unknown orientation: " . $raw_or . "\n";
				echo "  (".$path.$photo."\n";
				$or = 0;
			}

			// Insert image into database
			if (!sql_query("INSERT INTO fa_photos (
					name,
					path,
					rotation)
				VALUES (%s, %s, %i)",
					$photo, $path, $or))
				die (mysql_error());
		}
	}
	require('footer.php');

	/* This recursively scans the $fotodir and stores the results in $albums and $photos */
	function scan_gallery($path) {
		global $fotodir, $albums, $photos, $extensions;
		echo 'scanning '.$path."\n";

		foreach(scandir($fotodir . $path) as $fn) {
			if($fn[0] == '.') {
				continue;
			}
			$subpath = $fotodir . $path . $fn;
			if(is_readable($subpath)) {
				if(is_dir($subpath)) {
					$albums[$path][] = $fn;
					scan_gallery($path . $fn .'/');
				} elseif(isset($extensions[strtolower(getext($fn))]) && $path != '') {
					echo 'found photo: '.$subpath."\n";
					$photos[$path][] = $fn;
				}
			}
		}
	}
?>
