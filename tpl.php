<?php
	$t = array();

	function tpl_show($file) {
		global $t;
		extract($t, EXTR_SKIP);

		set_error_handler('tpl_error_handler', E_NOTICE);
		require('templates/'.$file);
		restore_error_handler();
	}

	function tpl_set($key, $value) {
		global $t;
		if($key == 'file') {
			trigger_error("You can not tpl_set('file', ...);", E_USER_WARNING);
			return;
		}
		$t[$key] = $value;
	}

	function tpl_concat($key, $value) {
		global $t;
		if($key == 'file') {
			trigger_error("You can not tpl_concat('file', ...);", E_USER_WARNING);
			return;
		}
		$t[$key] .= $value;
	}

	function tpl_delete($key) {
		global $t;
		unset($t[$key]);
	}

	function tpl_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
		/* Bummer! */
	}
?>
