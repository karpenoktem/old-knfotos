<?php
	function sql_query($query) {
		$args = func_get_args();
		array_shift($args);
		$GLOBALS['_sql_query_params'] = $args;
		$sql = preg_replace_callback('/%./', '_sql_query_callback', $query);
		unset($GLOBALS['_sql_query_params']);
		if(!$res = mysql_query($sql)) {
			throw new QueryFailedException($sql);
		}
		return $res;
	}

	function _sql_query_callback($m) {
		if($m[0] == '%%')
			return '%';
		$val = array_shift($GLOBALS['_sql_query_params']);
		switch($m[0]) {
			case '%i':
				if($val === null) {
					return 'NULL';
				}
				return intval($val);
			case '%s':
				if($val === null) {
					return 'NULL';
				}
				return "'".addslashes($val)."'";
			case '%I':
				if($val === null) {
					return 'NULL';
				}
				return implode(',', array_map('intval', $val));
			case '%S':
				if($val === null) {
					return 'NULL';
				}
				return "'". implode("','", array_map('addslashes', $val)). "'";
			default:
				trigger_error("sql_query(): ".$m[0]." is not a format", E_USER_ERROR);
		}
	}

	class QueryFailedException extends Exception {
		function __construct($sql) {
			parent::__construct("SQL query failed (". mysql_error(). "): ". $sql, mysql_errno());
		}
	}
?>
