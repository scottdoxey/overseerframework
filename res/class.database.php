<?php

###############################################################
#
# Class: Database
# Author: Neo Geek (NG)
#
###############################################################

class Database {

	public $resource = null;
	public $results = null;

	public function DatabaseList() {

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

	public function TableList($database) {

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

	public function Table($database, $table, $fields = '*', $clause = null) {

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

	public function Process($database, $table, $variables = array()) {

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

				if (is_number($value) || $value == 'NOW()') { $updates[] = '`' . $row['Field'] . '` = ' . $value . ''; } else {
					$updates[] = '`' . $row['Field'] . '` = "' . $value . '"';
				}

			} else if ($row['Key'] == 'PRI') { $primary_key = $row['Field']; }

		}

		if (!count($updates)) { return error('MySQL Error: None of the included key/value sets can update this table.'); }

		if (isset($variables[$primary_key])) {
			$results = $DB->Query('SELECT `' . $primary_key . '` FROM `' . $database . '`.`' . $table . '` WHERE `' . $primary_key . '` = ' . $variables[$primary_key] . '', $resource, 'array', false);
		}

		if (!isset($results) || !count($results)) {
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

?>