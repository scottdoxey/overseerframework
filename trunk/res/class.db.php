<?php

###############################################################
#
# Class: DB
# Author: Neo Geek (NG)
#
###############################################################

class DB {

	public $resource = null;
	public $results = null;
	public $instances = array('connect'=>0, 'query'=>0);

	public function Connect($server, $username, $password, $database, $cache = true) {

		$resource = @mysql_connect($server, $username, $password, true) or error('MySQL Error: ' . mysql_error());

		if ($database) { @mysql_select_db($database, $resource) or error('MySQL Error: ' . mysql_error()); }

		if ($cache) { $this->resource = $resource; }

		$this->instances['connect']++;

		return $resource;

	}

	public function Query($query, $resource = null, $return = 'array', $cache = true) {

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

?>