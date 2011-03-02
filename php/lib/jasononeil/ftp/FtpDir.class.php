<?php

class jasononeil_ftp_FtpDir {
	public function __construct($ftp_in, $path_in) { if( !php_Boot::$skip_constructor ) {
		if($ftp_in->isReady) {
			$this->path = str_replace("\\", "/", $path_in);
			$this->ftp = $ftp_in;
			$r = new EReg("/.+/", "");
			if($r->match($this->path)) {
				haxe_Log::trace("FTW!  Check if it exists and populate fields", _hx_anonymous(array("fileName" => "FtpDir.hx", "lineNumber" => 124, "className" => "jasononeil.ftp.FtpDir", "methodName" => "new")));
			}
			else {
				throw new HException("Trying to create a new FtpDir object but the path given isn't valid.");
			}
		}
		else {
			throw new HException("Trying to create a new FtpDir object with an FtpConnection that's not ready.");
		}
	}}
	public $path;
	public $ftp;
	public $exists;
	public $name;
	public $owner;
	public $group;
	public $permissions;
	public function toString() {
		return $this->path;
	}
	public function getter_exists() {
		return $this->ftp->isDir($this->path);
	}
	public function getter_name() {
		return "";
	}
	public function getter_owner() {
		return "";
	}
	public function getter_group() {
		return "";
	}
	public function getter_permissions() {
		return "";
	}
	function __toString() { return $this->toString(); }
}
