<?php

###############################################################
#
# Name: Overseer Framework
# Version: 0.2beta r2 build265
# Author: Neo Geek {neo@neo-geek.net}
# Author's Website: http://neo-geek.net/
# Framework's Website: http://overseercms.com/framework/
# Copyright: (c) 2008 Neo Geek, Neo Geek Labs
# Timestamp: 2008-04-03 11:04:31

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
#
###############################################################



###############################################################
#
# Code Level Constants
#
###############################################################

if (!defined('error_log')) {
	define('error_log', true);
}

if (!defined('error_reporting')) {
	define('error_reporting', false);
}

if (!defined('int_microtime')) {
	define('int_microtime', microtime(true));
}

if (!defined('lnbr')) {
	define('lnbr', PHP_EOL);
}

if (!defined('maxview')) {
	define('maxview', 10);
}

if (!defined('page')) {
	define('page', $_SERVER['SCRIPT_NAME'] . (isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:''));
}

if (!defined('script')) {
	define('script', preg_replace('/(' . str_replace('/', '\/', isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'') . ')?(\?.*)?$/', '', $_SERVER['REQUEST_URI']));
}

if (!defined('url')) {
	define('url', 'http://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80'?':' . $_SERVER['SERVER_PORT']:'') . $_SERVER['REQUEST_URI']);
}


###############################################################
#
# PHP Level Constants
#
###############################################################

// List of Supported Timezones
// http://us3.php.net/manual/en/timezones.php

if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('America/New_York');
}


