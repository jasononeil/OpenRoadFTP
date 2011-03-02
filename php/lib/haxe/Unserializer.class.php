<?php

class haxe_Unserializer {
	public function __construct($buf) {
		if( !php_Boot::$skip_constructor ) {
		$this->buf = $buf;
		$this->length = strlen($buf);
		$this->pos = 0;
		$this->scache = new _hx_array(array());
		$this->cache = new _hx_array(array());
		$this->setResolver(haxe_Unserializer::$DEFAULT_RESOLVER);
	}}
	public $buf;
	public $pos;
	public $length;
	public $cache;
	public $scache;
	public $resolver;
	public function setResolver($r) {
		if($r === null) {
			$this->resolver = _hx_anonymous(array("resolveClass" => array(new _hx_lambda(array("r" => &$r), null, array('_'), "{
				return null;
			}"), 'execute1'), "resolveEnum" => array(new _hx_lambda(array("r" => &$r), null, array('_'), "{
				return null;
			}"), 'execute1')));
		}
		else {
			$this->resolver = $r;
		}
	}
	public function get($p) {
		return _hx_char_code_at($this->buf, $p);
	}
	public function readDigits() {
		$k = 0;
		$s = false;
		$fpos = $this->pos;
		while(true) {
			$c = _hx_char_code_at($this->buf, $this->pos);
			if($c === null) {
				break;
			}
			if($c === 45) {
				if($this->pos !== $fpos) {
					break;
				}
				$s = true;
				$this->pos++;
				continue;
			}
			$c -= 48;
			if($c < 0 || $c > 9) {
				break;
			}
			$k = $k * 10 + $c;
			$this->pos++;
			unset($c);
		}
		if($s) {
			$k *= -1;
		}
		return $k;
	}
	public function unserializeObject($o) {
		while(true) {
			if($this->pos >= $this->length) {
				throw new HException("Invalid object");
			}
			if(_hx_char_code_at($this->buf, $this->pos) === 103) {
				break;
			}
			$k = $this->unserialize();
			if(!Std::is($k, _hx_qtype("String"))) {
				throw new HException("Invalid object key");
			}
			$v = $this->unserialize();
			$o->{$k} = $v;
			unset($v,$k);
		}
		$this->pos++;
	}
	public function unserializeEnum($edecl, $tag) {
		$constr = Reflect::field($edecl, $tag);
		if($constr === null) {
			throw new HException("Unknown enum tag " . Type::getEnumName($edecl) . "." . $tag);
		}
		if(_hx_char_code_at($this->buf, $this->pos++) !== 58) {
			throw new HException("Invalid enum format");
		}
		$nargs = $this->readDigits();
		if($nargs === 0) {
			$this->cache->push($constr);
			return $constr;
		}
		$args = new _hx_array(array());
		while($nargs > 0) {
			$args->push($this->unserialize());
			$nargs -= 1;
			;
		}
		$e = Reflect::callMethod($edecl, $constr, $args);
		$this->cache->push($e);
		return $e;
	}
	public function unserialize() {
		switch(_hx_char_code_at($this->buf, $this->pos++)) {
		case 110:{
			return null;
		}break;
		case 116:{
			return true;
		}break;
		case 102:{
			return false;
		}break;
		case 122:{
			return 0;
		}break;
		case 105:{
			return $this->readDigits();
		}break;
		case 100:{
			$p1 = $this->pos;
			while(true) {
				$c = _hx_char_code_at($this->buf, $this->pos);
				if(($c >= 43 && $c < 58) || $c === 101 || $c === 69) {
					$this->pos++;
				}
				else {
					break;
				}
				unset($c);
			}
			return Std::parseFloat(_hx_substr($this->buf, $p1, $this->pos - $p1));
		}break;
		case 121:{
			$len = $this->readDigits();
			if(substr($this->buf, $this->pos++, 1) != ":" || $this->length - $this->pos < $len) {
				throw new HException("Invalid string length");
			}
			$s = _hx_substr($this->buf, $this->pos, $len);
			$this->pos += $len;
			$s = urldecode($s);
			$this->scache->push($s);
			return $s;
		}break;
		case 107:{
			return Math::$NaN;
		}break;
		case 109:{
			return Math::$NEGATIVE_INFINITY;
		}break;
		case 112:{
			return Math::$POSITIVE_INFINITY;
		}break;
		case 97:{
			$buf = $this->buf;
			$a = new _hx_array(array());
			$this->cache->push($a);
			while(true) {
				$c2 = _hx_char_code_at($this->buf, $this->pos);
				if($c2 === 104) {
					$this->pos++;
					break;
				}
				if($c2 === 117) {
					$this->pos++;
					$n = $this->readDigits();
					$a[$a->length + $n - 1] = null;
				}
				else {
					$a->push($this->unserialize());
				}
				unset($n,$c2);
			}
			return $a;
		}break;
		case 111:{
			$o = _hx_anonymous(array());
			$this->cache->push($o);
			$this->unserializeObject($o);
			return $o;
		}break;
		case 114:{
			$n2 = $this->readDigits();
			if($n2 < 0 || $n2 >= $this->cache->length) {
				throw new HException("Invalid reference");
			}
			return $this->cache[$n2];
		}break;
		case 82:{
			$n3 = $this->readDigits();
			if($n3 < 0 || $n3 >= $this->scache->length) {
				throw new HException("Invalid string reference");
			}
			return $this->scache[$n3];
		}break;
		case 120:{
			throw new HException($this->unserialize());
		}break;
		case 99:{
			$name = $this->unserialize();
			$cl = $this->resolver->resolveClass($name);
			if($cl === null) {
				throw new HException("Class not found " . $name);
			}
			$o2 = Type::createEmptyInstance($cl);
			$this->cache->push($o2);
			$this->unserializeObject($o2);
			return $o2;
		}break;
		case 119:{
			$name2 = $this->unserialize();
			$edecl = $this->resolver->resolveEnum($name2);
			if($edecl === null) {
				throw new HException("Enum not found " . $name2);
			}
			return $this->unserializeEnum($edecl, $this->unserialize());
		}break;
		case 106:{
			$name3 = $this->unserialize();
			$edecl2 = $this->resolver->resolveEnum($name3);
			if($edecl2 === null) {
				throw new HException("Enum not found " . $name3);
			}
			$this->pos++;
			$index = $this->readDigits();
			$tag = _hx_array_get(Type::getEnumConstructs($edecl2), $index);
			if($tag === null) {
				throw new HException("Unknown enum index " . $name3 . "@" . $index);
			}
			return $this->unserializeEnum($edecl2, $tag);
		}break;
		case 108:{
			$l = new HList();
			$this->cache->push($l);
			$buf2 = $this->buf;
			while(_hx_char_code_at($this->buf, $this->pos) !== 104) $l->add($this->unserialize());
			$this->pos++;
			return $l;
		}break;
		case 98:{
			$h = new Hash();
			$this->cache->push($h);
			$buf3 = $this->buf;
			while(_hx_char_code_at($this->buf, $this->pos) !== 104) {
				$s2 = $this->unserialize();
				$h->set($s2, $this->unserialize());
				unset($s2);
			}
			$this->pos++;
			return $h;
		}break;
		case 113:{
			$h2 = new IntHash();
			$this->cache->push($h2);
			$buf4 = $this->buf;
			$c3 = _hx_char_code_at($this->buf, $this->pos++);
			while($c3 === 58) {
				$i = $this->readDigits();
				$h2->set($i, $this->unserialize());
				$c3 = _hx_char_code_at($this->buf, $this->pos++);
				unset($i);
			}
			if($c3 !== 104) {
				throw new HException("Invalid IntHash format");
			}
			return $h2;
		}break;
		case 118:{
			$d = Date::fromString(_hx_substr($this->buf, $this->pos, 19));
			$this->cache->push($d);
			$this->pos += 19;
			return $d;
		}break;
		case 115:{
			$len2 = $this->readDigits();
			$buf5 = $this->buf;
			if(substr($buf5, $this->pos++, 1) != ":" || $this->length - $this->pos < $len2) {
				throw new HException("Invalid bytes length");
			}
			$codes = haxe_Unserializer::$CODES;
			if($codes === null) {
				$codes = haxe_Unserializer::initCodes();
				haxe_Unserializer::$CODES = $codes;
			}
			$i2 = $this->pos;
			$rest = $len2 & 3;
			$size = ($len2 >> 2) * 3 + ((($rest >= 2) ? $rest - 1 : 0));
			$max = $i2 + ($len2 - $rest);
			$bytes = haxe_io_Bytes::alloc($size);
			$bpos = 0;
			while($i2 < $max) {
				$c1 = $codes[ord($buf5{$i2++})];
				$c22 = $codes[ord($buf5{$i2++})];
				$bytes->b[$bpos++] = chr(($c1 << 2) | ($c22 >> 4));
				$c32 = $codes[ord($buf5{$i2++})];
				$bytes->b[$bpos++] = chr(($c22 << 4) | ($c32 >> 2));
				$c4 = $codes[ord($buf5{$i2++})];
				$bytes->b[$bpos++] = chr(($c32 << 6) | $c4);
				unset($c4,$c32,$c22,$c1);
			}
			if($rest >= 2) {
				$c12 = $codes[ord($buf5{$i2++})];
				$c23 = $codes[ord($buf5{$i2++})];
				$bytes->b[$bpos++] = chr(($c12 << 2) | ($c23 >> 4));
				if($rest === 3) {
					$c33 = $codes[ord($buf5{$i2++})];
					$bytes->b[$bpos++] = chr(($c23 << 4) | ($c33 >> 2));
				}
			}
			$this->pos += $len2;
			$this->cache->push($bytes);
			return $bytes;
		}break;
		default:{
			;
		}break;
		}
		$this->pos--;
		throw new HException(("Invalid char " . substr($this->buf, $this->pos, 1) . " at position " . $this->pos));
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static $DEFAULT_RESOLVER;
	static $BASE64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789%:";
	static $CODES = null;
	static function initCodes() {
		$codes = new _hx_array(array());
		{
			$_g1 = 0; $_g = strlen(haxe_Unserializer::$BASE64);
			while($_g1 < $_g) {
				$i = $_g1++;
				$codes[ord(haxe_Unserializer::$BASE64{$i})] = $i;
				unset($i);
			}
		}
		return $codes;
	}
	static function run($v) {
		return _hx_deref(new haxe_Unserializer($v))->unserialize();
	}
	function __toString() { return 'haxe.Unserializer'; }
}
haxe_Unserializer::$DEFAULT_RESOLVER = _hx_qtype("Type");
