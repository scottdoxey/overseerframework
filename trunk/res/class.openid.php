<?php

###############################################################
#
# Class: OpenID
# Author: Neo Geek (NG)
#
###############################################################

class OpenID {

	function Request($url = '', $options = array(), $canonize = false, $query = '') {

		if ($canonize) { $url = preg_replace(array('/([^:])\/+/', '/\/$/'), array('\1/', ''), $url); }

		if (function_exists('stream_get_contents')) { $contents = @stream_get_contents(@fopen($url, 'rb')); }
		else { $contents = file_get_contents($url); }

		preg_match('/<link.*(?:rel=["\']openid.server["\'].*href=["\'](.*)["\']|href=["\'](.*)["\'].*rel=["\']openid.server["\']).*>/U', $contents, $server);
		preg_match('/<link.*(?:rel=["\']openid.delegate["\'].*href=["\'](.*)["\']|href=["\'](.*)["\'].*rel=["\']openid.delegate["\']).*>/U', $contents, $delegate);

		if (isset($delegate[1])) { return OpenID::Request($delegate[1]); } else if (!isset($server[1])) { return false; }

		setcookie('openid', $server[1], time() +3600, '/');

		if (!isset($options['openid.identity'])) { $options['openid.identity'] = $url; }
		if (!isset($options['openid.mode'])) { $options['openid.mode'] = 'checkid_setup'; }
		if (!isset($options['openid.trust_root'])) { $options['openid.trust_root'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; }
		if (!isset($options['openid.return_to'])) { $options['openid.return_to'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; }

		while (list($key, $value) = each($options)) { $query .= $key . '=' . $value . '&'; }

		header('Location: ' . $server[1] . (preg_match('/\?/', $server[1])?'&':'?') . $query); exit;

		return false;

	}

	function Verify($query = '') {

		$_GET['openid_mode'] = 'check_authentication';

		while (list($key, $value) = each($_GET)) { $query .= preg_replace('/(openid|sreg)_/', '\1.', $key) . '=' . urlencode($value) . '&'; }

		if (function_exists('stream_get_contents')) {
			$results = @stream_get_contents(@fopen($_COOKIE['openid'], 'rb', false, @stream_context_create(array('http'=>array('method'=>'POST', 'content'=>$query)))));
		} else {
			$results = @file_get_contents($_COOKIE['openid'] . (preg_match('/\?/', $_COOKIE['openid'])?'&':'?') . $query);
		}

		if (preg_match('/true/', $results)) { return $_GET; }

		return false;

	}

}

$OpenID = new OpenID;

?>