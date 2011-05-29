<?php
	$log_queries__start_time = microtime(true);
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('log_errors', 0);

	define('UNIT_PHOTO', 1);
	define('UNIT_ALBUM', 2);
	define('UNIT_BOTH', UNIT_PHOTO | UNIT_ALBUM);

	require('sql.php');

	if(!isset($cli_mode)) {
		$cli_mode = false;
	}
	if(!is_file('config.php')) {
		die("Missing config.php. Please move config.php.sample to config.php and edit the defaults");
	}
	require('config.php');

	/* Check the config */
	if(!isset($fotodir, $cachedir, $domain, $absolute_url_path, $thumbs_per_row, $rows_of_thumbs, $imagick, $thumbnail_size, $foto_slider, $db_host, $db_user, $db_pass, $db_db, $log_queries)) {
		die("Missing settings");
	}
	if($log_queries)
		file_put_contents($log_queries, "HEADER "
			.$_SERVER['PHP_SELF'] ."\n", FILE_APPEND);
	if(!is_dir($fotodir)) {
		die("foto dir not found");
	}
	if(!is_dir($cachedir)) {
		die("Cache dir not found");
	}
	if(!is_readable($fotodir)) {
		die("Could not open fotodir");
	}
	if(!is_readable($cachedir)) {
		die("Could not open cachedir");
	}
	if(substr($fotodir, -1) != '/') {
		$fotodir .= '/';
	}
	if(substr($cachedir, -1) != '/') {
		$cachedir .= '/';
	}
	if($foto_slider && !isset($foto_slider_preload, $foto_slider_timeout)) {
		die("Missing settings");
	}

	if(!$db = mysql_connect($db_host, $db_user, $db_pass)) {
		die('Could not connect to the database');
	}
	if(!mysql_select_db($db_db)) {
		die('Could not switch database');
	}

	if(!$cli_mode) {
		if(!is_file($cachedir .'.htaccess') && !isset($no_cache_htaccess)) {
			die('You should create an .htaccess file in your cache dir.<br>Add <i>$no_cache_htaccess=true;</i> to your config.php to ignore this.<br>Or create one with contents like this:<br>Order allow,deny<br>Deny from all');
		}

		handle_authentication();

		if(!isLid()) {
			$login_url = $_SERVER['REQUEST_URI'];
			if(strpos($login_url, '?') !== false) {
				$login_url .= '&login';
			} else {
				$login_url .= '?login';
			}
			template_assign('login_url');
		}

		template_assign('absolute_url_path');
		template_assign('thumbs_per_row');
		template_assign('rows_of_thumbs');

		$sliding = ($foto_slider && isset($_GET['slide']));
		template_assign('sliding');
		template_assign('foto_slider');
		if($foto_slider) {
			template_assign('foto_slider_preload');
			template_assign('foto_slider_timeout');
		}
	}

	/* Extensions */
	$extensions = array(
		'gif' => 'gif', 
		'jpg' => 'jpeg', 
		'jpeg' => 'jpeg', 
		'png' => 'png', 
		'gif' => 'gif', 
	);
	if($imagick) {
		$extensions['bmp'] = 'bmp';
	}

	/* Functions */
	function show_template($tpl) {
		global $_tplvars;
		extract($_tplvars, EXTR_SKIP);
		include('templates/'. $tpl);
	}
	function template_assign($var, $value = NULL) {
		global $$var, $_tplvars;
		if($value === NULL) {
			$_tplvars[$var] = &$$var;
		} else {
			$_tplvars[$var] = $value;
		}
	}

	function getext($fn) {
		$ex = explode('.', $fn);
		return strtolower(array_pop($ex));
	}

	function showTextAsImage($str, $width = 100, $height = NULL) { // XXX $width
		$fid = 3;
		$charsPerLine = floor($width / imagefontwidth($fid));
		$lines = explode("\n", wordwrap($str, $charsPerLine));
		if($height === NULL) {
			$height = count($lines) * imagefontheight($fid);
		}
		$img = imagecreatetruecolor($width, $height);
		$bgc = imagecolorallocate($img, 255, 255, 255);
		$fgc = imagecolorallocate($img, 0, 0, 0);
		imagefill($img, 0, 0, $bgc);
		$y = 0;
		foreach($lines as $line) {
			imagestring($img, $fid, 0, $y, $line, $fgc);
			$y += imagefontheight($fid);
		}
		header('Content-Type: image/png');
		imagepng($img);
		exit;
	}

	function handle_authentication() {
		global $domain, $absolute_url_path;

		session_set_cookie_params(3 * 3600, $absolute_url_path);
		session_name('sessid-knalbum');
		session_start();
		if($_SERVER['HTTP_HOST'] != $domain) {
			header('Location: http://'. $domain . $_SERVER['REQUEST_URI']);
			exit;
		}
		if(isset($_GET['user'], $_GET['token'])) {
			$params = array('user' => $_GET['user'], 'validate' => $_GET['token'], 'url' => 'http://'. $domain . $absolute_url_path);
			$ch = curl_init('http://www.karpenoktem.nl/accounts/rauth/?'. http_build_query($params));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch);
			curl_close($ch);
			if($res != 'OK') {
				die('Login mislukt. <a href="'. $absolute_url_path .'">Probeer het nogmaals.</a>');
			}
			$_SESSION['user'] = $_GET['user'];

			$res = sql_query("SELECT auth_group.name FROM kn_site.auth_user, kn_site.auth_user_groups, kn_site.auth_group WHERE auth_user.id = auth_user_groups.user_id AND auth_user_groups.group_id = auth_group.id AND auth_user.is_active AND auth_user.username=%s AND auth_group.name IN('webcie', 'fotocie', 'fototaggers')", $_SESSION['user']);
			$_SESSION['groups'] = array();
			while($row = mysql_fetch_assoc($res)) {
				$_SESSION['groups'][] = $row['name'];
			}

			if(isset($_SESSION['entry_url'])) {
				header('Location: '. $_SESSION['entry_url']);
				unset($_SESSION['entry_url']);
			} else {
				header('Location: '. $absolute_url_path);
			}
			exit;
		}
		if(isset($_GET['login']) && !isset($_SESSION['user'])) {
			$params = $_GET;
			unset($params['login']);
			$_SESSION['entry_url'] = $_SERVER['SCRIPT_NAME'] .'?'. http_build_query($params);
			header('Location: http://www.karpenoktem.nl/accounts/rauth/?url=http://'. $domain . $absolute_url_path);
			exit;
		} elseif(isset($_GET['logout'])) {
			unset($_SESSION['user'], $_SESSION['groups']);
			header('Location: '. $_SERVER['REQUEST_URI']);
			exit;
		}
	}

	function isAdmin() {
		return (isset($_SESSION['groups']) && count($_SESSION['groups']) > 0);
	}

	function isLid() {
		return isset($_SESSION['user']);
	}

	function getVisibleVisibilities() {
		$visibilities = array('world');
		if(isset($_SESSION['user'])) {
			$visibilities[] = 'leden';
		}
		if(isAdmin()) {
			$visibilities[] = 'hidden';
		}
		return $visibilities;
	}

	function isPathVisible($path) {
		$visibilities = getVisibleVisibilities();

		if(!is_array($path)) {
			$path = loadPathAlbums($path);
		}
		foreach($path as $album) {
			if(!in_array($album['visibility'], $visibilities)) {
				return false;
			}
		}

		return true;
	}

	function loadPathAlbums($path) {
		if(!$path) {
			return array();
		}
		if(substr($path, -1) == '/') {
			$path = substr($path, 0, -1);
		}
		$parts = array($path);
		while(($p = strrpos($path, '/')) !== false) {
			$path = substr($path, 0, $p);
			$parts[] = $path;
		}

		$albums = array();
		$res = sql_query("SELECT * FROM fa_albums WHERE CONCAT(path, name) IN (%S) ORDER BY path", $parts);
		while($row = mysql_fetch_assoc($res)) {
			$albums[] = $row;
		}
		return $albums;
	}

	function updatePhotoMetadata($id, $visibility, $rotation, $tags) {
		$id = intval($id);
		if(!in_array($visibility, array('world', 'leden', 'hidden', 'deleted'))) {
			return false;
		}
		if($rotation < 0 || $rotation > 360) {
			return false;
		}
		if($tags) {
			$tags = explode(',', $tags);
			$res = sql_query("SELECT COUNT(*) FROM kn_site.auth_user WHERE username IN (%S)", $tags);
			$row = mysql_fetch_row($res);
			if(count($tags) != $row[0]) {
				return false;
			}
		}
		sql_query("UPDATE fa_photos SET visibility=%s, cached=IF(rotation = %i, cached, CONCAT(cached, ',invalidated')), rotation=%i, check_tags=0 WHERE id=%i", $visibility, $rotation, $rotation, $id);
		sql_query("DELETE FROM fa_tags WHERE photo_id=%i", $id);

		if($tags) {
			$args = array();
			$sql = "INSERT INTO fa_tags (photo_id, username) VALUES ";
			$first = false;
			foreach($tags as $tag) {
				if(!$first)
					$first = true;
				else
					$sql .= ',';
				$sql .= "(%i, %s)";
				$args[]= $id;
				$args[]= $tag;
			}
			call_user_func_array(sql_query,
				array_merge(array($sql), $args));
		}
		return true;
	}

	function updateAlbumMetadata($id, $visibility, $humanname) {
		$id = intval($id);
		if(!in_array($visibility, array('world', 'leden', 'hidden', 'deleted'))) {
			return false;
		}
		sql_query("UPDATE fa_albums SET visibility=%s, humanname=%s WHERE id=%i", $visibility, $humanname, $id);
		return true;
	}

	function getUnitByFullPath($fullpath, $flags = UNIT_BOTH) {
		$p = strrpos($fullpath, '/');
		if($p === false) {
			$name = $fullpath;
			$path = '';
		} else {
			$name = substr($fullpath, $p+1);
			$path = substr($fullpath, 0, $p+1);
		}

		if($flags & UNIT_PHOTO) {
			$res = sql_query("SELECT * FROM fa_photos WHERE path = %s AND name = %s", $path, $name);
			if($photo = mysql_fetch_assoc($res)) {
				$photo['type'] = 'photo';
				return $photo;
			}
		}
		if($flags & UNIT_ALBUM) {
			$res = sql_query("SELECT * FROM fa_albums WHERE path = %s AND name = %s", $path, $name);
			if($album = mysql_fetch_assoc($res)) {
				$album['type'] = 'album';
				return $album;
			}
		}
		return false;
	}
?>
