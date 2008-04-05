<?php

###############################################################
#
# Class: Template
# Author: Neo Geek (NG)
#
###############################################################

class Template {

	public $tools = array();

	public function Parse($template = '') {

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

	public function Render($template, $data) {

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

	public function Generate($data, $sortable = true, $render = true) {

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

	public function Pagination($total_rows = 0, $single_page_display = true) {

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