<?php

class haxe_remoting_HttpConnection implements haxe_remoting_Connection{
	public function __construct($url, $path) {
		if(!php_Boot::$skip_constructor) {
		$this->__url = $url;
		$this->__path = $path;
	}}
	public $__url;
	public $__path;
	public function resolve($name) {
		$c = new haxe_remoting_HttpConnection($this->__url, $this->__path->copy());
		$c->__path->push($name);
		return $c;
	}
	public function call($params) {
		$data = null;
		$h = new haxe_Http($this->__url);
		$h->cnxTimeout = haxe_remoting_HttpConnection::$TIMEOUT;
		$s = new haxe_Serializer();
		$s->serialize($this->__path);
		$s->serialize($params);
		$h->setHeader("X-Haxe-Remoting", "1");
		$h->setParameter("__x", $s->toString());
		$h->onData = array(new _hx_lambda(array(&$data, &$h, &$params, &$s), "haxe_remoting_HttpConnection_0"), 'execute');
		$h->onError = array(new _hx_lambda(array(&$data, &$h, &$params, &$s), "haxe_remoting_HttpConnection_1"), 'execute');
		$h->request(true);
		if(_hx_substr($data, 0, 3) !== "hxr") {
			throw new HException("Invalid response : '" . $data . "'");
		}
		$data = _hx_substr($data, 3, null);
		return _hx_deref(new haxe_Unserializer($data))->unserialize();
	}
	public $»dynamics = array();
	public function __get($n) {
		if(isset($this->»dynamics[$n]))
			return $this->»dynamics[$n];
	}
	public function __set($n, $v) {
		$this->»dynamics[$n] = $v;
	}
	public function __call($n, $a) {
		if(isset($this->»dynamics[$n]) && is_callable($this->»dynamics[$n]))
			return call_user_func_array($this->»dynamics[$n], $a);
		if('toString' == $n)
			return $this->__toString();
		throw new HException("Unable to call «".$n."»");
	}
	static $TIMEOUT = 10;
	static function urlConnect($url) {
		return new haxe_remoting_HttpConnection($url, new _hx_array(array()));
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
			return "hxr" . $s->toString();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$s = new haxe_Serializer();
				$s->serializeException($e);
				return "hxr" . $s->toString();
			}
		}
	}
	function __toString() { return 'haxe.remoting.HttpConnection'; }
}
function haxe_remoting_HttpConnection_0(&$data, &$h, &$params, &$s, $d) {
	{
		$data = $d;
	}
}
function haxe_remoting_HttpConnection_1(&$data, &$h, &$params, &$s, $e) {
	{
		throw new HException($e);
	}
}
