package ftp;
import ftp.FtpItem;
import ftp.FtpFileType;
using Lambda;

class FtpFileList
{
	public var dirs:Map<String, FtpItem>;
	public var files:Map<String, FtpItem>;
	public var links:Map<String, FtpItem>;
	public var all:Map<String, FtpItem>;
	public var numDirs(get,null):Int;
	public var numFiles(get,null):Int;
	public var numLinks(get,null):Int;
	
	static var t = 0;
	public function new(cnx:FtpConnection, path:String)
	{
		dirs = new Map();
		files = new Map();
		links = new Map();
		
		var lsResult:Array<String> = cnx.ls(path);
		for (line in lsResult)
		{
			var file = FtpItem.newFromLsLine(cnx,path,line);
			if (file.type == FtpFileType.link)
			{
				links.set(file.path,file);
			}
			else if (file.type == FtpFileType.dir)
			{
				dirs.set(file.path,file);
			}
			else if (file.type == FtpFileType.file)
			{
				files.set(file.path,file);
			}
			t++;
		}
	}
	
	public function get_numDirs():Int
	{
		return dirs.count();
	}
	
	public function get_numFiles():Int
	{
		return files.count();
	}
	
	public function get_numLinks():Int
	{
		return links.count();
	}
}
