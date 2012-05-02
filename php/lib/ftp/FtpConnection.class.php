<?php

class ftp_FtpConnection {
	public function __construct($server_in, $user_in, $pass_in, $tmpDir_in, $fakeRoot_in, $port_in, $timeout_in) {
		if(!php_Boot::$skip_constructor) {
		if($timeout_in === null) {
			$timeout_in = 90;
		}
		if($port_in === null) {
			$port_in = 21;
		}
		if($fakeRoot_in === null) {
			$fakeRoot_in = "/";
		}
		$this->isReady = false;
		$this->server = $server_in;
		$this->username = $user_in;
		$this->password = $pass_in;
		$this->fakeRoot = $fakeRoot_in;
		$this->port = $port_in;
		$this->timeout = $timeout_in;
		$this->tmpDir = $tmpDir_in;
		$this->registerErrorTypes();
		$this->conn = ftp_connect($this->server, $this->port, $this->timeout);
		if(($this->conn === false)) {
			throw new HException(new hxbase_util_Error("FTP.SERVER_NOT_FOUND", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 39, "className" => "ftp.FtpConnection", "methodName" => "new"))));
		} else {
			$loginOkay = false;
			try {
				$loginOkay = ftp_login($this->conn, $this->username, $this->password);
			}catch(Exception $»e) {
				$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
				$e = $_ex_;
				{
					$loginOkay = false;
				}
			}
			if($loginOkay === false) {
				throw new HException(new hxbase_util_Error("FTP.BAD_LOGIN", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 55, "className" => "ftp.FtpConnection", "methodName" => "new"))));
			}
		}
		return true;
	}}
	public $server;
	public $username;
	public $password;
	public $port;
	public $timeout;
	public $fakeRoot;
	public $tmpDir;
	public $conn;
	public $isReady;
	public function registerErrorTypes() {
		hxbase_util_Error::registerErrorType("FTP.SERVER_NOT_FOUND", "The FTP server seems to be down.", null, null);
		hxbase_util_Error::registerErrorType("FTP.BAD_LOGIN", "The FTP server rejected your login.", null, null);
		hxbase_util_Error::registerErrorType("FTP.MOVE_FAILED", "We were unable to move (or rename) the file.  It's probably read only.", null, null);
		hxbase_util_Error::registerErrorType("FTP.COPY_READ_FAILED", "We were unable to copy the file.  The file you're coping couldn't be read, do you have permissions?", null, null);
		hxbase_util_Error::registerErrorType("FTP.COPY_WRITE_FAILED", "We were unable to copy the file.  The place you're coping to might be read only.", null, null);
		hxbase_util_Error::registerErrorType("FTP.DELETE_FILE_FAILED", "We were unable to delete the file.  It's probably read only.", null, null);
		hxbase_util_Error::registerErrorType("FTP.DELETE_DIR_FAILED", "We were unable to delete the folder.  There might be a file inside which just won't delete.", null, null);
		hxbase_util_Error::registerErrorType("FTP.MAKE_DIR_FAILED", "We were unable to create a new folder.  The folder you're in might be read only.", null, null);
	}
	public function isDir($path_in) {
		$result = false;
		$path = $this->sanitizePath($path_in);
		try {
			$result = ftp_chdir($this->conn, $path);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(is_string($e = $_ex_)){
				$result = false;
			} else throw $»e;;
		}
		return $result;
	}
	public function isFile($path_in) {
		$path = $this->sanitizePath($path_in);
		$filesize = null;
		$filesize = ftp_size($this->conn, $path);
		return (($filesize === -1) ? false : true);
	}
	public function exists($path_in) {
		$path = $this->sanitizePath($path_in);
		return $this->isFile($path) || $this->isDir($path);
	}
	public function getFileAt($path) {
		return new ftp_FtpItem($this, $path, null);
	}
	public function getDirAt($path) {
		return new ftp_FtpDir($this, $path);
	}
	public function ls($path_in, $recursive) {
		if($recursive === null) {
			$recursive = false;
		}
		if($path_in === null) {
			$path_in = "/";
		}
		$a = null;
		$s = null;
		$na = null;
		$path = $this->sanitizePath($path_in);
		$finalChar = _hx_char_at($path, strlen($path) - 1);
		$weAreListingChildren = (($finalChar === "/") ? true : false);
		$weAreGettingDirInfo = false;
		if($weAreListingChildren === false) {
			$weAreGettingDirInfo = $this->isDir($path);
		}
		if($weAreListingChildren || $weAreGettingDirInfo === false) {
			$na = ftp_rawlist($this->conn, $path, $recursive);
			$a = new _hx_array($na);
		} else {
			$onlyLastName = new EReg("([^/]+)\\\$", "");
			$name = (($onlyLastName->match($path)) ? $onlyLastName->matched(0) : null);
			$parentPath = $onlyLastName->replace($path, "");
			$na = ftp_rawlist($this->conn, $parentPath, $recursive);
			$a = new _hx_array($na);
			$filter = array(new _hx_lambda(array(&$a, &$finalChar, &$na, &$name, &$onlyLastName, &$parentPath, &$path, &$path_in, &$recursive, &$s, &$weAreGettingDirInfo, &$weAreListingChildren), "ftp_FtpConnection_0"), 'execute');
			$list = Lambda::filter($a, $filter);
			$a = Lambda::harray($list);
		}
		if($a === null) {
			$a = new _hx_array(array());
			$a->push("File not found.");
		}
		return $a;
	}
	public function move($oldPath_in, $newPath_in) {
		$didRenameWork = null;
		$oldPath = $this->sanitizePath($oldPath_in);
		$newPath = $this->sanitizePath($newPath_in);
		$didRenameWork = ftp_rename($this->conn, $oldPath, $newPath);
		if(!$didRenameWork) {
			throw new HException(new hxbase_util_Error("FTP.RENAME_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 163, "className" => "ftp.FtpConnection", "methodName" => "move"))));
		}
	}
	public function copy($path_in, $newPath_in) {
		$newPathOnFtpServer = null;
		$tmpPathOnWebServer = null;
		try {
			$tmpPathOnWebServer = $this->downloadFile($path_in);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				throw new HException(new hxbase_util_Error("FTP.COPY_READ_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 176, "className" => "ftp.FtpConnection", "methodName" => "copy"))));
			}
		}
		try {
			$newPathOnFtpServer = $newPath_in;
			$folder = null;
			$name = null;
			$extension = null;
			$number = 1;
			while($this->exists($newPathOnFtpServer)) {
				if($folder === null || $name === null || $extension === null) {
					$onlyLastName = new EReg("([^/]+)\$", "");
					$onlyLastName->match($newPathOnFtpServer);
					$folder = $onlyLastName->replace($newPathOnFtpServer, "");
					$nameAndExtension = $onlyLastName->matched(0);
					if(_hx_index_of($nameAndExtension, ".", null) > -1) {
						$anythingBeforeADot = new EReg("^([^.]*)", "");
						$anythingBeforeADot->match($nameAndExtension);
						$name = $anythingBeforeADot->matched(1);
						$extension = $anythingBeforeADot->replace($nameAndExtension, "");
						unset($anythingBeforeADot);
					} else {
						$name = $nameAndExtension;
						$extension = "";
					}
					unset($onlyLastName,$nameAndExtension);
				}
				$number++;
				$newPathOnFtpServer = $folder . $name . " (" . $number . ")" . $extension;
			}
			$this->uploadFile($tmpPathOnWebServer, $newPathOnFtpServer);
			@unlink($tmpPathOnWebServer);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e2 = $_ex_;
			{
				throw new HException(new hxbase_util_Error("FTP.COPY_WRITE_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 213, "className" => "ftp.FtpConnection", "methodName" => "copy"))));
			}
		}
	}
	public function deleteFile($path_in) {
		$didDeleteWork = null;
		$path = $this->sanitizePath($path_in);
		$didDeleteWork = ftp_delete($this->conn, $path);
		if(!$didDeleteWork) {
			throw new HException(new hxbase_util_Error("FTP.DELETE_FILE_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 222, "className" => "ftp.FtpConnection", "methodName" => "deleteFile"))));
		}
	}
	public function deleteDirectory($path_in) {
		$didDeleteWork = null;
		$path = $this->sanitizePath($path_in);
		$fileList = new ftp_FtpFileList($this, $path . "/");
		if(null == $fileList->dirs) throw new HException('null iterable');
		$»it = $fileList->dirs->iterator();
		while($»it->hasNext()) {
			$ftpDir = $»it->next();
			$this->deleteDirectory($ftpDir->path);
		}
		if(null == $fileList->files) throw new HException('null iterable');
		$»it = $fileList->files->iterator();
		while($»it->hasNext()) {
			$ftpItem = $»it->next();
			$this->deleteFile($ftpItem->path);
		}
		$didDeleteWork = ftp_rmdir($this->conn, $path);
		if(!$didDeleteWork) {
			throw new HException(new hxbase_util_Error("FTP.DELETE_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 239, "className" => "ftp.FtpConnection", "methodName" => "deleteDirectory"))));
		}
	}
	public function mkdir($path_in) {
		$didMakeDirWork = null;
		$path = $this->sanitizePath($path_in);
		$didMakeDirWork = ftp_mkdir($this->conn, $path);
		if(!$didMakeDirWork) {
			throw new HException(new hxbase_util_Error("FTP.MAKE_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 247, "className" => "ftp.FtpConnection", "methodName" => "mkdir"))));
		}
	}
	public function createWebServerDir($webServerDir) {
		$dirParts = _hx_explode("/", $webServerDir);
		$fullPath = "";
		if(!file_exists($webServerDir)) {
			while($dirParts->length > 0) {
				$dirName = $dirParts->shift();
				if($dirName !== "") {
					$fullPath = $fullPath . "/" . $dirName;
					$webServerDirExists = file_exists($fullPath);
					if($webServerDirExists === false) {
						try {
							@mkdir($fullPath, 493);
						}catch(Exception $»e) {
							$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
							$e = $_ex_;
							{
								throw new HException(new hxbase_util_Error("FTP.CREATE_TMP_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 269, "className" => "ftp.FtpConnection", "methodName" => "createWebServerDir"))));
							}
						}
						unset($e);
					}
					unset($webServerDirExists);
				}
				unset($dirName);
			}
		}
	}
	public function downloadFile($ftpPath_in) {
		$webServerPath = "";
		$didDownloadWork = null;
		$ftpPath = $this->sanitizePath($ftpPath_in);
		$cwd = php_Sys::getCwd();
		$webServerPath = $cwd . "/" . $this->tmpDir . $ftpPath;
		$onlyLastName = new EReg("([^/]+)\$", "");
		$webServerDir = $onlyLastName->replace($webServerPath, "");
		$this->createWebServerDir($webServerDir);
		$serverMode = FTP_BINARY;
		$didDownloadWork = ftp_get($this->conn, $webServerPath, $ftpPath, $serverMode);
		if(!$didDownloadWork) {
			throw new HException(new hxbase_util_Error("FTP.DOWNLOAD_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 289, "className" => "ftp.FtpConnection", "methodName" => "downloadFile"))));
		}
		return $webServerPath;
	}
	public function uploadFile($tmpPathOnWebServer, $newPathOnFtpServer_in) {
		$newPathOnFtpServer = $this->sanitizePath($newPathOnFtpServer_in);
		$folderToPasteIn = "";
		$fileToPasteAs = "";
		$serverMode = FTP_BINARY;
		$didUploadWork = false;
		$didChdirWork = false;
		$onlyLastName = new EReg("([^/]+)\$", "");
		$onlyLastName->match($newPathOnFtpServer);
		$folderToPasteIn = $onlyLastName->replace($newPathOnFtpServer, "");
		$fileToPasteAs = $onlyLastName->matched(0);
		$didChdirWork = ftp_chdir($this->conn, $folderToPasteIn);
		if($didChdirWork) {
			$didUploadWork = ftp_put($this->conn, $fileToPasteAs, $tmpPathOnWebServer, $serverMode);
		} else {
			throw new HException("HERE!");
		}
		if(!$didUploadWork) {
			throw new HException(new hxbase_util_Error("FTP.UPLOAD_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 317, "className" => "ftp.FtpConnection", "methodName" => "uploadFile"))));
		}
	}
	public function sanitizePath($path_in) {
		$path = ftp_FtpConnection_1($this, $path_in);
		$slashMultipleDotsSlash = new EReg("[/\\\\]\\.{2,}[/\\\\]", "");
		$path = $slashMultipleDotsSlash->replace($path, "/");
		return $path;
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
	function __toString() { return 'ftp.FtpConnection'; }
}
function ftp_FtpConnection_0(&$a, &$finalChar, &$na, &$name, &$onlyLastName, &$parentPath, &$path, &$path_in, &$recursive, &$s, &$weAreGettingDirInfo, &$weAreListingChildren, $line_in) {
	{
		$first8blocks = new EReg("(\\S+\\s+){8}", "");
		$leftOverName = $first8blocks->replace($line_in, "");
		if(StringTools::startsWith($line_in, "l")) {
			$leftOverName = _hx_array_get(_hx_explode(" -> ", $leftOverName), 0);
		}
		return $name === $leftOverName;
	}
}
function ftp_FtpConnection_1(&$»this, &$path_in) {
	if(_hx_substr($path_in, 0, strlen($»this->fakeRoot)) === $»this->fakeRoot) {
		return $path_in;
	} else {
		return $»this->fakeRoot . $path_in;
	}
}
