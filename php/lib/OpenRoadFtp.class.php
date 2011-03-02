<?php

class OpenRoadFtp {
	public function __construct(){}
	static function main() {
		$tpl = null;
		$session = null;
		$tpl = new hxbase_tpl_HxTpl();
		$tpl->loadTemplateFromFile("./tpl/index.hxtpl");
		$tpl->assign("site.title", "WBC Student Server 1", null);
		$tpl->assignObject("site", _hx_anonymous(array("title" => "OpenRoad FTP", "subtitle" => "Student Logon", "copyright" => "Created 2010 Jason O'Neil.  Released under a GPL license.")), null);
		php_Lib::hprint("<?xml version=\"1.0\" encoding=\"utf-8\"?><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
		php_Lib::hprint($tpl->getOutput());
		new hxbase_util_Error("This is an error in the main body", _hx_anonymous(array("fileName" => "OpenRoadFtp.hx", "lineNumber" => 30, "className" => "OpenRoadFtp", "methodName" => "main")));
	}
	function __toString() { return 'OpenRoadFtp'; }
}
