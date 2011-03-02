<?php

class StudentLogin {
	public function __construct(){}
	static function main() {
		$tpl = null;
		$session = null;
		$tpl = new jasononeil_tpl_HxTpl();
		$tpl->loadTemplateFromFile("./tpl/index.hxtpl");
		$tpl->assign("site.title", "WBC Student Server 1", null);
		$tpl->assignObject("site", _hx_anonymous(array("title" => "WBC Student Logon", "subtitle" => "Student Logon", "copyright" => "Copyright 2010 Jason O'Neil")), null);
		php_Lib::hprint("<?xml version=\"1.0\" encoding=\"utf-8\"?><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
		php_Lib::hprint($tpl->getOutput());
		new jasononeil_util_Error("This is an error in the main body", _hx_anonymous(array("fileName" => "StudentLogin.hx", "lineNumber" => 30, "className" => "StudentLogin", "methodName" => "main")));
	}
	function __toString() { return 'StudentLogin'; }
}
