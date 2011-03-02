<?php

class jasononeil_util_Error {
	public function __construct($str, $pos_in) {
		if( !php_Boot::$skip_constructor ) {
		if(jasononeil_util_Error::$errorTypes === null) {
			jasononeil_util_Error::init();
		}
		$this->code = "ERROR";
		$this->error = $str;
		$this->explanation = "This is an error that hasn't been explained properly.  ";
		$this->suggestion = "You could try bribing Jason to fix it.";
		$this->pos = $pos_in;
		if(jasononeil_util_Error::$errorTypes->exists($str)) {
			$type = jasononeil_util_Error::$errorTypes->get($str);
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
		return $this->code . ": " . $this->error . "\x0A\x0A" . $this->explanation . "\x0A\x0A" . $this->suggestion;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static $errorTypes;
	static function init() {
		jasononeil_util_Error::$errorTypes = new Hash();
	}
	static function registerErrorType($code_in, $error_in, $explanation_in, $suggestion_in) {
		if($suggestion_in === null) {
			$suggestion_in = "";
		}
		if($explanation_in === null) {
			$explanation_in = "";
		}
		if(jasononeil_util_Error::$errorTypes === null) {
			jasononeil_util_Error::init();
		}
		$type = null;
		$type = _hx_anonymous(array("code" => $code_in, "error" => $error_in, "explanation" => $explanation_in, "suggestion" => $suggestion_in));
		jasononeil_util_Error::$errorTypes->set($code_in, $type);
	}
	function __toString() { return $this->toString(); }
}
