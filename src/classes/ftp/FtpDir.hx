package ftp;
import ftp.FtpConnection;
using StringTools;

class FtpDir
{
	public var path:String;
	public var ftp:FtpConnection;
	public var exists(getter_exists,null):Bool;
	public var name:String;
	public var owner:String;
	public var group:String;
	public var permissions:String;
	
	public function new(ftp_in:FtpConnection, path_in:String)
	{
		path = path_in.replace("\\","/");
		ftp = ftp_in;
		if (ftp.isReady)
		{
			var r = ~/\/.+\//;
			if (r.match(path))
			{
				trace ("FTW! Check if it exists and populate fields");
			}
			else
			{
				throw "Trying to create new FtpDir object but the path given isn't valid.";
			}
		}
		else
		{
			throw "Trying to create a new FtpDir object with an FtpConnection that's not ready";
		}
	}
	
	public function toString() { return path; }
	
	public function getter_exists() 
	{
		return ftp.isDir(path);
	}
}
