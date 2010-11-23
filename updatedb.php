<?php
	$cli_mode = true;
	require('header.php');

	// XXX lost dingen recoveren

	$albums = array();
	$photos = array();
	scan_gallery('');

	$res = mysql_query('SELECT name, path FROM fa_albums');
	while($row = mysql_fetch_assoc($res)) {
		if(!in_array($row['name'], $albums[$row['path']])) {
			mysql_query("UPDATE fa_albums SET visibility='lost' WHERE name='". addslashes($row['name']) ."' AND path='". addslashes($row['path']) ."'");
		} else {
			unset($albums[$row['path']][array_search($row['name'], $albums[$row['path']])]);
		}
	}

	foreach($albums as $path=>$dirs) {
		foreach($dirs as $album) {
			if(isset($photos[$path . $album .'/']) || isset($albums[$path . $album .'/'])) {
				mysql_query("INSERT INTO fa_albums (name, path) VALUES ('". addslashes($album) ."', '". addslashes($path) ."')");
			}
		}
	}

	$res = mysql_query('SELECT name, path FROM fa_photos');
	while($row = mysql_fetch_assoc($res)) {
		if(!in_array($row['name'], $photos[$row['path']])) {
			mysql_query("UPDATE fa_photos SET visibility='lost' WHERE name='". addslashes($row['name']) ."' AND path='". addslashes($row['path']) ."'");
		} else {
			unset($photos[$row['path']][array_search($row['name'], $photos[$row['path']])]);
		}
	}

	foreach($photos as $path=>$dirs) {
		foreach($dirs as $photo) {
			mysql_query("INSERT INTO fa_photos (name, path) VALUES ('". addslashes($photo) ."', '". addslashes($path) ."')");
		}
	}

	function scan_gallery($path) {
		global $fotodir, $albums, $photos, $extensions;

		echo str_repeat(79, ' ') . chr(13) . $path . chr(13);

		foreach(scandir($fotodir . $path) as $f) {
			if($f[0] == '.') {
				continue;
			}
			if(true || is_readable($fotodir . $path . $f)) {
				if(is_dir($fotodir . $path . $f)) {
					$albums[$path][] = $f;
					scan_gallery($path . $f .'/');
				} elseif(isset($extensions[getext($f)]) && $path != '') {
					$photos[$path][] = $f;
				}
			}
		}
	}
?>