###############################################################



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
			file_put_contents(is_string(constant('error_log'))?constant('error_log'):'log.txt', $text . PHP_EOL, FILE_APPEND);
		}

		if (constant('error_reporting')) {
			echo '<p>' . preg_replace('/(^[[:alpha:] ]+:)/', '<b>\1</b>', strip_tags($text)) . '</p>';
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

			$header = 'GET ' . $path['path'] . ' HTTP/1.0' . PHP_EOL;
			$header .= 'Host: ' . $path['host'] . str_repeat(PHP_EOL, 2);

			fwrite($fs, $header);

			$buffer = '';

			while ($tmp = fread($fs, 1024)) { $buffer .= $tmp; }

			preg_match('/Content-Length: ([0-9]+)/', $buffer, $matches);

			if ($matches[1] > 0) { return substr($buffer, -$matches[1]); } else { return false; }

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



###############################################################
#
# Template
#
###############################################################

if (!isset($_GET['norender']) && isset($ob_template)) { ob_start('ob_template'); }

function ob_template($buffer) {

	global $ob_template;

	$regs = array();

	preg_match('/<title>(.*)<\/title>/si', $buffer, $regs);
	$ob_template = str_replace('%TITLE%', trim($regs[1]), $ob_template);

	preg_match('/<head>(.*)<\/head>/si', $buffer, $regs);
	$regs[1] = preg_replace('/<title>(.*)<\/title>/si', $regs[1]);
	$ob_template = str_replace('%HEAD%', trim($regs[1]), $ob_template);

	preg_match('/<body>(.*)(<\/body>)/si', $buffer, $regs);
	$ob_template = str_replace('%BODY%', trim($regs[1]), $ob_template);

	// $ob_template = ereg_replace('%+[[:alnum:]_]+%', '', $ob_template);

	return $ob_template;

}

###############################################################



###############################################################
#
# Class: DB
# Author: Neo Geek (NG)
#
###############################################################

class DB {

	var $resource = null;
	var $results = null;
	var $instances = array('connect'=>0, 'query'=>0);

	function Connect($server, $username, $password, $database, $cache = true) {

		$resource = @mysql_connect($server, $username, $password, true) or error('MySQL Error: ' . mysql_error());

		if ($database) { @mysql_select_db($database, $resource) or error('MySQL Error: ' . mysql_error()); }

		if ($cache) { $this->resource = $resource; }

		$this->instances['connect']++;

		return $resource;

	}

	function Query($query, $resource = null, $return = 'array', $cache = true) {

		if (is_resource($resource)) { $result = @mysql_query($query, $resource) or error('MySQL Error: ' . mysql_error()); }
		else if (is_resource($this->resource)) { $result = @mysql_query($query, $this->resource) or error('MySQL Error: ' . mysql_error()); }
		else { $result = @mysql_query($query) or error('MySQL Error: ' . mysql_error()); }

		if ($return == 'array') {

			$results = array();

			while ($row = @mysql_fetch_assoc($result)) { $results[] = $row; }

			@mysql_free_result($result);

		} else if ($return == 'boolean') {

			$results = @mysql_affected_rows();

		} else {

			$results = $result;

		}

		if ($cache) { $this->results = $results; }

		$this->instances['query']++;

		return $results;

	}

}

###############################################################

$DB = new DB;

###############################################################



###############################################################
#
# Class: Database
# Author: Neo Geek (NG)
#
###############################################################

class Database {

	var $resource = null;
	var $results = null;

	function DatabaseList() {

		global $DB;

		$results = array();

		if (is_resource($this->resource)) { $database_list = mysql_list_dbs($this->resource) or error('MySQL Error: ' . mysql_error()); }
		else if (is_resource($DB->resource)) { $database_list = mysql_list_dbs($DB->resource) or error('MySQL Error: ' . mysql_error()); }
		else { return error('MySQL Error: Cannot connect to MySQL server. Please advise.'); }

		while ($database = @mysql_fetch_object($database_list)) { $results[] = array('database'=>$database->Database); }

		$this->results = $results;

		@mysql_free_result($database_list);

		return $results;

	}

	function TableList($database) {

		global $DB;

		$results = array();

		if (is_resource($this->resource)) { $table_list = mysql_list_tables($database, $this->resource) or error('MySQL Error: ' . mysql_error()); }
		else if (is_resource($DB->resource)) { $table_list = mysql_list_tables($database, $DB->resource) or error('MySQL Error: ' . mysql_error()); }
		else { return error('MySQL Error: Cannot connect to MySQL server. Please advise.'); }

		$table_count = @mysql_num_rows($table_list);

		for ($i = 0; $i < $table_count; $i++) { $results[] = array('table'=>mysql_tablename($table_list, $i)); }

		$this->results = $results;

		@mysql_free_result($table_list);

		return $results;

	}

	function Table($database, $table, $fields = '*', $clause = null) {

		global $DB;

		if (is_resource($this->resource)) { $resource = $this->resource; }
		else if (is_resource($DB->resource)) { $resource = $DB->resource; }
		else { return error('MySQL Error: Cannot connect to MySQL server. Please advise.');}

		$db_sort_by = (isset($_GET['db_sort_by']) && is_simple($_GET['db_sort_by']))?$_GET['db_sort_by']:'';
		$db_sort_order = (isset($_GET['db_sort_order']) && is_simple($_GET['db_sort_order']))?$_GET['db_sort_order']:'asc';
		$db_start = (isset($_GET['db_start']) && is_simple_number($_GET['db_start']))?$_GET['db_start']:0;
		$db_limit = (isset($_GET['db_limit']) && is_simple_number($_GET['db_limit']))?$_GET['db_limit']:constant('maxview');

		$sql = 'SELECT ' . $fields . ' FROM `' . $database . '`.`' . $table . '`';
		if ($clause) { $sql .= ' ' . $clause; }
		if ($db_sort_by) { $sql .= ' ORDER BY `' . $db_sort_by . '` ' . ucwords($db_sort_order) . ''; }
		if ($db_limit) { $sql .= ' LIMIT ' . $db_start . ', ' . $db_limit; }

		$results = $DB->Query($sql, $resource, 'array', false);

		$this->results = $results;

		return $results;

	}

	function Process($database, $table, $variables = array()) {

		global $DB;

		if (!count($variables)) { return false; }

		if (is_resource($this->resource)) { $resource = $this->resource; }
		else if (is_resource($DB->resource)) { $resource = $DB->resource; }
		else { return error('MySQL Error: Cannot connect to MySQL server. Please advise.'); }

		$updates = array();

		$columns = $DB->Query('SHOW COLUMNS FROM `' . $database . '`.`' . $table . '`', $resource, 'resource', false);

		while ($row = @mysql_fetch_assoc($columns)) {

			if (isset($variables[$row['Field']]) && $row['Key'] != 'PRI') {

				$value = $variables[$row['Field']];

				if (is_number($value) || in_array($value, array('NOW()'))) { $updates[] = '`' . $row['Field'] . '` = ' . $value . ''; } else {

					$updates[] = '`' . $row['Field'] . '` = "' . $value . '"';

				}

			} else if ($row['Key'] == 'PRI') { $primary_key = $row['Field']; }

		}

		if (!count($updates)) { return error('MySQL Error: None of the included key/value sets can update this table.'); }

		if (isset($variables[$primary_key])) {
			$results = $DB->Query('SELECT COUNT(`' . $primary_key . '`) AS `row_count` FROM `' . $database . '`.`' . $table . '` WHERE `' . $primary_key . '` = ' . $variables[$primary_key] . '', $resource, 'array', false);
		}

		if (!isset($results) || $results[0]['row_count'] == 0) {
			$sql = 'INSERT INTO ' . '`' . $database . '`.`' . $table . '` SET ' . implode($updates, ', ');
		} else {
			$sql = 'UPDATE ' . '`' . $database . '`.`' . $table . '` SET ' . implode($updates, ', ') . ' WHERE `' . $primary_key . '` = ' . $variables[$primary_key] . '';
		}

		return $DB->Query($sql, $resource, 'boolean', false);

	}

}

###############################################################

$Database = new Database;

###############################################################



###############################################################
#
# Class: GFX
# Author: Neo Geek (NG)
#
###############################################################

class GFX
{
	
	function Resize($image, $width = 100, $height = 100, $output = null) {
		
		if (!is_file($image)) { return false; }
		
		$properties = getimagesize($image);
		
		$cache = md5(serialize(func_get_args()) . serialize($properties)) . '.' . substr($properties['mime'], 6);
		
		if (is_dir($output) && is_file($output . $cache)) { return $output . $cache; }
		
		if ($properties['mime'] == 'image/jpeg') { $original = imagecreatefromjpeg($image); }
		else if ($properties['mime'] == 'image/gif') { $original = imagecreatefromgif($image); }
		else if ($properties['mime'] == 'image/png') { $original = imagecreatefrompng($image); }
		
		$new = imagecreatetruecolor($width, $height);
		
		$ratio = $properties[0]/$properties[1];
		
		if ($width/$height < $ratio) { $width = $height*$ratio; } else { $height = $width/$ratio; }
		
		$offset_x = ($width-func_get_arg(1)) / 2;
		$offset_y = ($height-func_get_arg(2)) / 2;
		
		imagecopyresampled($new, $original, -$offset_x, -$offset_y, 0, 0, $width, $height, $properties[0], $properties[1]);
		
		if (!$output) { header('Content-type: ' . $properties['mime']); } else if (is_dir($output)) { $output .= $cache; }
		
		if ($properties['mime'] == 'image/jpeg') { imagejpeg($new, $output, 100); }
		else if ($properties['mime'] == 'image/gif') { imagegif($new, $output); }
		else if ($properties['mime'] == 'image/png') { imagepng($new, $output); }
		
		return $output;
		
	}
	
}

###############################################################

$GFX = new GFX;

###############################################################



###############################################################
#
# Class: Template
# Author: Neo Geek (NG)
#
###############################################################

class Template {

	var $tools = array();

	function Parse($template = '') {

		if (is_file($template)) { $template = file_get_contents($template); }

		preg_match('/<!--{header:start}-->(.*)<!--{header:end}-->/si', $template, $matches['header']);
		preg_match('/<!--{data:start}-->(.*)<!--{data:end}-->/si', $template, $matches['data']);
		preg_match('/<!--{nodata:start}-->(.*)<!--{nodata:end}-->/si', $template, $matches['nodata']);
		preg_match('/<!--{footer:start}-->(.*)<!--{footer:end}-->/si', $template, $matches['footer']);

		while (list($key, $value) = each($matches)) {

			$matches[$key] = isset($matches[$key][1])?$matches[$key][1]:'';

		}

		return $matches;

	}

	function Render($template, $data) {

		if (isset($data['header'])) { $data_header = $data['header']; unset($data['header']); }
		if (isset($data['footer'])) { $data_footer = $data['footer']; unset($data['footer']); }
		if (isset($data['data'])) { $data = $data['data']; }

		$output = '';

		if (!is_array($template) || is_file($template)) {

			$template = $this->Parse($template);

		}

		if (!is_array($template)) { return false; }

		if (isset($data_header)) {

			foreach ($data_header as $row) {

				$temp = isset($template['header'])?$template['header']:'';

				while (list($key, $value) = each($row)) { $temp = preg_replace('/%' . $key . '%/i', $value, $temp); }

				$output .= $temp;

			}

		} else {

			$output .= isset($template['header'])?$template['header']:'';

		}

		if (isset($data)) {

			foreach ($data as $row) {

				$temp = isset($template['data'])?$template['data']:'';

				while (list($key, $value) = each($row)) { $temp = preg_replace('/%' . $key . '%/i', $value, $temp); }

				$output .= $temp;

			}

		}

		if (!count($data)) {

			$output .= isset($template['nodata'])?$template['nodata']:'';

		}

		if (isset($data_footer)) {

			foreach ($data_footer as $row) {

				$temp = isset($template['footer'])?$template['footer']:'';

				while (list($key, $value) = each($row)) { $temp = preg_replace('/%' . $key . '%/i', $value, $temp); }

				$output .= $temp;

			}

		} else {

			$output .= isset($template['footer'])?$template['footer']:'';

		}

		return $output;

	}

	function Generate($data, $sortable = true, $render = true) {

		$output = '';

		if (!is_array($data) || !count($data)) { return false; }

		$db_sort_by = (isset($_GET['db_sort_by']) && is_simple($_GET['db_sort_by']))?$_GET['db_sort_by']:'';
		$db_sort_order = (isset($_GET['db_sort_order']) && is_simple($_GET['db_sort_order']))?strtolower($_GET['db_sort_order']):'asc';
		$db_start = (isset($_GET['db_start']) && is_simple_number($_GET['db_start']))?$_GET['db_start']:0;
		$db_limit = (isset($_GET['db_limit']) && is_simple_number($_GET['db_limit']))?$_GET['db_limit']:constant('maxview');

		if ($db_sort_order == 'asc') { $db_sort_order = 'desc'; } else { $db_sort_order = 'asc'; }

		$output .= '<!--{header:start}-->' . str_repeat(PHP_EOL, 2);
		$output .= '<table cellspacing="3" cellpadding="2" border="1">' . str_repeat(PHP_EOL, 2);

		$output .= '<tr>' . PHP_EOL;

		while (list($key, $value) = each($data[0])) {

			$tmp_url = url_query(array('db_sort_by'=>$key, 'db_sort_order'=>$db_sort_order));

			if ($db_sort_by == $key) { $tmp_class = 'sort_' . $db_sort_order; } else { $tmp_class = ''; }

			if ($sortable) {

				$output .= '<th>';
				$output .= '<a href="' . $tmp_url . '">' . $key . '</a>';

				if ($db_sort_by == $key && $db_sort_order == 'asc') { $output .= ' <span class="sort_desc">&darr;</span>'; }
				else if ($db_sort_by == $key && $db_sort_order == 'desc') { $output .= ' <span class="sort_asc">&uarr;</span>'; }

				$output .= '</th>' . PHP_EOL;

			} else { $output .= '<th>' . $key . '</th>' . PHP_EOL; }

		}

		reset($this->tools);

		while (list($key, $value) = each($this->tools)) {

			$output .= '<th>' . $value[0] . '</th>' . PHP_EOL;

		}

		$output .= '</tr>' . str_repeat(PHP_EOL, 2);

		$output .= '<!--{header:end}-->' . str_repeat(PHP_EOL, 2);

		$output .= '<!--{data:start}-->' . str_repeat(PHP_EOL, 2);

		$output .= '<tr>' . PHP_EOL;

		reset($data[0]);

		while (list($key, $value) = each($data[0])) {

			$output .= '<td>%' . strtoupper($key) . '%</td>' . PHP_EOL;

		}

		reset($this->tools);

		while (list($key, $value) = each($this->tools)) {

			$output .= '<td class="tools">' . $value[1] . '</td>' . PHP_EOL;

		}

		$output .= '</tr>' . str_repeat(PHP_EOL, 2);

		$output .= '<!--{data:end}-->' . str_repeat(PHP_EOL, 2);

		$output .= '<!--{footer:start}-->' . str_repeat(PHP_EOL, 2);
		$output .= '</table>' . str_repeat(PHP_EOL, 2);
		$output .= '<!--{footer:end}-->';

		if ($render) { return $this->Render($output, $data); }

		return $output;

	}

	function Pagination($total_rows = 0, $single_page_display = true) {

		global $DB;

		$output = '';

		if (!$total_rows) { return false; }

		$output .= '<div class="pagination">' . PHP_EOL;

		$output .= '<b>Page:</b> ' . PHP_EOL;

		if (!is_number($total_rows)) { $total_rows = count($total_rows); }

		$db_start = (isset($_GET['db_start']) && is_simple_number($_GET['db_start']))?$_GET['db_start']:0;
		$db_limit = (isset($_GET['db_limit']) && is_simple_number($_GET['db_limit']))?$_GET['db_limit']:constant('maxview');

		for ($i = 1; $i <= ceil($total_rows / $db_limit); $i++) {

			if ($db_start == (($i-1) * $db_limit)) { $output .= '<b>'; }

			$output .= '<a href="' . constant('page') . '' . url_query(array('db_start'=>(($i-1) * $db_limit))) . '">' . $i . '</a> ';

			if ($db_start == (($i-1) * $db_limit)) { $output .= '</b> '; }

			$output .= PHP_EOL;

		}

		$output .= '</div>' . str_repeat(PHP_EOL, 2);

		if (!$single_page_display && $total_rows <= constant('maxview')) { return false; }

		return $output;

	}

	function Form($database, $table, $data = array(), $fields = array()) {

		global $DB;

		$primary_key = '';

		$output = '';

		$columns = $DB->Query('SHOW COLUMNS FROM `' . $database . '`.`' . $table . '`', $DB->resource, 'resource', false);

		$action = str_replace('&', '&amp;', substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') +1));

		$output .= '<form action="' . $action . '" method="post">' . str_repeat(PHP_EOL, 2);

		while ($row = @mysql_fetch_assoc($columns)) {

			preg_match('/[a-zA-Z]+/', $row['Type'], $type);

			if (count($fields) && !in_array($row['Field'], $fields) && $row['Key'] != 'PRI') { continue; }

			if (count($data) && isset($data[0][$row['Field']])) { $value = $data[0][$row['Field']]; }
			else { $value = isset($row['Default'])?$row['Default']:''; }

			if ($row['Key'] != 'PRI') {

				$output .= '<label for="txt_' . $row['Field'] . '">' . ucwords(str_replace('_', ' ', $row['Field'])) . ':</label> ' . PHP_EOL;

				if (in_array($type[0], array('tinyblob', 'blob', 'mediumblob', 'longblob', 'tinytext', 'text', 'mediumtext', 'longtext'))) {

					$output .= '<textarea name="' . $row['Field'] . '" id="txt_' . $row['Field'] . '" cols="40" rows="5">' . htmlentities($value) . '</textarea><br />' . str_repeat(PHP_EOL, 2);


				} else {

					$output .= '<input name="' . $row['Field'] . '" id="txt_' . $row['Field'] . '" type="text" value="' . htmlentities($value) . '" size="40" /><br />' . str_repeat(PHP_EOL, 2);

				}

			} else {

				$output .= '<input name="' . $row['Field'] . '" id="txt_' . $row['Field'] . '" type="hidden" value="' . ($value?$value:0) . '" />' . str_repeat(PHP_EOL, 2);

				$primary_key = array('name'=>$row['Field'], 'value'=>$value);

			}

		}

		$output .= '<label>&nbsp;</label>' . PHP_EOL;
		if (isset($primary_key['value']) && $primary_key['value'] != 0) { $output .= '<button type="submit">Save</button> '; }
		else { $output .= '<button type="submit">Add</button> '; }
		$output .= '<button type="reset">Reset</button>' . str_repeat(PHP_EOL, 2);

		$output .= '</form>' . str_repeat(PHP_EOL, 2);

		return $output;

	}

}

###############################################################

$Template = new Template;

###############################################################

?>