<?php

class FtpTest {
	public function __construct(){}
	static function main() {
		php_Lib::hprint("Hello! <b>Testing FTP Stuff</b>");
		$ftp = new jasononeil_ftp_FtpConnection("localhost", "jason", "1Cor13", "tmp/", null, null, null);
		FtpTest::pushResults("Ftp test page");
		FtpTest::pushResults($ftp);
		$f = $ftp->getFileAt("/Desktop/LivingFaiths.mp4");
		FtpTest::pushResults($f->lsResult);
		FtpTest::pushResults($f->name);
		FtpTest::pushResults($f->type);
		FtpTest::pushResults($f->permissions);
		FtpTest::pushResults($f->size);
		FtpTest::pushResults($f->owner);
		FtpTest::pushResults($f->group);
		$arr = $ftp->ls("/", true);
		FtpTest::pushResults($arr->join("\x0A"));
		php_Lib::hprint("<pre style=\"border: 1px solid black;\">" . StringTools::htmlEscape(FtpTest::$str) . "</pre>");
	}
	static $str = "";
	static function pushResults($in_str) {
		if($in_str === null) {
			$in_str = "";
		}
		FtpTest::$str .= "\x0A" . Std::string($in_str);
	}
	function __toString() { return 'FtpTest'; }
}
