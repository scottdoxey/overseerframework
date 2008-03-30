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
# Function: array_walk_recursive(array $array, function $func);
# Author: Neo Geek (NG)
# 
###############################################################

if (!function_exists('array_walk_recursive')) {

	function array_walk_recursive(&$array, $func) {

		if (!is_array($array) || !function_exists($func)) { return $array; }

		reset($array);

		while (list($key, $value) = each($array)) {
			if (!is_array($array[$key])) { $array[$key] = call_user_func($func, $value, $key); }
			else { $array[$key] = array_walk_recursive($array[$key], $func); }
		}

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
	
		array_multisort($type, SORT_DESC, $structure);
	
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
			file_put_contents(is_string(constant('error_log'))?constant('error_log'):'log.txt', $text . constant('lnbr'), FILE_APPEND);
		}

		if (constant('error_reporting')) {
			echo '<p>' . preg_replace('/(^[[:alpha:] ]+:)/', '<b>\1</b>', strip_tags($text)) . '</p>';
		}

		return false;

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
		preg_match('/^[a-z]+/', $results[0]['Type'], $matches);
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

		$method = $flag?'a+':'w+'; 

		$file_handle = fopen(preg_replace('/\/+/', '/', $file), $method); 

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
		if (preg_match('/^([0-9]{2})(-|/)([0-9]{2})(-|/)([0-9]{2,4})$/', (string)$value)) { return true; } else { return false; } 
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
		if (preg_match('/^[[:alnum:].]+@[[:alnum:]]+\.[[:alnum:].]+$/', (string)$value)) { return true; } else { return false; } 
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
		if (preg_match('/^(http://)+[[:alnum:]]+\.+[[:alnum:]]/', (string)$value)) { return true; } else { return false; } 
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

			if (isset($offset, $path_info[$offset])) { return $path_info[$offset]; }

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
		echo '<pre>';
		foreach ($arrays as $array) { echo print_r($array, true); }
		echo '</pre>';
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

		if (!headers_sent()) {
			header('Location: ' . $url); exit;
		}

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
		else if (round($diff / 3600) < 24) { $output = 'about ' . round($diff / 3600) . ' hour(s) ago'; }
		else if (round($diff / 86400) < 7) { $output = round($diff / 86400) . ' day(s) ago'; }
		else if (round($diff / 604800) < 4) { $output = round($diff / 604800) . ' week(s) ago'; }
		else if (round($diff / 2419200)) { $output = round($diff / 2419200) . ' month(s) ago'; }

		preg_match('/[0-9]+/', $output, $matches);

		if ($matches[0] == 1) { $output = str_replace('(s)', '', $output); }
		else { $output = str_replace('(s)', 's', $output); }

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

			if (is_empty($value) && isset($_GET[$key])) { unset($replacements[$key]); }

		}

		$url_querys = array_merge($_GET, $replacements);

		while (list($key, $value) = each($url_querys)) {

			if ($value) { $output[] = $key . '=' . $value; }

		}

		if ($return == 'string') { $output = '?' . implode($output, '&amp;'); }

		return strlen($output)!=1?$output:'';

	}

}

###############################################################

?>