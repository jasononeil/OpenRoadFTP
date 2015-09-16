<?php

class hxbase_util_Error {
	public function __construct($str, $pos = null) {
		if(!php_Boot::$skip_constructor) {
		$this->code = "ERROR";
		$this->error = $str;
		$this->explanation = "This is an error that hasn't been explained properly yet.";
		$this->suggestion = "You should try bribing Jason to fix it.";
		$this->pos = $pos;
		if(hxbase_util_Error::$errorTypes->exists($str)) {
			$type = hxbase_util_Error::$errorTypes->get($str);
			$this->code = $str;
			$this->error = $type->error;
			$this->explanation = $type->explanation;
			$this->suggestion = $type->suggestion;
		}
	}}
	public $code;
	public $error;
	public $explanation;
	public $suggestion;
	public $pos;
	public function toString() {
		return _hx_string_or_null($this->code) . ": " . _hx_string_or_null($this->error) . "\x0A\x0A" . _hx_string_or_null($this->explanation) . "\x0A\x0A" . _hx_string_or_null($this->suggestion);
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static $errorTypes;
	static function registerErrorType($code_in, $error_in = null, $explanation_in = null, $suggestion_in = null) {
		if($suggestion_in === null) {
			$suggestion_in = "";
		}
		if($explanation_in === null) {
			$explanation_in = "";
		}
		$type = _hx_anonymous(array("code" => $code_in, "error" => $error_in, "explanation" => $explanation_in, "suggestion" => $suggestion_in));
		hxbase_util_Error::$errorTypes->set($code_in, $type);
	}
	function __toString() { return $this->toString(); }
}
hxbase_util_Error::$errorTypes = new haxe_ds_StringMap();
