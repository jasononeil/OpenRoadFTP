<?php

class php_Lib {
	public function __construct(){}
	static function hprint($v) {
		echo(Std::string($v));
	}
	static function println($v) {
		php_Lib::hprint($v);
		php_Lib::hprint("\x0A");
	}
	static function dump($v) {
		var_dump($v);
	}
	static function serialize($v) {
		return serialize($v);
	}
	static function unserialize($s) {
		return unserialize($s);
	}
	static function extensionLoaded($name) {
		return extension_loaded($name);
	}
	static function isCli() {
		return (0 == strncasecmp(PHP_SAPI, 'cli', 3));
	}
	static function printFile($file) {
		return fpassthru(fopen($file, "r"));
	}
	static function toPhpArray($a) {
		return $a->»a;
	}
	static function toHaxeArray($a) {
		return new _hx_array($a);
	}
	static function hashOfAssociativeArray($arr) {
		$h = new Hash();
		reset($arr); while(list($k, $v) = each($arr)) $h->set($k, $v);
		return $h;
	}
	static function associativeArrayOfHash($hash) {
		return $hash->h;
	}
	static function rethrow($e) {
		if(isset($»e)) throw $»e;
		if(Std::is($e, _hx_qtype("php.Exception"))) {
			$__rtex__ = $e;
			throw $__rtex__;
		}
		else {
			throw new HException($e);
		}
	}
	static function appendType($o, $path, $t) {
		$name = $path->shift();
		if($path->length === 0) {
			$o->$name = $t;
		}
		else {
			$so = (isset($o->$name) ? $o->$name : _hx_anonymous(array()));
			php_Lib::appendType($so, $path, $t);
			$o->$name = $so;
		}
	}
	static function getClasses() {
		$path = null;
		$o = _hx_anonymous(array());
		reset(php_Boot::$qtypes);
		while(($path = key(php_Boot::$qtypes)) !== null) {
			php_Lib::appendType($o, _hx_explode(".", $path), php_Boot::$qtypes[$path]);
			next(php_Boot::$qtypes);
			;
		}
		return $o;
	}
	function __toString() { return 'php.Lib'; }
}
