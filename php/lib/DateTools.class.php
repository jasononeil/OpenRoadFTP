<?php

class DateTools {
	public function __construct(){}
	static function format($d, $f) {
		return strftime($f, $d->__t);
	}
	function __toString() { return 'DateTools'; }
}
