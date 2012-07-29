<?php
	require('header.php');

        CsrfToken::checkOrDie();

	if(isAdmin() && isset($_POST['updatePhoto'], $_POST['visibility'], $_POST['rotation'], $_POST['tags'])) {
		if(!updatePhotoMetadata($_POST['updatePhoto'], $_POST['visibility'], $_POST['rotation'], $_POST['tags'])) {
			echo "Boem :(\n";
		}
		die(":)");
	} elseif(isAdmin() && isset($_POST['updateAlbum'], $_POST['visibility'], $_POST['humanname'])) {
		if(!updateAlbumMetadata($_POST['updateAlbum'], $_POST['visibility'], $_POST['humanname'])) {
			echo "Boem :(\n";
		}
		die(":)");
	}
	die('ERR');
?>
