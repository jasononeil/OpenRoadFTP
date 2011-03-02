<?php

class haxe_remoting_Context {
	public function __construct() {
		if( !php_Boot::$skip_constructor ) {
		$this->objects = new Hash();
	}}
	public $objects;
	public function addObject($name, $obj, $recursive) {
		$this->objects->set($name, _hx_anonymous(array("obj" => $obj, "rec" => $recursive)));
	}
	public function call($path, $params) {
		if($path->length < 2) {
			throw new HException("Invalid path '" . $path->join(".") . "'");
		}
		$inf = $this->objects->get($path[0]);
		if($inf === null) {
			throw new HException("No such object " . $path[0]);
		}
		$o = $inf->obj;
		$m = Reflect::field($o, $path[1]);
		if($path->length > 2) {
			if(!$inf->rec) {
				throw new HException("Can't access " . $path->join("."));
			}
			{
				$_g1 = 2; $_g = $path->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					$o = $m;
					$m = Reflect::field($o, $path[$i]);
					unset($i);
				}
			}
		}
		if(!Reflect::isFunction($m)) {
			throw new HException("No such method " . $path->join("."));
		}
		return Reflect::callMethod($o, $m, $params);
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function share($name, $obj) {
		$ctx = new haxe_remoting_Context();
		$ctx->addObject($name, $obj, null);
		return $ctx;
	}
	function __toString() { return 'haxe.remoting.Context'; }
}
