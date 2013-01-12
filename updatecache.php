<?php
	$cli_mode = true;
	require('header.php');
	require('dbutils.php');

	$lock = lock_db();

	/* First, cache all photos (they're probably far quicker) */
	echo "Caching photos...\n";

	$res = sql_query("SELECT * FROM fa_photos WHERE type='photo' AND ((NOT FIND_IN_SET('thumb', cached) OR NOT FIND_IN_SET('large', cached)) OR FIND_IN_SET('invalidated', cached)) AND visibility IN('hidden', 'leden', 'world') ORDER BY FIND_IN_SET('invalidated', cached), RAND()");
	while($row = mysql_fetch_assoc($res)) {
		echo '==> '. $row['path'] . $row['name'] ."\n";
		if(!is_dir($cachedir . $row['path'])) {
			mkdir($cachedir . $row['path'], 0755, true);
		}
		$cached = explode(',', $row['cached']);
		if(in_array('invalidated', $cached)) {
			$cached = array();
		}

		// create thumbnail if necessary
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

		// create larger photo if necessary
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
		sql_query("UPDATE fa_photos SET cached=%s WHERE id=%i",
				implode(',', $cached), $row['id']);
		echo "\n";
	}

	/* After that, transcode videos (potentially takes a looong time) */
	echo "\nCaching/transcoding videos...\n";

	$resolution_constraints_sql = '';
	foreach ($video_resolutions as $res) {
		$resolution_constraints_sql .=  " OR NOT FIND_IN_SET('$res', cached)";
	}
	$res = sql_query("SELECT * FROM fa_photos WHERE type='video' AND ((NOT FIND_IN_SET('thumb', cached)$resolution_constraints_sql) OR FIND_IN_SET('invalidated', cached)) AND visibility IN('hidden', 'leden', 'world') ORDER BY FIND_IN_SET('invalidated', cached), RAND()");
	$ffmpeg_thumbnail_size = substr($thumbnail_size, 0, 1) == 'x' ? '-1:'.substr($thumbnail_size, 1) : substr($thumbnail_size, 0, -1).':-1';
	while ($row = mysql_fetch_assoc($res)) {
		echo '==> '. $row['path'] . $row['name'] ."\n";
		if(!is_dir($cachedir . $row['path'])) {
			mkdir($cachedir . $row['path'], 0755, true);
		}
		$cached = explode(',', $row['cached']);
		if(in_array('invalidated', $cached)) {
			$cached = array();
		}

		// extract poster frame from video and resize
		if(!in_array('thumb', $cached)) {
			echo "===> Thumbnail\n";
			$command = $ffmpeg .' -i ' . escapeshellarg($fotodir . $row['path'] . $row['name']) . ' -ss 00:00:00.50 -vf "scale=' . $ffmpeg_thumbnail_size . '" -vcodec mjpeg -vframes 1 -f image2 ' . escapeshellarg($cachedir . $row['path'] . $row['name'] .'_thumb');
			passthru($command, $ret);
			if($ret != 0) {
				echo "ERROR while creating thumbnail via $command\n";
				var_dump($row);
				exit;
			}
			$cached[] = 'thumb';
		}

		// transcode necessary resolutions
		foreach ($video_resolutions as $resolution) {
			if (!in_array($resolution, $cached)) {
				echo "===> $resolution\n";
				foreach ($video_codecs as $format) {
					transcode($fotodir . $row['path'] . $row['name'],
							$cachedir . $row['path'] . $row['name'] ."_$resolution.". $format,
							'500k', intval($resolution));
				}
				$cached[] = $resolution;
			}
		}

		echo "===> Updating";
		sql_query("UPDATE fa_photos SET cached=%s WHERE id=%i",
				implode(',', $cached), $row['id']);
		echo "\n";
	}

	// not really needed when run from the command line
	unlock_db($lock);

	require('footer.php');

	// transcode one video with ffmpeg
	function transcode($input, $output, $bitrate, $size) {
		global $ffmpeg;
		$command = $ffmpeg .' -i '. escapeshellarg($input) .' -b:v '. $bitrate .' -vf "scale=-1:'. $size .'" -y '. escapeshellarg($output);
		passthru($command, $ret);
		if($ret != 0) {
			echo "ERROR while transcoding via $command\n";
			var_dump($row);
			exit;
		}
	}
?>
