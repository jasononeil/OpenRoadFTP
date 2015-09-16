<?php

class Reflect {
	public function __construct(){}
	static function field($o, $field) {
		return _hx_field($o, $field);
	}
	static function callMethod($o, $func, $args) {
		if(is_string($o) && !is_array($func)) {
			return call_user_func_array(Reflect::field($o, $func), $args->a);
		}
		return call_user_func_array(((is_callable($func)) ? $func : array($o, $func)), ((null === $args) ? array() : $args->a));
	}
	static function fields($o) {
		if($o === null) {
			return new _hx_array(array());
		}
		if($o instanceof _hx_array) {
			return new _hx_array(array('concat','copy','insert','iterator','length','join','pop','push','remove','reverse','shift','slice','sort','splice','toString','unshift'));
		} else {
			if(is_string($o)) {
				return new _hx_array(array('charAt','charCodeAt','indexOf','lastIndexOf','length','split','substr','toLowerCase','toString','toUpperCase'));
			} else {
				return new _hx_array(_hx_get_object_vars($o));
			}
		}
	}
	static function isFunction($f) {
		return (is_array($f) && is_callable($f)) || _hx_is_lambda($f) || is_array($f) && Reflect_0($f) && $f[1] !== "length";
	}
	static function isObject($v) {
		if($v === null) {
			return false;
		}
		if(is_object($v)) {
			return $v instanceof _hx_anonymous || Type::getClass($v) !== null;
		}
		return is_string($v) && !_hx_is_lambda($v);
	}
	function __toString() { return 'Reflect'; }
}
function Reflect_0(&$f) {
	{
		$o = $f[0];
		$field = $f[1];
		return _hx_has_field($o, $field);
	}
}
