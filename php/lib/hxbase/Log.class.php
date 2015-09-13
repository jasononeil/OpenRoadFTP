<?php

class hxbase_Log {
	public function __construct(){}
	static function log($type, $msg, $pos) {
		$str = null;
		$str = "<b>" . Std::string($type) . "</b> ";
		$str .= "[" . _hx_string_or_null($pos->className) . "." . _hx_string_or_null($pos->methodName) . "() ";
		$str .= _hx_string_rec($pos->lineNumber, "") . "] : ";
		$str .= _hx_string_or_null($msg);
		php_Lib::hprint("<br>" . _hx_string_or_null($str));
	}
	static function trace($v, $pos = null) {
		hxbase_Log::log(hxbase_LogType::$Trace, $v, $pos);
	}
	static function info($info, $pos = null) {
		hxbase_Log::log(hxbase_LogType::$Info, $info, $pos);
	}
	static function warning($warning, $pos = null) {
		hxbase_Log::log(hxbase_LogType::$Warning, $warning, $pos);
	}
	static function error($error, $pos = null) {
		hxbase_Log::log(hxbase_LogType::$Error, $error, $pos);
		throw new HException($error);
	}
	static function assert($cond, $desc = null, $pos = null) {
		if($desc === null) {
			$desc = "";
		}
		if(!$cond) {
			hxbase_Log::log(hxbase_LogType::$AssertionFailed, $desc, $pos);
		}
	}
	function __toString() { return 'hxbase.Log'; }
}
