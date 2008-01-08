<?php

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
	define('page', isset($_SERVER['PATH_INFO'])?substr($_SERVER['PATH_INFO'], 1):'');
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

?>