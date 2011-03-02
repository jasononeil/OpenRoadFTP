<?php

class jasononeil_ftp_FtpFileList {
	public function __construct() {
		if( !php_Boot::$skip_constructor ) {
		$this->dirs = new Hash();
		$this->files = new Hash();
		$this->links = new Hash();
	}}
	public $dirs;
	public $files;
	public $links;
	public $numDirs;
	public $numFiles;
	public $numLinks;
	public function toString() {
		$dirCount = null; $fileCount = null;
		return "FtpFileList object with " . $this->countDirs() . " directories and " . $this->countFiles() . " files";
	}
	public function countDirs() {
		return Lambda::count($this->dirs);
	}
	public function countFiles() {
		return Lambda::count($this->files);
	}
	public function countLinks() {
		return Lambda::count($this->links);
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function getFileList($cnx, $path) {
		$ftpFileList = new jasononeil_ftp_FtpFileList();
		$lsResult = $cnx->ls($path, null);
		{
			$_g = 0;
			while($_g < $lsResult->length) {
				$line = $lsResult[$_g];
				++$_g;
				$file = jasononeil_ftp_FtpItem::newFromLsLine($cnx, $path, $line);
				if($file->type == jasononeil_ftp_FileType::$link) {
					$ftpFileList->links->set($file->path, $file);
				}
				else {
					if($file->type == jasononeil_ftp_FileType::$dir) {
						$ftpFileList->dirs->set($file->path, $file);
					}
					else {
						if($file->type == jasononeil_ftp_FileType::$file) {
							$ftpFileList->files->set($file->path, $file);
						}
					}
				}
				unset($line,$file);
			}
		}
		return $ftpFileList;
	}
	function __toString() { return $this->toString(); }
}
