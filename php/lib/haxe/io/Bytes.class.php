<?php

class haxe_io_Bytes {
	public function __construct($length, $b) {
		if( !php_Boot::$skip_constructor ) {
		$this->length = $length;
		$this->b = $b;
	}}
	public $length;
	public $b;
	public function get($pos) {
		return ord($this->b[$pos]);
	}
	public function set($pos, $v) {
		$this->b[$pos] = chr($v);
	}
	public function blit($pos, $src, $srcpos, $len) {
		if($pos < 0 || $srcpos < 0 || $len < 0 || $pos + $len > $this->length || $srcpos + $len > $src->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		$this->b = substr($this->b, 0, $pos) . substr($src->b, $srcpos, $len) . substr($this->b, $pos+$len);
	}
	public function sub($pos, $len) {
		if($pos < 0 || $len < 0 || $pos + $len > $this->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		return new haxe_io_Bytes($len, substr($this->b, $pos, $len));
	}
	public function compare($other) {
		return $this->b < $other->b ? -1 : ($this->b == $other->b ? 0 : 1);
	}
	public function readString($pos, $len) {
		if($pos < 0 || $len < 0 || $pos + $len > $this->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		return substr($this->b, $pos, $len);
	}
	public function toString() {
		return $this->b;
	}
	public function getData() {
		return $this->b;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function alloc($length) {
		return new haxe_io_Bytes($length, str_repeat(chr(0), $length));
	}
	static function ofString($s) {
		return new haxe_io_Bytes(strlen($s), $s);
	}
	static function ofData($b) {
		return new haxe_io_Bytes(strlen($b), $b);
	}
	function __toString() { return $this->toString(); }
}
