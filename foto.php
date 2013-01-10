<?php
	require('header.php');
	require('media.php');

	$media = getUnit(UNIT_PHOTO);

	$path = $fotodir . $media['path'] . $media['name'];

	// send real filename instead of 'foto.php'
	header('Content-disposition: inline; filename='. $media['name']);
	output($path);

	require('footer.php');
?>
