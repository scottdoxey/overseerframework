<?php

###############################################################
#
# Function: array_clean(array $array [, string $method]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('array_clean')) {

	function array_clean(&$array, $method = null) {

		if (!is_array($array)) { return $array; }

		reset($array);

		while (list($key, $value) = each($array)) {
			if (is_array($value)) { $array[$key] = array_clean($value); }
			else if (!$value && $method == 'empty') { unset($array[$key]); }
			else { $array[$key] = trim($value); }
		}

		reset($array);

		return $array;

	}

}

###############################################################
#
# Function: array_move(array $array [, integer $key, integer $offset]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('array_move')) {

	function array_move(&$array, $key = 0, $offset = 0) {

		$value = array_slice($array, $key, 1);
		unset($array[$key]);
		array_splice($array, $offset, 0, $value);
		return $array;

	}

}

###############################################################
#
# Function: array_sort(array $array, flag $flags);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('array_sort')) {

	function array_sort(&$array, $flags = null) {

		sort($array, $flags);
		return $array;

	}

}

###############################################################
#
# Function: array_walk_recursive(array $array, function $func);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('array_walk_recursive')) {

	function array_walk_recursive(&$array, $func) {

		if (!is_array($array) || !function_exists($func)) { return $array; }

		reset($array);

		while (list($key, $value) = each($array)) {
			if (is_array($array[$key])) { $array[$key] = array_walk_recursive($array[$key], $func); }
			else { $array[$key] = call_user_func($func, $value, $key); }
		}

		reset($array);

		return $array;

	}

}

###############################################################
#
# Function: check_referer();
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('check_referer')) {

	function check_referer() {

		if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != constant('url')) { return false; }

		return true;

	}

}

###############################################################
#
# Function: dir_get_contents([string $dir, string $filter]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('dir_get_contents')) {

	function dir_get_contents($dir = '/', $filter = '', $sort = SORT_ASC) {

		if (!is_dir($dir)) { return false; }

		$structure = array();

		$open_dir = opendir($dir);

		while ($name = @readdir($open_dir)) {

			if (is_dir($dir . $name) && !in_array($name, array('.', '..')) && (($filter && preg_match($filter, $dir . $name . '/')) || !$filter)) {
				$structure[] = array('name'=>$name, 'type'=>'dir', 'url'=>$dir . $name . '/', 'contents'=>dir_get_contents($dir . $name . '/', $filter, $sort));
			} else if (is_file($dir . $name) && (($filter && preg_match($filter, $dir . $name)) || !$filter)) {
				$structure[] = array('name'=>$name, 'type'=>'file', 'url'=>$dir . $name);
			}

		}

		$type = array();

		while (list($key, $value) = each($structure)) { $type[$key] = $value['type']; }

		array_multisort($type, $sort, $structure);

		return $structure;

	}

}

###############################################################
#
# Function: endtime(string $text);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('endtime')) {

	function endtime() { return microtime(true) - constant('int_microtime'); }

}

###############################################################
#
# Function: error(string $text);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('error')) {

	function error($text = '') {

		if (!$text) { return false; } else { $text = date('Y-m-d H:i:s') . ' - ' . $text; }

		if (constant('error_log')) {
			file_put_contents(is_string(constant('error_log'))?constant('error_log'):'log.txt', $text . PHP_EOL, FILE_APPEND);
		}

		if (constant('error_reporting')) {
			echo '<p>' . preg_replace('/(^[[:alpha:] ]+:)/', '<strong>\1</strong>', strip_tags($text)) . '</p>' . PHP_EOL;
		}

		return false;

	}

}

###############################################################
#
# Function: fetch_remote_file(string $file);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('fetch_remote_file')) {

	function fetch_remote_file($file) {

		$path = parse_url($file);

		if ($fs = @fsockopen($path['host'], isset($path['port'])?$path['port']:80)) {

			$header = "GET " . $path['path'] . " HTTP/1.0\r\nHost: " . $path['host'] . "\r\n\r\n";

			fwrite($fs, $header);

			$buffer = '';

			while ($tmp = fread($fs, 1024)) { $buffer .= $tmp; }

			preg_match('/HTTP\/[0-9\.]{1,3} ([0-9]{3})/', $buffer, $http);
			preg_match('/Location: (.*)/', $buffer, $redirect);

			if (isset($redirect[1]) && $file != trim($redirect[1])) { return fetch_remote_file(trim($redirect[1])); }

			if (isset($http[1]) && $http[1] == 200) { return substr($buffer, strpos($buffer, "\r\n\r\n") +4); } else { return false; }

		} else { return false; }

	}

}

###############################################################
#
# Function: file_put_contents(string $table, string $field);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('field_type')) {

	function field_type($table, $field) {

		$results = mysql_fetch_results('SHOW COLUMNS FROM `' . $table . '` WHERE `Field` = "' . $field . '"');
		preg_match('/^[a-z]+/i', $results[0]['Type'], $matches);
		return $matches[0];

	}

}

