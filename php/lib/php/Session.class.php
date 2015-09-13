<?php

class php_Session {
	public function __construct(){}
	static function setName($name) {
		if(php_Session::$started) {
			throw new HException("You can't set the name while the session is already in use");
		}
		session_name($name);
	}
	static function getId() {
		return session_id();
	}
	static function regenerateId($deleteold = null) {
		return session_regenerate_id($deleteold);
	}
	static function get($name) {
		php_Session::start();
		if(!isset($_SESSION[$name])) {
			return null;
		}
		return $_SESSION[$name];
	}
	static function set($name, $value) {
		php_Session::start();
		return $_SESSION[$name] = $value;
	}
	static function exists($name) {
		php_Session::start();
		return array_key_exists($name, $_SESSION);
	}
	static $started;
	static function start() {
		if(php_Session::$started) {
			return;
		}
		php_Session::$started = true;
		session_start();
	}
	function __toString() { return 'php.Session'; }
}
php_Session::$started = isset($_SESSION);
