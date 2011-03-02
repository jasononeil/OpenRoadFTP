<?php

class Lambda {
	public function __construct(){}
	static function harray($it) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$a = new _hx_array(array());
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$i = $»it->next();
		$a->push($i);
		}
		return $a;
	}
	static function hlist($it) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$l = new HList();
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$i = $»it->next();
		$l->add($i);
		}
		return $l;
	}
	static function map($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$l = new HList();
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		$l->add(call_user_func_array($f, array($x)));
		}
		return $l;
	}
	static function mapi($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$l = new HList();
		$i = 0;
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		$l->add(call_user_func_array($f, array($i++, $x)));
		}
		return $l;
	}
	static function has($it, $elt, $cmp) {
		if($it === null) {
			throw new HException("null iterable");
		}
		if($cmp === null) {
			$»it = $it->iterator();
			while($»it->hasNext()) {
			$x = $»it->next();
			if($x === $elt) {
				return true;
			}
			}
		}
		else {
			$»it2 = $it->iterator();
			while($»it2->hasNext()) {
			$x2 = $»it2->next();
			if(call_user_func_array($cmp, array($x2, $elt))) {
				return true;
			}
			}
		}
		return false;
	}
	static function exists($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		if(call_user_func_array($f, array($x))) {
			return true;
		}
		}
		return false;
	}
	static function hforeach($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		if(!call_user_func_array($f, array($x))) {
			return false;
		}
		}
		return true;
	}
	static function iter($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		call_user_func_array($f, array($x));
		}
	}
	static function filter($it, $f) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$l = new HList();
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		if(call_user_func_array($f, array($x))) {
			$l->add($x);
		}
		}
		return $l;
	}
	static function fold($it, $f, $first) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$x = $»it->next();
		$first = call_user_func_array($f, array($x, $first));
		}
		return $first;
	}
	static function count($it) {
		if($it === null) {
			throw new HException("null iterable");
		}
		$n = 0;
		$»it = $it->iterator();
		while($»it->hasNext()) {
		$_ = $»it->next();
		++$n;
		}
		return $n;
	}
	static function hempty($it) {
		if($it === null) {
			throw new HException("null iterable");
		}
		return !$it->iterator()->hasNext();
	}
	function __toString() { return 'Lambda'; }
}
