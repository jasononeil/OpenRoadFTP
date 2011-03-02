<?php

class haxe_io_Output {
	public function __construct(){}
	public $bigEndian;
	public function writeByte($c) {
		throw new HException("Not implemented");
	}
	public function writeBytes($s, $pos, $len) {
		$k = $len;
		$b = $s->b;
		if($pos < 0 || $len < 0 || $pos + $len > $s->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		while($k > 0) {
			$this->writeByte(ord($b[$pos]));
			$pos++;
			$k--;
			;
		}
		return $len;
	}
	public function flush() {
		;
	}
	public function close() {
		;
	}
	public function setEndian($b) {
		$this->bigEndian = $b;
		return $b;
	}
	public function write($s) {
		$l = $s->length;
		$p = 0;
		while($l > 0) {
			$k = $this->writeBytes($s, $p, $l);
			if($k === 0) {
				throw new HException(haxe_io_Error::$Blocked);
			}
			$p += $k;
			$l -= $k;
			unset($k);
		}
	}
	public function writeFullBytes($s, $pos, $len) {
		while($len > 0) {
			$k = $this->writeBytes($s, $pos, $len);
			$pos += $k;
			$len -= $k;
			unset($k);
		}
	}
	public function writeFloat($x) {
		$this->write(haxe_io_Bytes::ofString(pack("f", $x)));
	}
	public function writeDouble($x) {
		$this->write(haxe_io_Bytes::ofString(pack("d", $x)));
	}
	public function writeInt8($x) {
		if($x < -128 || $x >= 128) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		$this->writeByte($x & 255);
	}
	public function writeInt16($x) {
		if($x < -32768 || $x >= 32768) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		$this->writeUInt16($x & 65535);
	}
	public function writeUInt16($x) {
		if($x < 0 || $x >= 65536) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		if($this->bigEndian) {
			$this->writeByte($x >> 8);
			$this->writeByte($x & 255);
		}
		else {
			$this->writeByte($x & 255);
			$this->writeByte($x >> 8);
		}
	}
	public function writeInt24($x) {
		if($x < -8388608 || $x >= 8388608) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		$this->writeUInt24($x & 16777215);
	}
	public function writeUInt24($x) {
		if($x < 0 || $x >= 16777216) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		if($this->bigEndian) {
			$this->writeByte($x >> 16);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte($x & 255);
		}
		else {
			$this->writeByte($x & 255);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte($x >> 16);
		}
	}
	public function writeInt31($x) {
		if($x < -1073741824 || $x >= 1073741824) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		if($this->bigEndian) {
			$this->writeByte(_hx_shift_right($x, 24));
			$this->writeByte(($x >> 16) & 255);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte($x & 255);
		}
		else {
			$this->writeByte($x & 255);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte(($x >> 16) & 255);
			$this->writeByte(_hx_shift_right($x, 24));
		}
	}
	public function writeUInt30($x) {
		if($x < 0 || $x >= 1073741824) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		if($this->bigEndian) {
			$this->writeByte(_hx_shift_right($x, 24));
			$this->writeByte(($x >> 16) & 255);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte($x & 255);
		}
		else {
			$this->writeByte($x & 255);
			$this->writeByte(($x >> 8) & 255);
			$this->writeByte(($x >> 16) & 255);
			$this->writeByte(_hx_shift_right($x, 24));
		}
	}
	public function writeInt32($x) {
		if($this->bigEndian) {
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x1 = _hx_shift_right((\$x), 24);
				if((((\$x1) >> 30) & 1) !== (_hx_shift_right((\$x1), 31))) {
					throw new HException(\"Overflow \" . \$x1);
				}
				\$팿 = ((\$x1) & -1);
				return \$팿;
			"));
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x12 = _hx_shift_right((\$x), 16);
				if((((\$x12) >> 30) & 1) !== (_hx_shift_right((\$x12), 31))) {
					throw new HException(\"Overflow \" . \$x12);
				}
				\$팿2 = ((\$x12) & -1);
				return \$팿2;
			") & 255);
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x13 = _hx_shift_right((\$x), 8);
				if((((\$x13) >> 30) & 1) !== (_hx_shift_right((\$x13), 31))) {
					throw new HException(\"Overflow \" . \$x13);
				}
				\$팿3 = ((\$x13) & -1);
				return \$팿3;
			") & 255);
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x14 = (\$x) & 255;
				if((((\$x14) >> 30) & 1) !== (_hx_shift_right((\$x14), 31))) {
					throw new HException(\"Overflow \" . \$x14);
				}
				\$팿4 = ((\$x14) & -1);
				return \$팿4;
			"));
		}
		else {
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x15 = (\$x) & 255;
				if((((\$x15) >> 30) & 1) !== (_hx_shift_right((\$x15), 31))) {
					throw new HException(\"Overflow \" . \$x15);
				}
				\$팿5 = ((\$x15) & -1);
				return \$팿5;
			"));
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x16 = _hx_shift_right((\$x), 8);
				if((((\$x16) >> 30) & 1) !== (_hx_shift_right((\$x16), 31))) {
					throw new HException(\"Overflow \" . \$x16);
				}
				\$팿6 = ((\$x16) & -1);
				return \$팿6;
			") & 255);
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x17 = _hx_shift_right((\$x), 16);
				if((((\$x17) >> 30) & 1) !== (_hx_shift_right((\$x17), 31))) {
					throw new HException(\"Overflow \" . \$x17);
				}
				\$팿7 = ((\$x17) & -1);
				return \$팿7;
			") & 255);
			$this->writeByte(eval("if(isset(\$this)) \$퍁his =& \$this;\$x18 = _hx_shift_right((\$x), 24);
				if((((\$x18) >> 30) & 1) !== (_hx_shift_right((\$x18), 31))) {
					throw new HException(\"Overflow \" . \$x18);
				}
				\$팿8 = ((\$x18) & -1);
				return \$팿8;
			"));
		}
	}
	public function prepare($nbytes) {
		;
	}
	public function writeInput($i, $bufsize) {
		if($bufsize === null) {
			$bufsize = 4096;
		}
		$buf = haxe_io_Bytes::alloc($bufsize);
		try {
			while(true) {
				$len = $i->readBytes($buf, 0, $bufsize);
				if($len === 0) {
					throw new HException(haxe_io_Error::$Blocked);
				}
				$p = 0;
				while($len > 0) {
					$k = $this->writeBytes($buf, $p, $len);
					if($k === 0) {
						throw new HException(haxe_io_Error::$Blocked);
					}
					$p += $k;
					$len -= $k;
					unset($k);
				}
				unset($p,$len,$k);
			}
		}catch(Exception $팫) {
		$_ex_ = ($팫 instanceof HException) ? $팫->e : $팫;
		;
		if(($e = $_ex_) instanceof haxe_io_Eof){
			;
		} else throw $팫; }
	}
	public function writeString($s) {
		$b = haxe_io_Bytes::ofString($s);
		$this->writeFullBytes($b, 0, $b->length);
	}
	function __toString() { return 'haxe.io.Output'; }
}
