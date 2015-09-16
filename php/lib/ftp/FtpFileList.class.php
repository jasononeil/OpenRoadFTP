<?php

class ftp_FtpFileList {
	public function __construct($cnx, $path) {
		if(!php_Boot::$skip_constructor) {
		$this->dirs = new haxe_ds_StringMap();
		$this->files = new haxe_ds_StringMap();
		$this->links = new haxe_ds_StringMap();
		$lsResult = $cnx->ls($path, null);
		{
			$_g = 0;
			while($_g < $lsResult->length) {
				$line = $lsResult[$_g];
				++$_g;
				$file = ftp_FtpItem::newFromLsLine($cnx, $path, $line);
				if((is_object($_t = $file->type) && !($_t instanceof Enum) ? $_t === ftp_FtpFileType::$link : $_t == ftp_FtpFileType::$link)) {
					$this->links->set($file->path, $file);
				} else {
					if((is_object($_t2 = $file->type) && !($_t2 instanceof Enum) ? $_t2 === ftp_FtpFileType::$dir : $_t2 == ftp_FtpFileType::$dir)) {
						$this->dirs->set($file->path, $file);
					} else {
						if((is_object($_t3 = $file->type) && !($_t3 instanceof Enum) ? $_t3 === ftp_FtpFileType::$file : $_t3 == ftp_FtpFileType::$file)) {
							$this->files->set($file->path, $file);
						}
						unset($_t3);
					}
					unset($_t2);
				}
				ftp_FtpFileList::$t++;
				unset($line,$file,$_t);
			}
		}
	}}
	public $dirs;
	public $files;
	public $links;
	public $all;
	public $numDirs;
	public $numFiles;
	public $numLinks;
	public function get_numDirs() {
		return Lambda::count($this->dirs, null);
	}
	public function get_numFiles() {
		return Lambda::count($this->files, null);
	}
	public function get_numLinks() {
		return Lambda::count($this->links, null);
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static $t = 0;
	static $__properties__ = array("get_numLinks" => "get_numLinks","get_numFiles" => "get_numFiles","get_numDirs" => "get_numDirs");
	function __toString() { return 'ftp.FtpFileList'; }
}
