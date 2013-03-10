<?php
	require('header.php');
	require('media.php');

	$media = getUnit(UNIT_PHOTO);

	$path = $fotodir . $media['path'] . $media['name'];

	output($path, $media['name']);

	require('footer.php');
?>