###############################################################
#
# Function: file_put_contents(string $file [, string $contents, boolean $flag]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('file_put_contents')) {

	if (!@constant('FILE_APPEND')) { define('FILE_APPEND', true); }

	function file_put_contents($file, $contents = '', $flag = false) {

		$file_handle = fopen(preg_replace('/\/+/', '/', $file), $flag?'a+':'w+');

		fwrite($file_handle, $contents);

		fclose($file_handle);

		return true;

	}

}

###############################################################
#
# Function: is_date(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_date')) {

	function is_date($value) {
		if (preg_match('/^([0-9]{2})([-\/ ])([0-9]{2})([-\/ ])([0-9]{2,4})$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_email(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_email')) {

	function is_email($value) {
		if (preg_match('/^[[:alnum:]-.]+@[[:alnum:]-]+\.[[:alnum:]]{2,}+$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_empty(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_empty')) {

	function is_empty($value) {
		if (!isset($value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_number(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_number')) {

	function is_number($value) {
		if (preg_match('/^[.0-9]+$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_simple(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_simple')) {

	function is_simple($value) {
		if (preg_match('/^[[:alnum:]_]+$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_simple_alpha(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_simple_alpha')) {

	function is_simple_alpha($value) {
		if (preg_match('/^[[:alnum:]]+$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_simple_number(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_simple_number')) {

	function is_simple_number($value) {
		if (preg_match('/^[0-9]+$/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: is_web_address(string $value);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('is_web_address')) {

	function is_web_address($value) {
		if (preg_match('/^((http|https):\/\/)?+[[:alnum:]-]+\.+[[:alnum:]]/', (string)$value)) { return true; } else { return false; }
	}

}

###############################################################
#
# Function: mysql_fetch_results(string $query [, array $results]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('mysql_fetch_results')) {

	function mysql_fetch_results($query = '', $results = array()) {
		$result = @mysql_query($query) or error('MySQL Error: ' . mysql_error());
		while ($row = @mysql_fetch_assoc($result)) { $results[] = $row; }
		return $results;
	}

}

###############################################################
#
# Function: path_info([integer $offset]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('path_info')) {

	function path_info($offset = 0) {

		if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != $_SERVER['SCRIPT_NAME']) {

			$path_info = explode('/', substr($_SERVER['PATH_INFO'], 1));

			if (isset($path_info[$offset])) { return $path_info[$offset]; }

		}

		return false;

	}

}

###############################################################
#
# Function: primary_key(string $table);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('primary_key')) {

	function primary_key($table = '') {
		$results = mysql_fetch_results('DESCRIBE ' . $table);
		foreach ($results as $field) { if ($field['Key'] == 'PRI') { return $field['Field']; } }
		return false;
	}

}

###############################################################
#
# Function: print_array(array $array1 [, array $array2, ..., array $array10]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('print_array')) {

	function print_array() {
		$arrays = func_get_args();
		foreach ($arrays as $array) { echo '<pre>' . print_r($array, true) . '</pre>'; }
	}

}

###############################################################
#
# Function: sanitize_data([array $array]);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('sanitize_data')) {

	function sanitize_data(&$data) {

		reset($data);

		while (list($key, $value) = each($data)) {
			$data[$key] = mysql_real_escape_string(get_magic_quotes_gpc()?stripslashes($value):$value);
		}

		reset($data);

		return $data;

	}

}

###############################################################
#
# Function: set_location(string $url);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('set_location')) {

	function set_location($url) {

		if (!headers_sent()) { header('Location: ' . $url); exit; }

		return false;

	}

}

###############################################################
#
# Function: timeago(timestamp $timestamp);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('timeago')) {

	function timeago($timestamp) {

		$diff = time() - strtotime($timestamp);

		if ($diff < 60) { $output = $diff . ' seconds ago'; }
		else if (round($diff / 60) < 60) { $output = round($diff / 60) . ' minute(s) ago'; }
		else if (round($diff / 3600) < 24) { $output = round($diff / 3600) . ' hour(s) ago'; }
		else if (round($diff / 86400) < 7) { $output = round($diff / 86400) . ' day(s) ago'; }
		else if (round($diff / 604800) < 4) { $output = round($diff / 604800) . ' week(s) ago'; }
		else if (round($diff / 2419200) < 12) { $output = round($diff / 2419200) . ' month(s) ago'; }
		else if (round($diff / 29030400)) { $output = round($diff / 29030400) . ' years(s) ago'; }

		if (preg_match('/[2-9]+/', $output)) { $output = str_replace('(s)', 's', $output); }
		else { $output = str_replace('(s)', '', $output); }

		return $output;

	}

}

###############################################################
#
# Function: url_query(array $replacements, string $return);
# Author: Neo Geek (NG)
#
###############################################################

if (!function_exists('url_query')) {

	function url_query($replacements = array(), $return = 'string') {

		if (!is_array($replacements)) { return false; }

		reset($replacements);

		$output = array();

		while (list($key, $value) = each($replacements)) {
			if (!$value && isset($_GET[$key])) { unset($_GET[$key]); unset($replacements[$key]); }
		}

		$url_querys = array_merge($_GET, $replacements);

		while (list($key, $value) = each($url_querys)) {
			if ($value) { $output[] = $key . '=' . $value; }
		}

		if ($return == 'string' && count($output)) { $output = '?' . implode($output, '&amp;'); }

		return $output?$output:'';

	}

}

###############################################################

?>