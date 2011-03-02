<?php

class jasononeil_ftp_FtpConnection {
	public function __construct($server_in, $user_in, $pass_in, $tmpDir_in, $fakeRoot_in, $port_in, $timeout_in) { if( !php_Boot::$skip_constructor ) {
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
		if(!_hx_equal($this->conn, false)) {
			$loginOkay = null;
			try {
				$loginOkay = ftp_login($this->conn, $this->username, $this->password);
			}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			;
			if(is_string($e = $_ex_)){
				$loginOkay = false;
			} else throw $»e; }
			if($loginOkay) {
				$this->isReady = true;
			}
			else {
				throw new HException(new jasononeil_util_Error("FTP.BAD_LOGIN", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 170, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "new"))));
			}
		}
		else {
			throw new HException(new jasononeil_util_Error("FTP.SERVER_NOT_FOUND", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 176, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "new"))));
		}
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
		jasononeil_util_Error::registerErrorType("FTP.SERVER_NOT_FOUND", "The FTP server seems to be down.", null, null);
		jasononeil_util_Error::registerErrorType("FTP.BAD_LOGIN", "The FTP server rejected your login.  Probably incorrect details.", null, null);
		jasononeil_util_Error::registerErrorType("FTP.MOVE_FAILED", "We were unable to move (or rename) the file.  It's probably read only", null, null);
		jasononeil_util_Error::registerErrorType("FTP.COPY_READ_FAILED", "We were unable to copy the file.  The file you're coping couldn't be read, do you have permissions?", null, null);
		jasononeil_util_Error::registerErrorType("FTP.COPY_WRITE_FAILED", "We were unable to copy the file.  The place you're coping to might be read only.", null, null);
		jasononeil_util_Error::registerErrorType("DELETE_FILE_FAILED", "We were unable to delete the file.  It's probably read only", null, null);
		jasononeil_util_Error::registerErrorType("DELETE_DIR_FAILED", "We were unable to delete the folder.  There might be a file inside which just won't delete.", null, null);
		jasononeil_util_Error::registerErrorType("MAKE_DIR_FAILED", "We were unable to create a new folder.  The folder you're in might be read only.", null, null);
	}
	public function isDir($path_in) {
		$result = false;
		$path = $this->sanitizePath($path_in);
		try {
			$result = ftp_chdir($this->conn, $path);
		}catch(Exception $»e) {
		$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
		;
		if(is_string($e = $_ex_)){
			$result = false;
		} else throw $»e; }
		return $result;
	}
	public function isFile($path_in) {
		$path = $this->sanitizePath($path_in);
		$fileSize = null;
		$fileSize = ftp_size($this->conn, $path);
		return (($fileSize === -1) ? false : true);
	}
	public function exists($path_in) {
		$path = $this->sanitizePath($path_in);
		return ($this->isFile($path) || $this->isDir($path));
	}
	public function getFileAt($path) {
		return new jasononeil_ftp_FtpItem($this, $path, null);
	}
	public function getDirAt($path) {
		return new jasononeil_ftp_FtpDir($this, $path);
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
		$finalChar = substr($path, strlen($path) - 1, 1);
		$weAreListingChildren = (($finalChar == "/") ? true : false);
		$weAreGettingDirInfo = false;
		if($weAreListingChildren === false) {
			try {
				$weAreGettingDirInfo = ftp_chdir($this->conn, $path);
			}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			;
			{ $e = $_ex_;
			{
				$weAreGettingDirInfo = false;
			}}}
		}
		if($weAreListingChildren || $weAreGettingDirInfo === false) {
			$na = ftp_rawlist($this->conn, $path, $recursive);
			$a = new _hx_array($na);
		}
		else {
			$onlyLastName = new EReg("([^/]+)\$", "");
			$name = (($onlyLastName->match($path)) ? $onlyLastName->matched(0) : null);
			$parentPath = $onlyLastName->replace($path, "");
			$na = ftp_rawlist($this->conn, $parentPath, $recursive);
			$a = new _hx_array($na);
			$filter = array(new _hx_lambda(array("_ex_" => &$_ex_, "a" => &$a, "e" => &$e, "filter" => &$filter, "finalChar" => &$finalChar, "na" => &$na, "name" => &$name, "onlyLastName" => &$onlyLastName, "parentPath" => &$parentPath, "path" => &$path, "path_in" => &$path_in, "recursive" => &$recursive, "s" => &$s, "weAreGettingDirInfo" => &$weAreGettingDirInfo, "weAreListingChildren" => &$weAreListingChildren, "»e" => &$»e), null, array('line_in'), "{
				\$first8blocks = new EReg(\"(\\\\S+\\\\s+){8}\", \"\");
				\$leftoverName = \$first8blocks->replace(\$line_in, \"\");
				if(StringTools::startsWith(\$line_in, \"l\")) {
					\$leftoverName = _hx_array_get(_hx_explode(\" -> \", \$leftoverName), 0);
				}
				return (\$name == \$leftoverName);
			}"), 'execute1');
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
			throw new HException(new jasononeil_util_Error("FTP.RENAME_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 330, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "move"))));
		}
	}
	public function copy($path_in, $newPath_in) {
		$newPathOnFtpServer = null;
		$tmpPathOnWebServer = null;
		try {
			$tmpPathOnWebServer = $this->downloadFile($path_in);
		}catch(Exception $»e) {
		$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
		;
		{ $e = $_ex_;
		{
			throw new HException(new jasononeil_util_Error("FTP.COPY_READ_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 347, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "copy"))));
		}}}
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
					$anythingBeforeADot = new EReg("^([^.]*)", "");
					$anythingBeforeADot->match($nameAndExtension);
					$name = $anythingBeforeADot->matched(0);
					$extension = $anythingBeforeADot->replace($nameAndExtension, "");
				}
				$number++;
				$newPathOnFtpServer = $folder . $name . " (" . $number . ")" . $extension;
				unset($onlyLastName,$nameAndExtension,$anythingBeforeADot);
			}
			$this->uploadFile($tmpPathOnWebServer, $newPathOnFtpServer);
			unlink($tmpPathOnWebServer);;
		}catch(Exception $»e2) {
		$_ex_2 = ($»e2 instanceof HException) ? $»e2->e : $»e2;
		;
		{ $e2 = $_ex_2;
		{
			throw new HException(new jasononeil_util_Error("FTP.COPY_WRITE_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 391, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "copy"))));
		}}}
	}
	public function deleteFile($path_in) {
		$didDeleteWork = null;
		$path = $this->sanitizePath($path_in);
		$didDeleteWork = ftp_delete($this->conn, $path);
		if(!$didDeleteWork) {
			throw new HException(new jasononeil_util_Error("FTP.DELETE_FILE_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 403, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "deleteFile"))));
		}
	}
	public function deleteDirectory($path_in) {
		$didDeleteWork = null;
		$path = $this->sanitizePath($path_in);
		$fileList = jasononeil_ftp_FtpFileList::getFileList($this, $path . "/");
		$»it = $fileList->dirs->iterator();
		while($»it->hasNext()) {
		$ftpItem = $»it->next();
		{
			$this->deleteDirectory($ftpItem->path);
			;
		}
		}
		$»it2 = $fileList->files->iterator();
		while($»it2->hasNext()) {
		$ftpItem2 = $»it2->next();
		{
			$this->deleteFile($ftpItem2->path);
			;
		}
		}
		$didDeleteWork = ftp_rmdir($this->conn, $path);
		if(!$didDeleteWork) {
			throw new HException(new jasononeil_util_Error("FTP.DELETE_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 424, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "deleteDirectory"))));
		}
	}
	public function mkdir($path_in) {
		$didMakeDirWork = null;
		$path = $this->sanitizePath($path_in);
		$didMakeDirWork = ftp_mkdir($this->conn, $path);
		if(!$didMakeDirWork) {
			throw new HException(new jasononeil_util_Error("FTP.MAKE_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 439, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "mkdir"))));
		}
	}
	public function createWebServerDir($webServerDir) {
		$dirParts = _hx_explode("/", $webServerDir);
		$fullPath = "";
		{
			$_g = 0;
			while($_g < $dirParts->length) {
				$dirName = $dirParts[$_g];
				++$_g;
				if($dirName != "") {
					$fullPath = $fullPath . "/" . $dirName;
					$webServerDirExists = is_dir($fullPath);
					if($webServerDirExists === false) {
						$didMakeTmpDirWork = null;
						try {
							$didMakeTmpDirWork = call_user_func_array($__php__, array("mkdir('" . $fullPath . "', 0777, true);"));
						}catch(Exception $»e) {
						$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
						;
						{ $e = $_ex_;
						{
							$didMakeTmpDirWork = false;
						}}}
						if(!$didMakeTmpDirWork) {
							;
						}
					}
				}
				unset($»e,$webServerDirExists,$e,$dirName,$didMakeTmpDirWork,$_ex_);
			}
		}
	}
	public function downloadFile($ftpPath_in) {
		$webServerPath = "";
		$didDownloadWork = null;
		$ftpPath = $this->sanitizePath($ftpPath_in);
		$cwd = getcwd();
		$webServerPath = $cwd . "/" . $this->tmpDir . $ftpPath_in;
		$onlyLastName = new EReg("([^/]+)\$", "");
		$webServerDir = $onlyLastName->replace($webServerPath, "");
		$webServerDirExists = is_dir($webServerDir);
		if($webServerDirExists === false) {
			try {
				mkdir($webServerDir, 511, true);
			}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			;
			{ $e = $_ex_;
			{
				throw new HException(new jasononeil_util_Error("FTP.CREATE_TMP_DIR_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 505, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "downloadFile"))));
			}}}
		}
		$serverMode = FTP_BINARY;
		$didDownloadWork = ftp_get($this->conn, $webServerPath, $ftpPath, $serverMode);
		if(!$didDownloadWork) {
			throw new HException(new jasononeil_util_Error("FTP.DOWNLOAD_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 516, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "downloadFile"))));
		}
		return $webServerPath;
	}
	public function uploadFile($tmpPathOnWebServer, $newPathOnFtpServer_in) {
		$newPathOnFtpServer = $this->sanitizePath($newPathOnFtpServer_in);
		$folderToPasteIn = null;
		$fileToPasteAs = null;
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
		}
		else {
			throw new HException("HERE!");
		}
		if(!$didUploadWork) {
			throw new HException(new jasononeil_util_Error("FTP.UPLOAD_FAILED", _hx_anonymous(array("fileName" => "FtpConnection.hx", "lineNumber" => 555, "className" => "jasononeil.ftp.FtpConnection", "methodName" => "uploadFile"))));
		}
	}
	public function sanitizePath($path_in) {
		$path = (((_hx_substr($path_in, 0, strlen($this->fakeRoot)) == $this->fakeRoot)) ? $path_in : $this->fakeRoot . $path_in);
		$slashMultipleDotsSlash = new EReg("[/\\\\]\\.{2,}[/\\\\]", "");
		$path = $slashMultipleDotsSlash->replace($path, "/");
		return $path;
	}
	public function toString() {
		$str = null;
		if($this->isReady) {
			$str = "FTP: Connected to " . $this->server . ":" . $this->port . " as " . $this->username;
		}
		else {
			$str = "FTP Connection failed.";
		}
		return $str;
	}
	function __toString() { return $this->toString(); }
}
