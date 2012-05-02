<?php

class ftp_FtpDir {
	public function __construct($ftp_in, $path_in) {
		if(!php_Boot::$skip_constructor) {
		$this->path = str_replace("\\", "/", $path_in);
		$this->ftp = $ftp_in;
		if($this->ftp->isReady) {
			$r = new EReg("/.+/", "");
			if($r->match($this->path)) {
				haxe_Log::trace("FTW! Check if it exists and populate fields", _hx_anonymous(array("fileName" => "FtpDir.hx", "lineNumber" => 24, "className" => "ftp.FtpDir", "methodName" => "new")));
			} else {
				throw new HException("Trying to create new FtpDir object but the path given isn't valid.");
			}
		} else {
			throw new HException("Trying to create a new FtpDir object with an FtpConnection that's not ready");
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
	function __toString() { return $this->toString(); }
}
