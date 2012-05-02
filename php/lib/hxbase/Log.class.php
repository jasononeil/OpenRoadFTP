<?php

class hxbase_Log {
	public function __construct(){}
	static function log($type, $msg, $pos) {
		$str = null;
		$str = "<b>" . $type . "</b> ";
		$str .= "[" . $pos->className . "." . $pos->methodName . "() ";
		$str .= $pos->lineNumber . "] : ";
		$str .= $msg;
		php_Lib::hprint("<br>" . $str);
	}
	static function trace($v, $pos) {
		hxbase_Log::log(hxbase_LogType::$Trace, $v, $pos);
	}
	static function info($info, $pos) {
		hxbase_Log::log(hxbase_LogType::$Info, $info, $pos);
	}
	static function warning($warning, $pos) {
		hxbase_Log::log(hxbase_LogType::$Warning, $warning, $pos);
	}
	static function error($error, $pos) {
		hxbase_Log::log(hxbase_LogType::$Error, $error, $pos);
		throw new HException($error);
	}
	static function assert($cond, $desc, $pos) {
		if($desc === null) {
			$desc = "";
		}
		if(!$cond) {
			hxbase_Log::log(hxbase_LogType::$AssertionFailed, $desc, $pos);
		}
	}
	function __toString() { return 'hxbase.Log'; }
}
