<?php

class Download {
	public function __construct(){}
	static function main() {
		$api = new Api();
		$api->checkLoggedIn();
		$params = php_Web::getParams();
		if($params->exists("key")) {
			$key = $params->get("key");
			$sessionKey = $api->session->get("DOWNLOAD.key");
			$str = str_replace($sessionKey, "", $key);
			if($str === "") {
				$url = $api->session->get("DOWNLOAD.url");
				
				
				header("Content-type: application/force-download");
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-length: ".filesize($url));
				header("Content-disposition: attachment;filename=\"".basename($url)."\""); 
				readfile($url); 
				unlink($url);
				
				;
			}
		}
	}
	function __toString() { return 'Download'; }
}
