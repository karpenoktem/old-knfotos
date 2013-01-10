<?php
	require('header.php');
	require('media.php');

	$path = $fotodir . $row['path'] . $row['name'];

	// send real filename instead of 'foto.php'
	header('Content-disposition: inline; filename='. $row['name']);
	output($path);

	require('footer.php');
?>
