<?php

class haxe_io_Error extends Enum {
		public static $Blocked;
		public static function Custom($e) { return new haxe_io_Error("Custom", 3, array($e)); }
		public static $OutsideBounds;
		public static $Overflow;
	}
	haxe_io_Error::$Blocked = new haxe_io_Error("Blocked", 0);
	haxe_io_Error::$OutsideBounds = new haxe_io_Error("OutsideBounds", 2);
	haxe_io_Error::$Overflow = new haxe_io_Error("Overflow", 1);
