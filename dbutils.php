<?php

	// lock the database/cache for write operations
	function lock_db () {
		global $cachedir;
		// provide a locking mechanism to prevent two processes from simultaneously running
		$fp = fopen ($cachedir .'lockfile', 'w'); // create if necessary
		if (!$fp) {
			echo "Could not acquire lock: unable to create lockfile.\n";
		}
		if (!flock($fp, LOCK_EX|LOCK_NB)) {
			echo "Another process is already updating the database/cache.\nIf this isn't true, remove {$cachedir}lockfile.\n";
			fclose($fp);
			echo "Exiting.\n";
			exit;
		}
		// $fp is required for unlocking (unlocking may be necessary)
		return $fp;
	}

	// unlock again (requires file pointer returned by lock_db)
	// this isn't really needed when run from the command line
	function unlock_db ($fp) {
		flock($fp, LOCK_UN);
		fclose($fp);
	}
