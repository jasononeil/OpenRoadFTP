<?php

class php_Web {
	public function __construct(){}
	static function getParams() {
		$a = array_merge($_GET, $_POST);
		if(get_magic_quotes_gpc()) {
			reset($a); while(list($k, $v) = each($a)) $a[$k] = stripslashes((string)$v);
		}
		return php_Lib::hashOfAssociativeArray($a);
	}
	static function getURI() {
		$s = $_SERVER['REQUEST_URI'];
		return _hx_array_get(_hx_explode("?", $s), 0);
	}
	static function getClientHeader($k) {
		$k1 = null;
		{
			$s = strtoupper($k);
			$k1 = str_replace("-", "_", $s);
		}
		if(null == php_Web::getClientHeaders()) throw new HException('null iterable');
		$__hx__it = php_Web::getClientHeaders()->iterator();
		while($__hx__it->hasNext()) {
			$i = $__hx__it->next();
			if($i->header === $k1) {
				return $i->value;
			}
		}
		return null;
	}
	static $_client_headers;
	static function getClientHeaders() {
		if(php_Web::$_client_headers === null) {
			php_Web::$_client_headers = new HList();
			$h = php_Lib::hashOfAssociativeArray($_SERVER);
			if(null == $h) throw new HException('null iterable');
			$__hx__it = $h->keys();
			while($__hx__it->hasNext()) {
				$k = $__hx__it->next();
				if(_hx_substr($k, 0, 5) === "HTTP_") {
					php_Web::$_client_headers->add(_hx_anonymous(array("header" => _hx_substr($k, 5, null), "value" => $h->get($k))));
				} else {
					if(_hx_substr($k, 0, 8) === "CONTENT_") {
						php_Web::$_client_headers->add(_hx_anonymous(array("header" => $k, "value" => $h->get($k))));
					}
				}
			}
		}
		return php_Web::$_client_headers;
	}
	static $isModNeko;
	function __toString() { return 'php.Web'; }
}
php_Web::$isModNeko = !php_Lib::isCli();
