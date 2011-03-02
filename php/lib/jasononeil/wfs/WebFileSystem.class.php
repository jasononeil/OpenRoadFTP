<?php

class jasononeil_wfs_WebFileSystem {
	public function __construct() { if( !php_Boot::$skip_constructor ) {
		$this->setID(16);
	}}
	public function setLocalRoot() {
		;
	}
	public function setFTPServer() {
		;
	}
	public function setID($in_id) {
		if($this->id > 0) {
			haxe_Log::trace("PISS OFF!<br />", _hx_anonymous(array("fileName" => "WebFileSystem.hx", "lineNumber" => 89, "className" => "jasononeil.wfs.WebFileSystem", "methodName" => "setID")));
		}
		else {
			$this->id = $in_id;
		}
		return $in_id;
	}
	public $id;
	function __toString() { return 'jasononeil.wfs.WebFileSystem'; }
}
