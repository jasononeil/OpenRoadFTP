<?php

class php_FileKind extends Enum {
		public static $kdir;
		public static $kfile;
		public static function kother($k) { return new php_FileKind("kother", 2, array($k)); }
	}
	php_FileKind::$kdir = new php_FileKind("kdir", 0);
	php_FileKind::$kfile = new php_FileKind("kfile", 1);
