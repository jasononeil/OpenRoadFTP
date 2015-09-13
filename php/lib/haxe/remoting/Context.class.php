<?php

class haxe_remoting_Context {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->objects = new haxe_ds_StringMap();
	}}
	public $objects;
	public function addObject($name, $obj, $recursive = null) {
		$this->objects->set($name, _hx_anonymous(array("obj" => $obj, "rec" => $recursive)));
	}
	public function call($path, $params) {
		if($path->length < 2) {
			throw new HException("Invalid path '" . _hx_string_or_null($path->join(".")) . "'");
		}
		$inf = $this->objects->get($path[0]);
		if($inf === null) {
			throw new HException("No such object " . _hx_string_or_null($path[0]));
		}
		$o = $inf->obj;
		$m = Reflect::field($o, $path[1]);
		if($path->length > 2) {
			if(!$inf->rec) {
				throw new HException("Can't access " . _hx_string_or_null($path->join(".")));
			}
			{
				$_g1 = 2;
				$_g = $path->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					$o = $m;
					$m = Reflect::field($o, $path[$i]);
					unset($i);
				}
			}
		}
		if(!Reflect::isFunction($m)) {
			throw new HException("No such method " . _hx_string_or_null($path->join(".")));
		}
		return Reflect::callMethod($o, $m, $params);
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
	function __toString() { return 'haxe.remoting.Context'; }
}
