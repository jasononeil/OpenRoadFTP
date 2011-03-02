<?php

class jasononeil_util_Misc {
	public function __construct(){}
	static function assert($cond, $assertion, $pos) {
		if($assertion === null) {
			$assertion = "";
		}
		if(!$cond) {
			$str = null;
			$str = "<b>Assertion failed in " . $pos->className . "." . $pos->methodName . "()</b>";
			if($assertion != "") {
				$str .= ": " . $assertion;
			}
			throw new HException($str);
		}
	}
	function __toString() { return 'jasononeil.util.Misc'; }
}
