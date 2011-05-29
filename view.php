<?php
	require('header.php');

	if(!isset($_GET['foto'])) {
		header('HTTP/1.1 400 Bad Request');
		die("Missing parameter");
	}
	if(isAdmin() && isset($_GET['updatePhoto'], $_GET['visibility'], $_GET['rotation'], $_GET['tags'])) {
		if(!updatePhotoMetadata($_GET['updatePhoto'], $_GET['visibility'], $_GET['rotation'], $_GET['tags'])) {
			header('HTTP/1.1 400 Bad Request');
			die('Er is een fout opgetreden');
		}
		header('Location: view.php?foto='. urlencode($_GET['foto']));
		exit;
	}
	$photo = getUnitByFullPath($_GET['foto'], UNIT_PHOTO);
	if(!$photo) {
		header('HTTP/1.1 404 Not Found');
		die('Photo not found');
	}

	$parentalbums = loadPathAlbums($photo['path']);

	if(!in_array($photo['visibility'], getVisibleVisibilities()) || !isPathVisible($parentalbums)) {
		header('HTTP/1.1 403 Access denied');
		die('Access denied');
	}

	$name = $photo['name'];
	$foto = $photo['path'] . $photo['name'];
	$album = $photo['path'];
	$id = $photo['id'];
	$visibility = $photo['visibility'];
	$rotation = $photo['rotation'];
	$users = array();
	$res = sql_query("SELECT username FROM kn_site.auth_user ORDER BY username");
	while($row = mysql_fetch_assoc($res)) {
		$users[$row['username']] = $row['username'];
	}
	$taggedUsers = array();
	$res = sql_query("SELECT username FROM fa_tags WHERE photo_id=%i", $photo['id']);
	while($row = mysql_fetch_assoc($res)) {
		$taggedUsers[$row['username']] = $row['username'];
	}

	$next = false;
	$prev = false;
	$first = false;

	$res = sql_query("SELECT name FROM fa_photos WHERE path=%s AND visibility IN (%S) ORDER BY name", $album, getVisibleVisibilities());
	while($row = mysql_fetch_assoc($res)) {
		if(!$first) {
			$first = $album . $photo['name'];
		}
		if($photo['name'] == $row['name']) {
			$next = NULL;
		} elseif($next === NULL) {
			$next = $album . $row['name'];
			break;
		} else {
			$prev = $album . $row['name'];
		}
	}
	mysql_free_result($res);

	if(!$next) {
		$sliding = false;
	}

	$mode = 'view';
	template_assign('mode');

	template_assign('name');
	template_assign('album');
	template_assign('parentalbums');
	template_assign('foto');
	template_assign('id');
	template_assign('visibility');
	template_assign('rotation');
	template_assign('users');
	template_assign('taggedUsers');
	template_assign('next');
	template_assign('prev');
	template_assign('first');
	template_assign('title', 'Foto: '. htmlentities($photo['path'] . $photo['name'])); // XXX
	show_template('view.tpl');
	require('footer.php');
?>
