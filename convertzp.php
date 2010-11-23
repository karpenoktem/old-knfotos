<?php
	$cli_mode = true;
	require('header.php');

	$zpAlbums = array();
	$res = mysql_query('SELECT id, parentid, folder, title, `show` FROM zp_albums ORDER BY id');
	while($row = mysql_fetch_assoc($res)) {
		$row['name'] = basename($row['folder']);
		$zpAlbums[$row['id']] = $row;
	}

	do {
		$done = true;
		foreach($zpAlbums as &$album) {
			if(!$album['parentid']) {
				$album['path'] = '';
			} elseif(isset($zpAlbums[$album['parentid']]['path'])) {
				$album['path'] = $zpAlbums[$album['parentid']]['path'] . $zpAlbums[$album['parentid']]['name'] .'/';
			} else {
				echo "Taking another pass for ". $album['id'] ." (". $album['folder'] .")\n";
				$done = false;
			}
		}
		unset($album);
	} while(!$done);

	mysql_query("UPDATE fa_albums SET humanname=NULL");
	foreach($zpAlbums as $album) {
		mysql_query("UPDATE fa_albums SET humanname='". addslashes($album['title']) ."' WHERE path='". addslashes($album['path']) ."' AND name='". addslashes($album['name']) ."'");
		if($album['show']) {
			mysql_query("UPDATE fa_albums SET visibility='world' WHERE path='". addslashes($album['path']) ."' AND name='". addslashes($album['name']) ."' AND visibility='hidden'");
		}
	}

	$zpPhotos = array();
	$zpPhotoPaths = array();
	$res = mysql_query('SELECT id, albumid, filename, EXIFOrientation FROM zp_images');
	while($row = mysql_fetch_assoc($res)) {
		if(!isset($zpAlbums[$row['albumid']])) {
			// Lost image?
			continue;
		}

		$row['path'] = $zpAlbums[$row['albumid']]['path'] . $zpAlbums[$row['albumid']]['name'] .'/';
		$zpPhotos[$row['id']] = $row;
		$zpPhotoPaths[$row['path'] . $row['filename']] = $row['id'];
	}

	$res = mysql_query('SELECT id, name, path FROM fa_photos');
	while($row = mysql_fetch_assoc($res)) {
		if(isset($zpPhotoPaths[$row['path'] . $row['name']])) {
			$zpPhotos[$zpPhotoPaths[$row['path'] . $row['name']]]['photo_id'] = $row['id'];
		} else {
			echo $row['path'] . $row['name'] ."\n";
		}
	}

	$rotations = array();
	foreach($zpPhotos as &$row) {
		if(!isset($row['photo_id'])) {
			unset($zpPhotos[$row['id']]);
			continue;
		}
		switch($row['EXIFOrientation']) {
			case '6':
			case '6: 90 deg CCW':
			case '6: 90 graden linksom':
				$row['rotation'] = 270;
				break;

			case '8':
			case '8: 90 deg CW':
			case '8: 90 graden rechtsom':
				$row['rotation'] = 90;
				break;

			case NULL:
			case '1: Normaal (0 graden)':
			case '1: Normal (0 deg)':
			default:
				$row['rotation'] = 0;
				continue;
				break;
		}

		$rotations[$row['rotation']][] = $row['photo_id'];
	}
	unset($row);

	foreach($rotations as $rotation => $ids) {
		echo "Rotation ". $rotation .": ". count($ids) ." images\n";
		mysql_query("UPDATE fa_photos SET cached=IF(rotation = ". intval($rotation) .", cached, CONCAT(cached, ',invalidated')), rotation=". intval($rotation) ." WHERE id IN(". implode(',', $ids) .")");
		var_dump(mysql_error());
	}

	$knUsers = array();
	$res = mysql_query('SELECT username FROM kn_site.auth_user');
	while($row = mysql_fetch_assoc($res)) {
		$knUsers[] = $row['username'];
	}

	$zpTags = array();
	$zpTagToUser = array();
	$res = mysql_query('SELECT * FROM zp_tags WHERE id IN (SELECT DISTINCT tagid FROM zp_obj_to_tag)');
	while($row = mysql_fetch_assoc($res)) {
		$zpTags[$row['id']] = $row['name'];
		switch($row['name']) {
			case 'Leden':
				$zpTagToUser[$row['id']] = '*leden';
				break;
			case '_verborgen_1':
			case '_verborgen_2':
			case 'Verborgen1':
				$zpTagToUser[$row['id']] = 'marlies';
				break;
			case 'Bart. Stan':
				$zpTagToUser[$row['id']] = 'bart,stan';
				break;
			case 'Paul. Felix':
				$zpTagToUser[$row['id']] = 'paul,felix';
				break;
			case 'Daniel. Iris':
				$zpTagToUser[$row['id']] = 'daniel,iris';
				break;
			case 'Daniel. Paul':
				$zpTagToUser[$row['id']] = 'daniel,paul';
				break;
			case 'Paul. Iris':
				$zpTagToUser[$row['id']] = 'paul,iris';
				break;
			case 'Abel. Alysha':
				$zpTagToUser[$row['id']] = 'abel,alysha';
				break;
			case 'Paul. Carlien':
				$zpTagToUser[$row['id']] = 'paul,carlien';
				break;
			case 'Manuel. Jille':
				$zpTagToUser[$row['id']] = 'manuel,jille';
				break;
			case 'Michiel. Felix':
				$zpTagToUser[$row['id']] = 'michiel,felix';
				break;
			case 'Jillie':
				$zpTagToUser[$row['id']] = 'jille';
				break;
			case 'Sophie Robin':
				$zpTagToUser[$row['id']] = 'sophie,robin';
				break;
			case 'Bente Kristan':
				$zpTagToUser[$row['id']] = 'bente';
				break;
			case 'Joene':
			case 'Joenne':
				$zpTagToUser[$row['id']] = 'jeroen';
				break;
			case 'PP2':
				$zpTagToUser[$row['id']] = 'ppr';
				break;
			case 'Sopie':
				$zpTagToUser[$row['id']] = 'sophie';
				break;
			case 'Samatha':
				$zpTagToUser[$row['id']] = 'samantha';
				break;
			case 'Guido':
				$zpTagToUser[$row['id']] = 'giedo';
				break;
			case 'Hanna':
				$zpTagToUser[$row['id']] = 'hannah';
				break;
			case 'Jurien':
				$zpTagToUser[$row['id']] = 'jurrien';
				break;
			case 'Loes':
				$zpTagToUser[$row['id']] = 'loesje';
				break;
			case 'Rober':
			case 'Robbert':
				$zpTagToUser[$row['id']] = 'robert';
				break;
			case 'Harrie':
				$zpTagToUser[$row['id']] = 'barts';
				break;
			case 'Yurr':
			case 'Jurre':
				$zpTagToUser[$row['id']] = 'yurre';
				break;
			case 'Marolijn':
				$zpTagToUser[$row['id']] = 'marjolijn';
				break;
			case 'Yasmin':
				$zpTagToUser[$row['id']] = 'jasmin';
				break;
			case 'Mik':
				$zpTagToUser[$row['id']] = 'mikel';
				break;
			case 'Anette':
				$zpTagToUser[$row['id']] = 'annette';
				break;
			case 'Ellke':
				$zpTagToUser[$row['id']] = 'elleke';
				break;
			case 'Feli':
				$zpTagToUser[$row['id']] = 'felix';
				break;
			case 'Kris':
				$zpTagToUser[$row['id']] = 'chris';
				break;
			case 'Myrhte':
				$zpTagToUser[$row['id']] = 'myrthe';
				break;
			case 'Bente. Kristan':
				$zpTagToUser[$row['id']] = 'bente';
				break;
			case 'Michiel. Bas':
				$zpTagToUser[$row['id']] = 'michiel,bas';
				break;
			case 'Robn':
				$zpTagToUser[$row['id']] = 'robin';
				break;
			case 'Bente Chaim':
				$zpTagToUser[$row['id']] = 'bente,chaim';
				break;
			case 'Yurre. Noelle':
				$zpTagToUser[$row['id']] = 'yurre,noelle';
				break;
			case 'Chaim. Remco':
				$zpTagToUser[$row['id']] = 'chaim,remco';
				break;
			case 'Machtold':
				$zpTagToUser[$row['id']] = 'machteld';
				break;
			default:
				if(in_array(strtolower($row['name']), $knUsers)) {
					$zpTagToUser[$row['id']] = strtolower($row['name']);
				} else {
					echo "Unknown tag ". $row['name'] ."\n";
				}
		}
		if(isset($zpTagToUser[$row['id']])) {
			foreach(explode(',', $zpTagToUser[$row['id']]) as $tag) {
				if($tag[0] != '*' && !in_array($tag, $knUsers)) {
					die("Tag conversion error: ". $tag ."\n");
				}
			}
		}
	}

	$visibilities = array();
	$res = mysql_query("SELECT tagid, objectid FROM zp_obj_to_tag WHERE type='images'");
	mysql_query('LOCK TABLE fa_tags WRITE');
	while($row = mysql_fetch_assoc($res)) {
		if(!isset($zpPhotos[$row['objectid']]['photo_id'])) {
			continue;
		}
		$pid = $zpPhotos[$row['objectid']]['photo_id'];
		if(isset($zpTagToUser[$row['tagid']])) {
			foreach(explode(',', $zpTagToUser[$row['tagid']]) as $tag) {
				if($tag == '*leden') {
					$visibilities['leden'][] = $pid;
				} else {
					mysql_query("REPLACE INTO fa_tags (photo_id, username) VALUES (". $pid .", '". addslashes($tag) ."')");
				}
			}
		}
	}
	mysql_query('UNLOCK TABLES');

	foreach($visibilities as $visibility => $ids) {
		echo "Visibility ". $visibility .": ". count($ids) ." images\n";
		mysql_query("UPDATE fa_photos SET visibility=". intval($visibility) ." WHERE id IN(". implode(',', $ids) .")");
		var_dump(mysql_error());
	}
?>
