<?php

class Login {
	public function __construct(){}
	static function main() {
		$params = null;
		$username = null;
		$password = null;
		$ftp = null;
		$loginOkay = null;
		$error = null;
		$session = null;
		$session = new jasononeil_util_SessionHandler("WbcStudentLoginSessionID", null);
		$loginOkay = false;
		$params1 = php_Web::getParams();
		if($params1->exists("username") && $params1->exists("password")) {
			$username = $params1->get("username");
			$password = $params1->get("password");
			try {
				$ftp = new jasononeil_ftp_FtpConnection("localhost", $username, $password, "tmp/", null, null, null);
				$session->start()->set("username", $username)->set("password", $password);
				$loginOkay = true;
			}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			;
			if(is_string($e = $_ex_)){
				$error = $e;
			} else throw $»e; }
		}
		$msg = null;
		if(!$loginOkay) {
			$session->end();
			$msg = "FAILURE";
		}
		else {
			$msg = "SUCCESS";
		}
		php_Lib::hprint($msg);
	}
	function __toString() { return 'Login'; }
}
