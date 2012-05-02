<?php

class Api {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->session = new hxbase_session_SessionHandler(AppConfig::$sessionID, AppConfig::$sessionTimeOut);
	}}
	public $loginOkay;
	public $error;
	public $ftp;
	public $session;
	public $tpl;
	public function initiateFTP($username, $password, $id) {
		$server = AppConfig::$ftpServer;
		$tmpFolder = "tmp/" . $id . "-" . $username;
		$home = null;
		$home = AppConfig::getHomeDir($username);
		try {
			$this->ftp = new ftp_FtpConnection($server, $username, $password, $tmpFolder, $home, null, null);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(($e = $_ex_) instanceof hxbase_util_Error){
				switch($e->code) {
				case "FTP.SERVER_NOT_FOUND":{
					throw new HException(new hxbase_util_Error("FTP.SERVER_DOWN", _hx_anonymous(array("fileName" => "Api.hx", "lineNumber" => 111, "className" => "Api", "methodName" => "initiateFTP"))));
				}break;
				case "FTP.BAD_LOGIN":{
					throw new HException(new hxbase_util_Error("SESSION.INCORRECT_LOGIN", _hx_anonymous(array("fileName" => "Api.hx", "lineNumber" => 113, "className" => "Api", "methodName" => "initiateFTP"))));
				}break;
				}
			} else throw $»e;;
		}
	}
	public function checkLoggedIn() {
		$this->session_keepalive();
		$this->initiateFTP($this->session->get("username"), $this->session->get("password"), $this->session->get("SESSION.firstID"));
	}
	public function session_getLoginForm($err) {
		$str = null;
		$this->tpl = new hxbase_tpl_HxTpl();
		$this->tpl->loadTemplateFromFile("./tpl/login.hxtpl");
		$this->tpl->assign("message", $err->error, null);
		if(_hx_has_field($err, "explanation")) {
			$this->tpl->setSwitch("explanation", true)->assign("explanation", $err->explanation, null);
		}
		if(_hx_has_field($err, "suggestion")) {
			$this->tpl->setSwitch("suggestion", true)->assign("suggestion", $err->suggestion, null);
		}
		$str = $this->tpl->getOutput();
		return $str;
	}
	public function session_login($username, $password) {
		$id = $this->session->start()->get("SESSION.firstID");
		$this->initiateFTP($username, $password, $id);
		$this->session->set("username", $username)->set("password", $password);
		return true;
	}
	public function session_keepalive() {
		try {
			$this->session->check();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(($err = $_ex_) instanceof hxbase_util_Error){
				switch($err->code) {
				case "SESSION.NO_SESSION":{
					throw new HException(new hxbase_util_Error("SESSION.NOT_LOGGED_IN", _hx_anonymous(array("fileName" => "Api.hx", "lineNumber" => 178, "className" => "Api", "methodName" => "session_keepalive"))));
				}break;
				case "SESSION.TIMEOUT":{
					throw new HException(new hxbase_util_Error("SESSION.TIMED_OUT", _hx_anonymous(array("fileName" => "Api.hx", "lineNumber" => 180, "className" => "Api", "methodName" => "session_keepalive"))));
				}break;
				}
			} else throw $»e;;
		}
	}
	public function session_logoff() {
		$this->session->end();
		include("scripts/clearTempFiles.php");;
		throw new HException(new hxbase_util_Error("SESSION.LOGGED_OUT", _hx_anonymous(array("fileName" => "Api.hx", "lineNumber" => 201, "className" => "Api", "methodName" => "session_logoff"))));
	}
	public function getBrowserMask() {
		$str = null;
		$this->checkLoggedIn();
		$this->tpl = new hxbase_tpl_HxTpl();
		$this->tpl->loadTemplateFromFile("./tpl/browserMask.hxtpl");
		$str = $this->tpl->getOutput();
		return $str;
	}
	public function getDirListing($path) {
		$str = null;
		$this->checkLoggedIn();
		$tpl = new hxbase_tpl_HxTpl();
		$ftpFileList = new ftp_FtpFileList($this->ftp, $path);
		$tpl->loadTemplateFromFile("./tpl/dirList.hxtpl");
		$onlyLastName = new EReg("([^/]+)/\$", "");
		$name = (($onlyLastName->match($path)) ? $onlyLastName->matched(1) : "Home");
		$tpl->assign("dir.name", $name, null);
		$tpl->assign("dir.path", $path, null);
		if(null == $ftpFileList->links) throw new HException('null iterable');
		$»it = $ftpFileList->links->iterator();
		while($»it->hasNext()) {
			$link = $»it->next();
			if(!AppConfig::$limitSymlinks || Lambda::has(AppConfig::$allowedSymlinks, $link->name, null)) {
				$ftpItem = $tpl->newLoop("ftpItem");
				$ftpItem->assignObject("file", $link, null);
				$ftpItem->assign("file.type", "dir", null);
				$ftpItem->assign("file.path", $link->path . "/", null);
				unset($ftpItem);
			}
		}
		if(null == $ftpFileList->dirs) throw new HException('null iterable');
		$»it = $ftpFileList->dirs->iterator();
		while($»it->hasNext()) {
			$dir = $»it->next();
			$ftpItem = $tpl->newLoop("ftpItem");
			$ftpItem->assignObject("file", $dir, null);
			unset($ftpItem);
		}
		if(null == $ftpFileList->files) throw new HException('null iterable');
		$»it = $ftpFileList->files->iterator();
		while($»it->hasNext()) {
			$file = $»it->next();
			$ftpItem = $tpl->newLoop("ftpItem");
			$ftpItem->assignObject("file", $file, null);
			unset($ftpItem);
		}
		$str = $tpl->getOutput();
		return $str;
	}
	public function moveFile($oldPath, $newPath) {
		$this->checkLoggedIn();
		$file = null;
		$file = $this->ftp->getFileAt($oldPath);
		if($file->type != ftp_FtpFileType::$link) {
			$file->move($newPath);
		}
		return true;
	}
	public function deleteFile($path) {
		$this->checkLoggedIn();
		$file = null;
		$file = $this->ftp->getFileAt($path);
		if($file->type != ftp_FtpFileType::$link) {
			$file->delete();
		}
		return true;
	}
	public function deleteFiles($pathsToDelete) {
		$this->checkLoggedIn();
		$file = null;
		{
			$_g = 0;
			while($_g < $pathsToDelete->length) {
				$path = $pathsToDelete[$_g];
				++$_g;
				$file = $this->ftp->getFileAt($path);
				if($file->type != ftp_FtpFileType::$link) {
					$file->delete();
				}
				unset($path);
			}
		}
		return true;
	}
	public function pasteFromCut($filesToMove, $newDir) {
		$this->checkLoggedIn();
		$file = null;
		{
			$_g = 0;
			while($_g < $filesToMove->length) {
				$fileData = $filesToMove[$_g];
				++$_g;
				$newPath = $newDir . $fileData->name;
				$file = $this->ftp->getFileAt($fileData->oldPath);
				if($file->type != ftp_FtpFileType::$link) {
					$file->move($newPath);
				}
				unset($newPath,$fileData);
			}
		}
		return true;
	}
	public function pasteFromCopy($filesToMove, $newDir) {
		$this->checkLoggedIn();
		$file = null;
		{
			$_g = 0;
			while($_g < $filesToMove->length) {
				$fileData = $filesToMove[$_g];
				++$_g;
				$newPath = $newDir . $fileData->name;
				$file = $this->ftp->getFileAt($fileData->oldPath);
				$file->copy($newPath);
				unset($newPath,$fileData);
			}
		}
		return true;
	}
	public function upload($localFilePath, $ftpPath) {
		$this->checkLoggedIn();
		$this->ftp->uploadFile($localFilePath, $ftpPath);
	}
	public function mkdir($path) {
		$this->checkLoggedIn();
		$this->ftp->mkdir($path);
		return true;
	}
	public function download($path) {
		$downloadKey = null;
		$this->checkLoggedIn();
		$file = $this->ftp->getFileAt($path);
		$url = $file->download();
		$downloadKey = Std::string(Math::random());
		$this->session->set("DOWNLOAD.url", $url)->set("DOWNLOAD.key", $downloadKey);
		return $downloadKey;
	}
	public function test($x, $y) {
		return $x + $y;
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
	static $inst;
	static function main() {
		Api::$inst = new Api();
		Api::registerErrorMessages();
		$ctx = new haxe_remoting_Context();
		$ctx->addObject("api", Api::$inst, null);
		haxe_remoting_HttpConnection::handleRequest($ctx);
	}
	static function registerErrorMessages() {
		hxbase_util_Error::registerErrorType("SESSION.NOT_LOGGED_IN", "Please sign in", "We won't be able to get started until you've signed in.", "Please sign in with your S drive username and password.");
		hxbase_util_Error::registerErrorType("SESSION.LOGGED_OUT", "See you next time!", "You've signed out successfully.", "Don't worry, your files are safe with us.");
		hxbase_util_Error::registerErrorType("SESSION.TIMED_OUT", "I thought you were gone!", "Sorry, after 5 minutes we sign you out automatically, to keep your files safe in case you're gone.", "You'll have to sign in again.  Make sure you're quick!");
		hxbase_util_Error::registerErrorType("SESSION.INCORRECT_LOGIN", "Try again...", "Your username or password seems to be incorrect.", null);
		hxbase_util_Error::registerErrorType("FTP.SERVER_DOWN", "The student server seems to be down.", "We're really sorry but it looks like the student server is down at the moment.", "You might want to try again a bit later.  If there's a teacher nearby, perhaps let them know so the IT guys can get onto it.");
		hxbase_util_Error::registerErrorType("FTP.OPERATION_FAILED", "Sorry, that didn't work.", "Whatever it was you were trying to do just failed, and we're not entirely sure why.", "Check the file or folder is not read only, try again later or ask for help.");
	}
	function __toString() { return 'Api'; }
}
