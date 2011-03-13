package ftp;
import ftp.FtpItem;
import ftp.FtpFileType;
using Lambda;

class FtpFileList
{
	public var dirs:Hash<FtpItem>;
	public var files:Hash<FtpItem>;
	public var links:Hash<FtpItem>;
	public var numDirs(countDirs,null):Int;
	public var numFiles(countFiles,null):Int;
	public var numLinks(countLinks,null):Int;
	
	public function new(cnx:FtpConnection, path:String)
	{
		dirs = new Hash();
		files = new Hash();
		links = new Hash();
		
		var lsResult:Array<String> = cnx.ls(path);
		for (line in lsResult)
		{
			var file = new FtpItem(cnx,path,line);
			if (file.type == FtpFileType.link)
			{
				links.set(path,file);
			}
			else if (file.type == FtpFileType.dir)
			{
				dirs.set(path,file);
			}
			else if (file.type == FtpFileType.file)
			{
				dirs.set(path,file);
			}
			
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
