<?php

class ftp_FtpItem {
	public function __construct($ftpConn_in, $path_in, $lsResult_in) {
		if(!php_Boot::$skip_constructor) {
		hxbase_Log::assert($ftpConn_in !== null, "ftpConn_in must not be null", _hx_anonymous(array("fileName" => "FtpItem.hx", "lineNumber" => 24, "className" => "ftp.FtpItem", "methodName" => "new")));
		hxbase_Log::assert($path_in !== null, "path_in must not be null", _hx_anonymous(array("fileName" => "FtpItem.hx", "lineNumber" => 25, "className" => "ftp.FtpItem", "methodName" => "new")));
		$this->ftpConn = $ftpConn_in;
		$this->path = $path_in;
		$this->name = "File Not Found.";
		$this->type = ftp_FtpFileType::$unknown;
		$this->permissions = _hx_anonymous(array("ownerRead" => false, "ownerWrite" => false, "ownerExecute" => false, "groupRead" => false, "groupWrite" => false, "groupExecute" => false, "otherRead" => false, "otherWrite" => false, "otherExecute" => false));
		$this->size = 0;
		$this->owner = "";
		$this->group = "";
		$this->target = "";
		if($lsResult_in === null) {
			$arr = $this->ftpConn->ls($path_in, null);
			if($arr->length > 0) {
				$this->lsResult = $arr[0];
			} else {
				$this->lsResult = "File not found.";
			}
		} else {
			$this->lsResult = $lsResult_in;
		}
		if($this->lsResult !== "File not found.") {
			$this->processLsResult();
		}
	}}
	public $ftpConn;
	public $path;
	public $lsResult;
	public $name;
	public $type;
	public $permissions;
	public $size;
	public $owner;
	public $group;
	public $modified;
	public $modifiedStr;
	public $target;
	public function move($newPath_in) {
		$this->ftpConn->move($this->path, $newPath_in);
		$this->path = $newPath_in;
	}
	public function copy($newPath_in) {
		$this->ftpConn->copy($this->path, $newPath_in);
	}
	public function download() {
		return $this->ftpConn->downloadFile($this->path);
	}
	public function delete() {
		$»t = ($this->type);
		switch($»t->index) {
		case 0:
		{
			$this->ftpConn->deleteDirectory($this->path);
		}break;
		case 1:
		{
			$this->ftpConn->deleteFile($this->path);
		}break;
		default:{
		}break;
		}
	}
	public function appendNameToPath() {
		$this->path = $this->path . $this->name;
		if($this->type == ftp_FtpFileType::$dir) {
			$this->path = $this->path . "/";
		}
	}
	public function toString() {
		return $this->lsResult;
	}
	public function processLsResult() {
		$arr = null;
		$whitespace = new EReg("\\s+", "g");
		$arr = $whitespace->split($this->lsResult);
		$raw_typeAndPermissions = $arr->shift();
		$this->type = ftp_FtpItem_0($this, $arr, $raw_typeAndPermissions, $whitespace);
		$raw_numDirOrLinksInside = $arr->shift();
		$raw_owner = $arr->shift();
		$this->owner = $raw_owner;
		$raw_group = $arr->shift();
		$this->group = $raw_group;
		$raw_size = $arr->shift();
		$this->size = Std::parseInt($raw_size);
		$raw_month = $arr->shift();
		$raw_day = $arr->shift();
		$raw_timeOrYear = $arr->shift();
		$day = Std::parseInt($raw_day);
		$month = intval(_hx_index_of("Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec", $raw_month, null) / 4);
		$isTimeNotYear = _hx_index_of($raw_timeOrYear, ":", null) !== null;
		$hours = 0;
		$minutes = 0;
		$year = 0;
		if($isTimeNotYear) {
			$timeParts = _hx_explode(":", $raw_timeOrYear);
			$hours1 = Std::parseInt($timeParts[0]);
			$minutes1 = Std::parseInt($timeParts[1]);
			$nowDate = Date::now();
			$fileDate = new Date($nowDate->getFullYear(), $month, $day, $nowDate->getHours(), $nowDate->getMinutes(), 0);
			$howManySecsFileDateIsOlder = ($nowDate->getTime() - $fileDate->getTime()) / 1000;
			if($howManySecsFileDateIsOlder < 0) {
				$year = $nowDate->getFullYear() - 1;
			} else {
				$year = $nowDate->getFullYear();
			}
		} else {
			$year = Std::parseInt($raw_timeOrYear);
			$hours = 0;
			$minutes = 0;
		}
		$this->modified = new Date($year, $month, $day, $hours, $minutes, 0);
		$this->modifiedStr = DateTools::format($this->modified, "%F");
		$first8blocks = new EReg("(\\S+\\s+){8}", "");
		$leftOverName = $first8blocks->replace($this->lsResult, "");
		$this->name = $leftOverName;
		if($this->type == ftp_FtpFileType::$link) {
			$arr1 = _hx_explode(" -> ", $this->name);
			$this->name = $arr1[0];
			$this->target = $arr1[1];
		}
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
	static function newFromLsLine($ftpConn_in, $dirPath_in, $lsResult_in) {
		$item = new ftp_FtpItem($ftpConn_in, $dirPath_in, $lsResult_in);
		$item->appendNameToPath();
		return $item;
	}
	function __toString() { return $this->toString(); }
}
function ftp_FtpItem_0(&$»this, &$arr, &$raw_typeAndPermissions, &$whitespace) {
	switch(_hx_char_at($raw_typeAndPermissions, 0)) {
	case "-":{
		return ftp_FtpFileType::$file;
	}break;
	case "d":{
		return ftp_FtpFileType::$dir;
	}break;
	case "l":{
		return ftp_FtpFileType::$link;
	}break;
	default:{
		return ftp_FtpFileType::$unknown;
	}break;
	}
}
