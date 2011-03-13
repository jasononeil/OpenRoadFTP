package ftp;
import ftp.FtpConnection;
import ftp.FtpFileType;
import hxbase.Log;
using DateTools;

class FtpItem
{
	public var ftpConn:FtpConnection;
	public var path:String;
	public var lsResult:String;
	public var name:String;
	public var type:FtpFileType;
	public var permissions:Dynamic<Bool>;
	public var size:Int;
	public var owner:String;
	public var group:String;
	public var modified:Date;
	public var modifiedStr:String;
	public var target:String;
	
	public function new(ftpConn_in, path_in, ?lsResult_in)
	{
		Log.assert(ftpConn_in != null, "ftpConn_in must not be null");
		Log.assert(path_in != null, "path_in must not be null");
		ftpConn = ftpConn_in;
		path = path_in;
		name = "File Not Found.";
		type = FtpFileType.unknown;
		permissions = {
			ownerRead: false,
			ownerWrite: false,
			ownerExecute: false,
			groupRead: false,
			groupWrite: false,
			groupExecute: false,
			otherRead: false,
			otherWrite: false,
			otherExecute: false,
		}
		size = 0;
		owner = "";
		group = "";
		target = "";
		if (lsResult_in == null)
		{
			var arr = ftpConn.ls(path_in, null);
			if (arr.length > 0)
			{
				lsResult = arr[0];
			}
			else
			{
				lsResult = "File not found.";
			}
		}
		else
		{
			lsResult = lsResult_in;
		}
		if (lsResult != "File not found.")
		{
			this.processLsResult();
		}
	}
	
	public function move(newPath_in)
	{
		ftpConn.move(path, newPath_in);
		path = newPath_in;
	}
	
	public function copy(newPath_in)
	{
		ftpConn.copy(path,newPath_in);
	}
	
	public function download()
	{
		return ftpConn.downloadFile(path);
	}
	
	public function delete() 
	{
		switch (type)
		{
			case FtpFileType.dir:
				ftpConn.deleteDirectory(path);
			case FtpFileType.file:
				ftpConn.deleteFile(path);
		}
	}
	
	public function appendNameToPath()
	{
		path = path + name;
		if (type == FtpFileType.dir)
		{
			path = path + "/";
		}
	}
	
	public function toString()
	{
		return lsResult;
	}
	
	public function processLsResult()
	{
		var arr:Array<String>;
		var whitespace = ~/\s+/g;
		arr = whitespace.split(lsResult);
		var raw_typeAndPermissions:String = arr.shift();
		type = switch (raw_typeAndPermissions.charAt(0))
		{
			case "-": FtpFileType.file;
			case "d": FtpFileType.dir;
			case "l": FtpFileType.link;
			default: FtpFileType.unknown;
		};
		// Not implemented yet
		//permissions = 
		var raw_numDirOrLinksInside = arr.shift();
		var raw_owner = arr.shift();
		owner = raw_owner;
		var raw_group = arr.shift();
		group = raw_group;
		var raw_size = arr.shift();
		size = Std.parseInt(raw_size);
		
		var raw_month = arr.shift();
		var raw_day = arr.shift();
		var raw_timeOrYear = arr.shift();
		var day = Std.parseInt(raw_day);
		var month = Std.int("Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".indexOf(raw_month) / 4);
		var isTimeNotYear = (raw_timeOrYear.indexOf(":") != null);
		var hours = 0;
		var minutes = 0;
		var year = 0;
		if (isTimeNotYear)
		{
			var timeParts = raw_timeOrYear.split(":");
			var hours = Std.parseInt(timeParts[0]);
			var minutes = Std.parseInt(timeParts[1]);
			var nowDate = Date.now();
			var fileDate = new Date(nowDate.getFullYear(), month, day, nowDate.getHours(), nowDate.getMinutes(), 0);
			var howManySecsFileDateIsOlder = (nowDate.getTime() - fileDate.getTime()) / 1000;
			if (howManySecsFileDateIsOlder < 0)
			{
				year = nowDate.getFullYear() - 1;
			}
			else
			{
				year = nowDate.getFullYear();
			}
		}
		else
		{
			year = Std.parseInt(raw_timeOrYear);
			hours = 0;
			minutes = 0;
		}
		modified = new Date(year,month,day,hours,minutes,0);
		modifiedStr = modified.format("%F");
		var first8blocks = ~/(\S+\s+){8}/;
		var leftOverName = first8blocks.replace(lsResult,"");
		name = leftOverName;
		if (this.type == FtpFileType.link)
		{
			var arr1 = name.split(" -> ");
			name = arr1[0];
			target = arr1[1];
		}
	}
	
	/* I theorize that this static function is redundant - just use the constructor
	/*static public function newFromLsLine(ftpConn_in, dirPath_in, lsResult_in)
	{
		var item = new FtpItem(ftpConn_in, dirPath_in, lsResult_in);
		item.appendNameToPath();
		return item;
	}*/
}
