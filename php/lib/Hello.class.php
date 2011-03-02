<?php

class Hello {
	public function __construct(){}
	public $tpl;
	public function multiply() {
		haxe_Log::trace("Test this out!", _hx_anonymous(array("fileName" => "Hello.hx", "lineNumber" => 88, "className" => "Hello", "methodName" => "multiply")));
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function main() {
		$files = null;
		$tpl = null;
		$tpl = new jasononeil_tpl_HxTpl();
		$tpl->loadTemplateFromFile("./tpl/test.hxtpl");
		$tpl->assign("title", "The Title is Being Set Dynamically!!!!", null);
		$tpl->assign("subtitle", "And the subtitle too, showing that we can do many variables", null);
		$tpl->assignObject("page", _hx_anonymous(array("title" => "New Website", "url" => "http://google.com/myhouse.html", "urlParts" => _hx_anonymous(array("protocol" => "http://", "domain" => "google.com", "filename" => "myhouse", "extension" => ".html", "size" => 2)))), null);
		$errBlock = null;
		$errBlock = $tpl->setSwitch("error", true);
		$errBlock->assign("message", "NOT SO EPIC FAIL!", null);
		$m1 = null;
		$m2 = null;
		$m1 = $tpl->newLoop("menuItem");
		$m1->assignObject("page", _hx_anonymous(array("url" => "http://slashdot.org", "name" => "A big waste of time")), null);
		$m2 = $tpl->newLoop("menuItem");
		$m2->assignObject("page", _hx_anonymous(array("url" => "http://www.wbc.wa.edu.au", "name" => "One of my employers")), null);
		$content = null;
		$copyright = null;
		$copyright = $tpl->hinclude("copyright", null);
		$copyright->assign("year", "2009", null);
		$content = $tpl->hinclude("content", "./tpl/content.hxtpl");
		$content->assign("pageTitle", "The Blue Header", null);
		php_Lib::hprint($tpl->getOutput());
	}
	function __toString() { return 'Hello'; }
}
