<?php

class haxe_remoting_HttpConnection implements haxe_remoting_Connection{
	public function __construct(){}
	public $__dynamics = array();
	public function __get($n) {
		if(isset($this->__dynamics[$n]))
			return $this->__dynamics[$n];
	}
	public function __set($n, $v) {
		$this->__dynamics[$n] = $v;
	}
	public function __call($n, $a) {
		if(isset($this->__dynamics[$n]) && is_callable($this->__dynamics[$n]))
			return call_user_func_array($this->__dynamics[$n], $a);
		if('toString' == $n)
			return $this->__toString();
		throw new HException("Unable to call <".$n.">");
	}
	static function handleRequest($ctx) {
		$v = php_Web::getParams()->get("__x");
		if(php_Web::getClientHeader("X-Haxe-Remoting") === null || $v === null) {
			return false;
		}
		php_Lib::hprint(haxe_remoting_HttpConnection::processRequest($v, $ctx));
		return true;
	}
	static function processRequest($requestData, $ctx) {
		try {
			$u = new haxe_Unserializer($requestData);
			$path = $u->unserialize();
			$args = $u->unserialize();
			$data = $ctx->call($path, $args);
			$s = new haxe_Serializer();
			$s->serialize($data);
			return "hxr" . _hx_string_or_null($s->toString());
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				$s1 = new haxe_Serializer();
				$s1->serializeException($e);
				return "hxr" . _hx_string_or_null($s1->toString());
			}
		}
	}
	function __toString() { return 'haxe.remoting.HttpConnection'; }
}
