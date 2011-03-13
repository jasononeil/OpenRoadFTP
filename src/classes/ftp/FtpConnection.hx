package ftp;
import php.NativeArray;
import hxbase.util.Error;
import ftp.FtpFileList;
import ftp.FtpItem;
import ftp.FtpDir;
using StringTools;

class FtpConnection
{
	public var server:String;
	public var username:String;
	public var password:String;
	public var port:Int;
	public var timeout:Int;
	public var fakeRoot:String;
	public var tmpDir:String;
	public var conn:Dynamic;
	public var isReady:Bool;
	
	public function new(server_in, user_in, pass_in, tmpDir_in, ?fakeRoot_in:String, ?port_in:Int, ?timeout_in:Int)
	{
		if (timeout_in == null) timeout_in = 90;
		if (port_in == null) port_in = 21;
		if (fakeRoot_in == null) fakeRoot_in = "/";
		isReady = false;
		server = server_in;
		username = user_in;
		password = pass_in;
		fakeRoot = fakeRoot_in;
		port = port_in;
		timeout = timeout_in;
		tmpDir = tmpDir_in;
		registerErrorTypes();
		conn = untyped __call__("ftp_connect", server, port, timeout);
		if (untyped __physeq__(conn, false))
		{
			// Server is down!  Throw an error
			throw new Error("FTP.SERVER_NOT_FOUND");
		}
		else
		{
			// Server is up! Try login
			var loginOkay:Bool = untyped __call__("ftp_login", conn, username, password);
			if (loginOkay == false)
			{
				throw new Error("FTP.BAD_LOGIN");
			}
		}
		return true;
	}
	
	public function registerErrorTypes()
	{
		Error.registerErrorType("FTP.SERVER_NOT_FOUND", "The FTP server seems to be down.", null, null);
		Error.registerErrorType("FTP.BAD_LOGIN", "The FTP server rejected your login.", null, null);
		Error.registerErrorType("FTP.MOVE_FAILED", "We were unable to move (or rename) the file.  It's probably read only.", null, null);
		Error.registerErrorType("FTP.COPY_READ_FAILED", "We were unable to copy the file.  The file you're coping couldn't be read, do you have permissions?", null, null);
		Error.registerErrorType("FTP.COPY_WRITE_FAILED", "We were unable to copy the file.  The place you're coping to might be read only.", null, null);
		Error.registerErrorType("FTP.DELETE_FILE_FAILED", "We were unable to delete the file.  It's probably read only.", null, null);
		Error.registerErrorType("FTP.DELETE_DIR_FAILED", "We were unable to delete the folder.  There might be a file inside which just won't delete.", null, null);
		Error.registerErrorType("FTP.MAKE_DIR_FAILED", "We were unable to create a new folder.  The folder you're in might be read only.", null, null);
	}
	
	public function isDir(path_in:String)
	{
		var result = false;
		var path = sanitizePath(path_in);
		try {
			result = untyped __call__(ftp_chdir, conn, path);
		} catch (e:String) {
			result = false;	
		}
		return result;
	}
	
	public function isFile(path_in:String)
	{
		var path = sanitizePath(path_in);
		var filesize = null;
		filesize = untyped __call__(ftp_size, conn, path);
		return (filesize == -1) ? false : true;
	}
	
	public function exists(path_in:String)
	{
		var path = sanitizePath(path_in);
		return (isFile(path) || isDir(path));
	}
	
	public function getFileAt(path:String)
	{
		return new FtpItem(this, path);
	}
	
	public function getDirAt(path:String)
	{
		return new FtpDir(this, path);
	}
	
	public function ls(?path_in:String = "/", ?recursive:Bool = false)
	{
		var a:Array<String> = null;
		var s = null;
		var na:NativeArray = null;
		var path = sanitizePath(path_in);
		var finalChar = path.charAt(path.length - 1);
		var weAreListingChildren = (finalChar == "/") ? true : false;
		var weAreGettingDirInfo = false;
		if (weAreListingChildren == false) weAreGettingDirInfo = isDir(path);
		if (weAreListingChildren || weAreGettingDirInfo == false)
		{
			na = untyped __call__(ftp_rawlist, conn, path, recursive);
			a = untyped php.Lib.toHaxeArray(na);
		}
		else
		{
			var onlyLastName = ~/([^\/]+)\$/;
			var name = onlyLastName.match(path) ? onlyLastName.matched(0) : null;
			var parentPath = onlyLastName.replace(path,"");
			na = untyped __call__(ftp_rawlist, conn, parentPath, recursive);
			a = untyped php.Lib.toHaxeArray(na);
			/* REALLY NOT SURE IF THIS FILTER FUNCTION IS CORRECT, OR WHAT IT DOES... */
			
			var filter = function (line_in) {
				// the first 8 blocks - think this is the first 8 words followed by whitespace.  What's left should be the name
				var first8blocks = ~/(\S+\s+){8}/;
				var leftOverName = first8blocks.replace(line_in, "");
				// If the file is a link, (in which case the line begins with 'l', then it has the link target, not just the name
				// We need to get ONLY the bit before the ' -> '
				if (line_in.startsWith('l')) {
					leftOverName = leftOverName.split(' -> ')[0];
				}
				return (name == leftOverName);
			}
			var list = Lambda.filter(a,filter);
			a = Lambda.array(list);
		}
		if (a == null)
		{
			a = new Array();
			a.push("File not found.");
		}
		return a;
	}
	
	public function move(oldPath_in, newPath_in)
	{
		var didRenameWork:Bool;
		var oldPath = sanitizePath(oldPath_in);
		var newPath = sanitizePath(newPath_in);
		didRenameWork = untyped __call__("ftp_rename",conn,oldPath,newPath);
		if (!didRenameWork)
		{
			throw new Error("FTP.RENAME_FAILED");
		}
	}
	
