<?php
	// Reads EXIF rotation from all images and compares it to
	// the rotation in the database.

	$cli_mode = true;
	require('header.php');

	$res = sql_query("SELECT * FROM fa_photos WHERE visibility <> 'lost'");
	$n = 0;
	while($row = mysql_fetch_assoc($res)) {
		$n++;
		if($n % 100 == 0)
			echo $n . ' ';
		$path = $fotodir . $row['path'] . $row['name'];
		$exif = exif_read_data($path, 'IFD0');
		$raw_or = isset($exif['Orientation']) ? $exif['Orientation'] : 0;
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
			echo "  (".$path."\n";
			continue;
		}
		if($or != $row['rotation']) {
			sql_query("UPDATE fa_photos
					SET rotation=%i,
							cached=CONCAT(cached, ',invalidated')
					WHERE `id`=%i", $or, $row['id']);

			echo "\n".$path." ".$row['rotation']." -> ".$or."\n";
		}
	}
?>
