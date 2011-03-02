<?php

class StringTools {
	public function __construct(){}
	static function urlEncode($s) {
		return rawurlencode($s);
	}
	static function urlDecode($s) {
		return urldecode($s);
	}
	static function htmlEscape($s) {
		return _hx_explode(">", _hx_explode("<", _hx_explode("&", $s)->join("&amp;"))->join("&lt;"))->join("&gt;");
	}
	static function htmlUnescape($s) {
		return htmlspecialchars_decode($s);
	}
	static function startsWith($s, $start) {
		return (strlen($s) >= strlen($start) && _hx_substr($s, 0, strlen($start)) == $start);
	}
	static function endsWith($s, $end) {
		$elen = strlen($end);
		$slen = strlen($s);
		return ($slen >= $elen && _hx_substr($s, $slen - $elen, $elen) == $end);
	}
	static function isSpace($s, $pos) {
		$c = _hx_char_code_at($s, $pos);
		return ($c >= 9 && $c <= 13) || $c === 32;
	}
	static function ltrim($s) {
		return ltrim($s);
	}
	static function rtrim($s) {
		return rtrim($s);
	}
	static function trim($s) {
		return trim($s);
	}
	static function rpad($s, $c, $l) {
		return str_pad($s, $l, $c, STR_PAD_RIGHT);
	}
	static function lpad($s, $c, $l) {
		return str_pad($s, $l, $c, STR_PAD_LEFT);
	}
	static function replace($s, $sub, $by) {
		return str_replace($sub, $by, $s);
	}
	static function hex($n, $digits) {
		$neg = false;
		if($n < 0) {
			$neg = true;
			$n = -$n;
		}
		$s = "";
		$hexChars = "0123456789ABCDEF";
		do {
			$s = substr($hexChars, $n % 16, 1) . $s;
			$n = intval($n / 16);
			;
		} while($n > 0);
		if($digits !== null) {
			while(strlen($s) < $digits) $s = "0" . $s;
		}
		if($neg) {
			$s = "-" . $s;
		}
		return $s;
	}
	function __toString() { return 'StringTools'; }
}
