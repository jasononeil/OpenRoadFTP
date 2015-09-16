<?php

class HList implements IteratorAggregate{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->length = 0;
	}}
	public $h;
	public $length;
	public function add($item) {
		$x = array($item, null);
		if($this->h === null) {
			$this->h =& $x;
		} else {
			$this->q[1] =& $x;
		}
		$this->q =& $x;
		$this->length++;
	}
	public function iterator() {
		return new _hx_list_iterator($this);
	}
	public function join($sep) {
		$s = "";
		$first = true;
		$l = $this->h;
		while($l !== null) {
			if($first) {
				$first = false;
			} else {
				$s .= _hx_string_or_null($sep);
			}
			$s .= Std::string($l[0]);
			$l = $l[1];
		}
		return $s;
	}
	public function filter($f) {
		$l2 = new HList();
		$l = $this->h;
		while($l !== null) {
			$v = $l[0];
			$l = $l[1];
			if(call_user_func_array($f, array($v))) {
				$l2->add($v);
			}
			unset($v);
		}
		return $l2;
	}
	public function getIterator() {
		return $this->iterator();
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
	function __toString() { return 'List'; }
}
