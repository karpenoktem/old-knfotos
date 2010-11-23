<?php
	$cli_mode = true;
	require('header.php');

	$res = mysql_query("SELECT * FROM fa_photos WHERE ((NOT FIND_IN_SET('thumb', cached) OR NOT FIND_IN_SET('large', cached)) OR FIND_IN_SET('invalidated', cached)) AND visibility IN('hidden', 'leden', 'world') AND path LIKE '2010-03-04%marjolijn/' ORDER BY RAND()");
	while($row = mysql_fetch_assoc($res)) {
		echo '==> '. $row['path'] . $row['name'] ."\n";
		if(!is_dir($cachedir . $row['path'])) {
			mkdir($cachedir . $row['path'], 0755, true);
		}
		$cached = explode(',', $row['cached']);
		if(in_array('invalidated', $cached)) {
			$cached = array();
		}
		if(!in_array('thumb', $cached)) {
			echo "===> Thumbnail\n";
			passthru('convert -resize '. $thumbnail_size .' -rotate '. intval($row['rotation']) .' '. escapeshellarg($fotodir . $row['path'] . $row['name']) .' '. escapeshellarg($cachedir . $row['path'] . $row['name'] .'_thumb'), $ret);
			if($ret != 0) {
				echo "ERROR while creating thumbnail\n";
				var_dump($row);
				exit;
			}
			$cached[] = 'thumb';
		}
		if(!in_array('large', $cached)) {
			echo "===> Large\n";
			passthru('convert -resize '. $large_size .' -rotate '. intval($row['rotation']) .' '. escapeshellarg($fotodir . $row['path'] . $row['name']) .' '. escapeshellarg($cachedir . $row['path'] . $row['name'] .'_large'), $ret);
			if($ret != 0) {
				echo "ERROR while creating large image\n";
				var_dump($row);
				exit;
			}
			$cached[] = 'large';
		}
		echo "===> Updating";
		mysql_query("UPDATE fa_photos SET cached='". implode(',', $cached) ."' WHERE id=". $row['id']);
		echo "\n";
	}
?>
