<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	define('RAUTH_MYURL', 'http://fotoadmin.karpenoktem.com/');
	define('FOTO_DIR', '/var/fotos/');
	define('USER_DIRS', '/mnt/phassa/home/');

	session_name('sessid-fotoadmin');
	session_start();
	if(isset($_GET['user'], $_GET['token'])) {
		$params = array('user' => $_GET['user'], 'validate' => $_GET['token'], 'url' => RAUTH_MYURL);
		$ch = curl_init('http://karpenoktem.nl/accounts/rauth/?'. http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);
		if($res != 'OK') {
			die('Login mislukt. <a href="'. RAUTH_MYURL .'">Probeer het nogmaals.</a>');
		}
		$_SESSION['user'] = $_GET['user'];
		header('Location: '. RAUTH_MYURL);
		exit;
	}
	if(!isset($_SESSION['user'])) {
		header('Location: http://karpenoktem.nl/accounts/rauth/?url='. RAUTH_MYURL);
		exit;
	}

	require('df/form.php');
	require('df/field.php');
	require('df/defaultvaluefield.php');
	require('df/labelfield.php');
	require('df/inputfield.php');
	require('df/text.php');
	require('df/submit.php');
	require('df/select.php');
	require('df/textarea.php');
	require('df/integer.php');
	require('tpl.php');
?>
