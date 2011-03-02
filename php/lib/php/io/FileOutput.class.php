<?php

class php_io_FileOutput extends haxe_io_Output {
	public function __construct($f) {
		if( !php_Boot::$skip_constructor ) {
		$this->__f = $f;
	}}
	public $__f;
	public function writeByte($c) {
		$r = fwrite($this->__f, chr($c));
		if($r === false) {
			eval("if(isset(\$this)) \$»this =& \$this;throw new HException(haxe_io_Error::Custom(\"An error occurred\"));
				return \$»r;
			");
			return;
		}
		$r;
		return;
	}
	public function writeBytes($b, $p, $l) {
		$s = $b->readString($p, $l);
		if(feof($this->__f)) {
			return eval("if(isset(\$this)) \$»this =& \$this;throw new HException(new haxe_io_Eof());
				return \$»r;
			");
		}
		$r = fwrite($this->__f, $s, $l);
		if($r === false) {
			return eval("if(isset(\$this)) \$»this =& \$this;throw new HException(haxe_io_Error::Custom(\"An error occurred\"));
				return \$»r2;
			");
		}
		return $r;
	}
	public function flush() {
		$r = fflush($this->__f);
		if($r === false) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
	}
	public function close() {
		parent::close();
		if($this->__f !== null) {
			fclose($this->__f);
		}
	}
	public function seek($p, $pos) {
		$w = null;
		$»t = ($pos);
		switch($»t->index) {
		case 0:
		{
			$w = SEEK_SET;
		}break;
		case 1:
		{
			$w = SEEK_CUR;
		}break;
		case 2:
		{
			$w = SEEK_END;
		}break;
		}
		$r = fseek($this->__f, $p, $w);
		if($r === false) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
	}
	public function tell() {
		$r = ftell($this->__f);
		if($r === false) {
			return eval("if(isset(\$this)) \$»this =& \$this;throw new HException(haxe_io_Error::Custom(\"An error occurred\"));
				return \$»r;
			");
		}
		return $r;
	}
	public function eof() {
		return feof($this->__f);
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
	function __toString() { return 'php.io.FileOutput'; }
}
