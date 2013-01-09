<?php
	$cli_mode = true;
	require('header.php');


	/* First, cache all photos (they're probably far quicker) */
	$res = sql_query("SELECT * FROM fa_photos WHERE ((NOT FIND_IN_SET('thumb', cached) OR NOT FIND_IN_SET('large', cached)) OR FIND_IN_SET('invalidated', cached)) AND visibility IN('hidden', 'leden', 'world') ORDER BY FIND_IN_SET('invalidated', cached), RAND()");
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
	require('footer.php');
	$res = sql_query("SELECT * FROM fa_videos WHERE ((NOT FIND_IN_SET('thumb', cached) OR NOT FIND_IN_SET('360p', cached) OR NOT FIND_IN_SET('720p', cached)) OR FIND_IN_SET('invalidated', cached)) AND visibility IN('hidden', 'leden', 'world') ORDER BY FIND_IN_SET('invalidated', cached), RAND()");
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
			$command = 'ffmpeg -loglevel warning -i ' . escapeshellarg($fotodir . $row['path'] . $row['name']) . ' -ss 00:00:00.50 -vf "scale=' . $ffmpeg_thumbnail_size . '" -vcodec mjpeg -vframes 1 -f image2 ' . escapeshellarg($cachedir . $row['path'] . $row['name'] .'_thumb');
			passthru($command, $ret);
			if($ret != 0) {
				echo "ERROR while creating thumbnail via $command\n";
				var_dump($row);
				exit;
			}
			$cached[] = 'thumb';
		}

		// transcode 360p version if necessary
		if (!in_array('360p', $cached)) {
			echo "===> 360p\n";
			foreach (array('mp4', 'webm') as $format) {
				transcode(escapeshellarg($fotodir . $row['path'] . $row['name']),
						escapeshellarg($cachedir . $row['path'] . $row['name'] .'_360p.'. $format),
						'500k', '360');
			}
			$cached[] = '360p';
		}

		// transcode 720p version if necessary
		if (!in_array('720p', $cached)) {
			echo "===> 720p\n";
			foreach (array('mp4', 'webm') as $format) {
				transcode(escapeshellarg($fotodir . $row['path'] . $row['name']),
						escapeshellarg($cachedir . $row['path'] . $row['name'] .'_720p.'. $format),
						'2500k', '720');
			}
			$cached[] = '720p';
		}

		echo "===> Updating";
		sql_query("UPDATE fa_videos SET cached=%s WHERE id=%i",
				implode(',', $cached), $row['id']);
		echo "\n";
	}

	// transcode one video with ffmpeg
	function transcode($input, $output, $bitrate, $size) {
		$command = 'ffmpeg -loglevel warning -i '. $input .' -b:v '. $bitrate .' -vf "scale=-1:'. $size .'" -y '. $output;
		passthru($command, $ret);
		if($ret != 0) {
			echo "ERROR while transcoding via $command\n";
			var_dump($row);
			exit;
		}
	}
?>
