<?php
	if($log_queries) {
		$dur = microtime(true) - $log_queries__start_time;
		file_put_contents($log_queries, 'FOOTER ' . $_SERVER['PHP_SELF']
			.' '.$dur."\n", FILE_APPEND);
	}
?>