	public function copy(path_in, newPath_in)
	{
		var newPathOnFtpServer:String;
		var tmpPathOnWebServer:String;
		
		// first download the file
		try {
			tmpPathOnWebServer = downloadFile(path_in);
		} catch (e:Dynamic) {
			throw new Error('FTP.COPY_READ_FAILED');
		}
		
		// now copy it back up
		try {
			newPathOnFtpServer = newPath_in;
			var folder:String = null;
			var name:String = null;
			var extension:String = null;
			var number = 1;
			// if the filename already exists, add a number to our filename.  Do this till it doesn't exist
			while (exists(newPathOnFtpServer)) {
				if (folder == null || name == null || extension == null)
				{
					var onlyLastName = ~/([^\/]+)\$/;
					onlyLastName.match(newPathOnFtpServer);
					folder = onlyLastName.replace(newPathOnFtpServer, "");
					var nameAndExtension = onlyLastName.matched(0);
					var anythingBeforeADot = ~/^([^.]*)$/;
					anythingBeforeADot.match(nameAndExtension);
					name = anythingBeforeADot.replace(nameAndExtension, "");
				}
				number++;
				newPathOnFtpServer = folder + name + " (" + number + ")" + extension;
			}
			uploadFile(tmpPathOnWebServer, newPathOnFtpServer);
			php.FileSystem.deleteFile(tmpPathOnWebServer);
		} catch (e:Dynamic) {
			throw new Error ("FTP.COPY_WRITE_FAILED");
		}
	}
	
	public function deleteFile(path_in) 
	{
		var didDeleteWork:Bool;
		var path = sanitizePath(path_in);
		didDeleteWork = untyped __call__("ftp_delete", conn, path);
		if (!didDeleteWork) throw new Error("FTP.DELETE_FILE_FAILED");
	}
	
	public function deleteDirectory(path_in)
	{
		var didDeleteWork:Bool;
		var path = sanitizePath(path_in);
		var fileList = new FtpFileList(this,path + "/");
		for (ftpDir in fileList.dirs)
		{
			deleteDirectory(ftpDir.path);
		}
		for (ftpItem in fileList.files)
		{
			deleteFile(ftpItem.path);
		}
		didDeleteWork = untyped __call__("ftp_rmdir", conn, path);
		if (!didDeleteWork) throw new Error("FTP.DELETE_DIR_FAILED");
	}
	
	public function mkdir(path_in)
	{
		var didMakeDirWork:Bool;
		var path = sanitizePath(path_in);
		didMakeDirWork = untyped __call__('ftp_mkdir',conn,path);
		if (!didMakeDirWork) throw new Error("FTP.MAKE_DIR_FAILED");
	}
	
	public function createWebServerDir(webServerDir:String)
	{
		var dirParts = webServerDir.split('/');
		var fullPath = "";
		if (!php.FileSystem.exists(webServerDir))
		{
			while (dirParts.length > 0)
			{
				var dirName = dirParts.shift();
				if (dirName != "")
				{
					fullPath = fullPath + "/" + dirName;
					var webServerDirExists = php.FileSystem.exists(fullPath);
					if (webServerDirExists == false)
					{
						try {
							php.FileSystem.createDirectory(fullPath);
						} catch (e:Dynamic)
						{
							throw new Error("FTP.CREATE_TMP_DIR_FAILED");
						}
					}
				}
			}
		}
	}
	
	public function downloadFile(ftpPath_in)
	{
		var webServerPath = "";
		var didDownloadWork:Bool;
		var ftpPath = sanitizePath(ftpPath_in);
		var cwd = php.Sys.getCwd();
		webServerPath = cwd + "/" + tmpDir + ftpPath;
		var onlyLastName = ~/([^\/]+)\$/;
		var webServerDir = onlyLastName.replace(webServerPath,"");
		createWebServerDir(webServerDir); // will only create if it doesn't exist already
		var serverMode = untyped __php__("FTP_BINARY");
		didDownloadWork = untyped __call__("ftp_get",conn,webServerPath,ftpPath,serverMode);
		if (!didDownloadWork) throw new Error("FTP.DOWNLOAD_FAILED");
		return webServerPath;
	}
	
	public function uploadFile(tmpPathOnWebServer, newPathOnFtpServer_in)
	{
		var newPathOnFtpServer = sanitizePath(newPathOnFtpServer_in);
		var folderToPasteIn = "";
		var fileToPasteAs = "";
		var serverMode = untyped __php__("FTP_BINARY");
		var didUploadWork = false;
		var didChdirWork = false;
		var onlyLastName = ~/([^\/]+)\$/;
		onlyLastName.match(newPathOnFtpServer);
		folderToPasteIn = onlyLastName.replace(newPathOnFtpServer,"");
		fileToPasteAs = onlyLastName.matched(0);
		didChdirWork = untyped __call__('ftp_chdir',conn,folderToPasteIn);
		if (didChdirWork) 
		{
			didUploadWork = untyped __call__('ftp_put',conn,fileToPasteAs,tmpPathOnWebServer,serverMode);
		}
		else
		{
			throw "HERE!";
		}
		if (!didUploadWork)
		{
			throw new Error ("FTP.UPLOAD_FAILED");
		}
	}
	
	public function sanitizePath(path_in:String)
	{
		// if it's not in the fake root, prepend the fake root to their path
		var path = (path_in.substr(0, fakeRoot.length) == fakeRoot) ? path_in : fakeRoot + path_in;
		var slashMultipleDotsSlash = ~/[\/\\]\.{2,}[\/\\]/;
		path = slashMultipleDotsSlash.replace(path,'/');
		return path;
	}
}
