<?php

class AppConfig {
	public function __construct(){}
	static $ftpServer = "localhost";
	static $siteTitle = "Geelong BC Student Logon";
	static $sessionID = "GeelongStudentLoginSessionID";
	static $sessionTimeOut = 3600;
	static $limitSymlinks = false;
	static $allowedSymlinks;
	static function getHomeDir($username) {
		$yeargroup = null;
		$home = null;
		$yearAtEndOfUsername = new EReg("[0-9]{4}\$", "");
		if($yearAtEndOfUsername->match($username)) {
			$yeargroup = $yearAtEndOfUsername->matched(0);
			$home = "/home/students/" . $yeargroup . "/" . $username;
		} else {
			$home = "/home/staff/" . $username;
		}
		return $home;
	}
	function __toString() { return 'AppConfig'; }
}
AppConfig::$allowedSymlinks = new _hx_array(array());
