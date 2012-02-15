package ftp;
import ftp.FtpItem;
import ftp.FtpFileType;
using Lambda;

class FtpFileList
{
	public var dirs:Hash<FtpItem>;
	public var files:Hash<FtpItem>;
	public var links:Hash<FtpItem>;
	public var all:Hash<FtpItem>;
	public var numDirs(countDirs,null):Int;
	public var numFiles(countFiles,null):Int;
	public var numLinks(countLinks,null):Int;
	
	static var t = 0;
	public function new(cnx:FtpConnection, path:String)
	{
		dirs = new Hash();
		files = new Hash();
		links = new Hash();
		
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
	
	public function countDirs():Int
	{
		return dirs.count();
	}
	
	public function countFiles():Int
	{
		return files.count();
	}
	
	public function countLinks():Int
	{
		return links.count();
	}
}
