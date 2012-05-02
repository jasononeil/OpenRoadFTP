<?php

class ftp_FtpFileList {
	public function __construct($cnx, $path) {
		if(!php_Boot::$skip_constructor) {
		$this->dirs = new Hash();
		$this->files = new Hash();
		$this->links = new Hash();
		$lsResult = $cnx->ls($path, null);
		{
			$_g = 0;
			while($_g < $lsResult->length) {
				$line = $lsResult[$_g];
				++$_g;
				$file = ftp_FtpItem::newFromLsLine($cnx, $path, $line);
				if($file->type == ftp_FtpFileType::$link) {
					$this->links->set($file->path, $file);
				} else {
					if($file->type == ftp_FtpFileType::$dir) {
						$this->dirs->set($file->path, $file);
					} else {
						if($file->type == ftp_FtpFileType::$file) {
							$this->files->set($file->path, $file);
						}
					}
				}
				ftp_FtpFileList::$t++;
				unset($line,$file);
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
	public function countDirs() {
		return Lambda::count($this->dirs, null);
	}
	public function countFiles() {
		return Lambda::count($this->files, null);
	}
	public function countLinks() {
		return Lambda::count($this->links, null);
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
	static $t = 0;
	function __toString() { return 'ftp.FtpFileList'; }
}
