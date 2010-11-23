<?php
	require('header.php');

	var_dump($_POST);
	if(isAdmin() && isset($_POST['updatePhoto'], $_POST['visibility'], $_POST['rotation'], $_POST['tags'])) {
		if(!updatePhotoMetadata($_POST['updatePhoto'], $_POST['visibility'], $_POST['rotation'], $_POST['tags'])) {
			echo "Boem :(\n";
		}
		var_dump(mysql_error());
		die(":)");
	} elseif(isAdmin() && isset($_POST['updateAlbum'], $_POST['visibility'], $_POST['humanname'])) {
		if(!updateAlbumMetadata($_POST['updateAlbum'], $_POST['visibility'], $_POST['humanname'])) {
			echo "Boem :(\n";
		}
		var_dump(mysql_error());
		die(":)");
	}
	die('ERR');
?>
