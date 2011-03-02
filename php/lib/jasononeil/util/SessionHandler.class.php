<?php

class jasononeil_util_SessionHandler {
	public function __construct($name_in, $timeout_in) {
		if( !php_Boot::$skip_constructor ) {
		if($timeout_in === null) {
			$timeout_in = 300;
		}
		$this->name = $name_in;
		$this->timeout = $timeout_in;
		$this->registerErrorMessages();
		php_Session::setName($this->name);
		$this->isSessionOkay = false;
	}}
	public $name;
	public $timeout;
	public $isSessionOkay;
	public function registerErrorMessages() {
		jasononeil_util_Error::registerErrorType("SESSION.TIMEOUT", "Session timed out.", null, null);
		jasononeil_util_Error::registerErrorType("SESSION.NO_SESSION", "No existing session found.", null, null);
	}
	public function check() {
		if(php_Session::exists("SESSION.active")) {
			$this->checkSecurity();
			php_Session::regenerateId(null);
			$this->set("SESSION.lastUsed", Date::now()->getTime());
		}
		else {
			throw new HException(new jasononeil_util_Error("SESSION.NO_SESSION", _hx_anonymous(array("fileName" => "SessionHandler.hx", "lineNumber" => 44, "className" => "jasononeil.util.SessionHandler", "methodName" => "check"))));
		}
	}
	public function start() {
		php_Session::start();
		php_Session::regenerateId(null);
		$agent = $_SERVER["HTTP_USER_AGENT"];
		php_Session::set("SESSION.active", true);
		php_Session::set("SESSION.agent", $agent);
		php_Session::set("SESSION.ip", $_SERVER['REMOTE_ADDR']);
		php_Session::set("SESSION.lastUsed", Date::now()->getTime());
		php_Session::set("SESSION.firstID", php_Session::getId());
		$this->isSessionOkay = true;
		return $this;
	}
	public function end() {
		$sessionName = $this->name;
		if(isset($_COOKIE[$sessionName])) {
			setcookie($sessionName, "", (time()) - 3600, "/");
		}
		session_write_close();
		$_SESSION = array();
		$this->isSessionOkay = false;
	}
	public function set($name_in, $value_in) {
		php_Session::set($name_in, $value_in);
		return $this;
	}
	public function get($name_in) {
		$r = null;
		$r = php_Session::get($name_in);
		return $r;
	}
	public function checkSecurity() {
		$oldTime = php_Session::get("SESSION.lastUsed");
		$oldAgent = php_Session::get("SESSION.agent");
		$oldIP = php_Session::get("SESSION.ip");
		$newTime = Date::now()->getTime();
		$newAgent = $_SERVER["HTTP_USER_AGENT"];
		$newIP = $_SERVER['REMOTE_ADDR'];
		$isSecurityOkay = false;
		if($newTime - $oldTime < 1000 * $this->timeout) {
			if($newAgent == $oldAgent) {
				if($newIP == $oldIP) {
					$isSecurityOkay = true;
				}
			}
		}
		else {
			throw new HException(new jasononeil_util_Error("SESSION.TIMEOUT", _hx_anonymous(array("fileName" => "SessionHandler.hx", "lineNumber" => 127, "className" => "jasononeil.util.SessionHandler", "methodName" => "checkSecurity"))));
		}
		return $isSecurityOkay;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return 'jasononeil.util.SessionHandler'; }
}
