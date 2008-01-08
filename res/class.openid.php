<?php

###############################################################
#
# Class: OpenID
# Author: Neo Geek (NG)
#
###############################################################

class OpenID
{
	
	var $settings = array();
	var $server_info = array();
	
	function Request($url = '', $query = '?') {
		
		if (!$url) { return false; }
		
		if (!isset($this->settings['openid.identity'])) { $this->settings['openid.identity'] = $url; }
		if (!isset($this->settings['openid.mode'])) { $this->settings['openid.mode'] = 'checkid_setup'; }
		if (!isset($this->settings['openid.trust_root'])) { $this->settings['openid.trust_root'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; }
		if (!isset($this->settings['openid.return_to'])) { $this->settings['openid.return_to'] = $this->settings['openid.trust_root']; }
		
		while (list($key, $value) = each($this->settings)) {
			if (is_array($value)) { $value = implode(',', $value); }
			$query .= $key . '=' . urlencode($value) . '&';
		}

		preg_match_all('/<link (.*)>/U', file_get_contents($url), $links);
		
		if (isset($links[1])) {
		
			foreach ($links[1] as $link) {
			
				preg_match('/rel="(.*)"/U', $link, $tmp_rel);
				preg_match('/href="(.*)"/U', $link, $tmp_href);
			
				if (isset($tmp_rel[1]) && $tmp_rel[1] == 'openid.server') { $this->server_info['openid.server'] = $tmp_href[1]; }
			
			}
		
		}
		
		if (isset($this->server_info['openid.server'])) { header('Location: ' . $this->server_info['openid.server'] . $query); }
		
		return false;
		
	}
	
	function Validate() {
		
		if (!isset($_GET['openid_op_endpoint'])) { return false; }
		
		$_GET['openid_mode'] = 'check_authentication';
		
		$query = $_GET['openid_op_endpoint'] . '?';
		
		while (list($key, $value) = each($_GET)) {
			$key = preg_replace(array('/(openid)_/', '/(sreg)_/'), '\1.', $key);
			$query .= $key . '=' . urlencode($value) . '&';
		}
		
		if (preg_match('/true/', file_get_contents($query), $matches)) { return true; }
		
		return false;
		
	}
	
}

$OpenID = new OpenID;

?>