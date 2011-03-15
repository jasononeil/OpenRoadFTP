#if php
import ftp.FtpConnection;
import ftp.FtpFileList;
import ftp.FtpItem;
import ftp.FtpFileType;
import hxbase.session.SessionHandler;
import hxbase.tpl.HxTpl;
import AppConfig;
using Lambda;
#end
import hxbase.util.Error;

class Api 
{
	// 
	// The static classes are used to create an instance of this class, and share it for remoting
	//
	static var inst : Api;
	static function main() 
	{
		inst = new Api();
		
		registerErrorMessages();
		
		// create an incoming connection and give acces to the "inst" object
		var ctx = new haxe.remoting.Context();
		//ctx.addObject("inst",inst);
		ctx.addObject("api",inst);
		//haxe.remoting.ExternalConnection.flashConnect("api",null,ctx);
		haxe.remoting.HttpConnection.handleRequest(ctx);
	}
	
	static function registerErrorMessages()
	{
		// (code, error, explanation, suggestion)
		Error.registerErrorType(
			"SESSION.NOT_LOGGED_IN", 
			"Please sign in", 
			"We won't be able to get started until you've signed in.", 
			"Please sign in with your S drive username and password."
		);
		Error.registerErrorType(
			"SESSION.LOGGED_OUT", 
			"See you next time!", 
			"You've signed out successfully.", 
			"Don't worry, your files are safe with us."
		);
		Error.registerErrorType(
			"SESSION.TIMED_OUT", 
			"I thought you were gone!", 
			"Sorry, after 5 minutes we sign you out automatically, to keep your files safe in case you're gone.", 
			"You'll have to sign in again.  Make sure you're quick!"
		);
		Error.registerErrorType(
			"SESSION.INCORRECT_LOGIN", 
			"Try again...", 
			"Your username or password seems to be incorrect."
		);
		Error.registerErrorType(
			"FTP.SERVER_DOWN", 
			"The student server seems to be down.", 
			"We're really sorry but it looks like the student server is down at the moment.", 
			"You might want to try again a bit later.  If there's a teacher nearby, perhaps let them know so the IT guys can get onto it."
		);
		Error.registerErrorType(
			"FTP.OPERATION_FAILED", 
			"Sorry, that didn't work.", 
			"Whatever it was you were trying to do just failed, and we're not entirely sure why.", 
			"Check the file or folder is not read only, try again later or ask for help."
		);
	}
	
	//
	// The rest are the classes available to remoting
	//
	
	private var loginOkay:Bool;
	private var error:String;
	#if php
	private var ftp:FtpConnection;
	public var session(default,null):SessionHandler;
	private var tpl:HxTpl;
	#end
	
	public function new()
	{
		#if php
		session = new SessionHandler(AppConfig.sessionID, AppConfig.sessionTimeOut);
		#end
	}
	
	private function initiateFTP(username,password,id)
	{
		#if php
		
		var server:String = AppConfig.ftpServer;
		var tmpFolder:String = "tmp/" + id + "-" + username;
		var home:String;
		
		home = AppConfig.getHomeDir(username);
		
		try
		{
			ftp = new FtpConnection(server, username, password, tmpFolder, home);
		}
		catch (e:Error)
		{
			switch (e.code)
			{
				case "FTP.SERVER_NOT_FOUND":
					throw new Error("FTP.SERVER_DOWN");
				case "FTP.BAD_LOGIN":
					throw new Error("SESSION.INCORRECT_LOGIN");
			}
			
		}
		#end
	}
	
	public function checkLoggedIn():Void
	{
		session_keepalive();
		initiateFTP(session.get('username'), session.get('password'), session.get('SESSION.firstID'));
	}
	
	public function session_getLoginForm(err:Dynamic):String
	{
		var str:String;
		#if php
		tpl = new HxTpl();
		tpl.loadTemplateFromFile('./tpl/login.hxtpl');
		tpl.assign('message', err.error);
		if (Reflect.hasField(err, 'explanation'))
		{
			tpl.setSwitch('explanation', true).assign('explanation', err.explanation);
		}
		if (Reflect.hasField(err, 'suggestion'))
		{
			tpl.setSwitch('suggestion', true).assign('suggestion', err.suggestion);
		}
		str = tpl.getOutput();
		#end
		return str;
	}
	
	public function session_login(username:String, password:String):Bool
	{
		#if php
		
		// Start the session and grab the unique ID
		var id:String = session.start().get('SESSION.firstID');
		
		// initiate the FTP
		initiateFTP(username,password,id);
		
		// if login was successful, and no errors thrown, initiate the session.
		session.set('username',username).set('password',password);
		
		#end
		
		// the javascript remoting callback doesn't seem to be called unless we have a return value
		return true;
	}
	
	public function session_keepalive()
	{
		#if php
		// Rather than return true or false, this will throw an error if we're not logged in
		try 
		{
			session.check();
		}
		catch (err:Error)
		{
			switch (err.code)
			{
				case "SESSION.NO_SESSION":
					throw new Error("SESSION.NOT_LOGGED_IN");
				case "SESSION.TIMEOUT":
					throw new Error("SESSION.TIMED_OUT");
			}
		}
		#end
	}
	
	public function session_logoff()
	{
		#if php
		session.end();
		
		//
		// this would be a good point to delete the session directory.  
		// technically it should just be a bunch of empty directorys, which
		// "./scripts/clearTempFiles.php" should get rid of, even if they belong
		// to another user.  So just include that file.
		untyped __php__('include("scripts/clearTempFiles.php");');
		
		
		#end
		
		throw new Error("SESSION.LOGGED_OUT");
	}
	
	public function getBrowserMask():String
	{
		var str:String;
		#if php
		checkLoggedIn();
		
		tpl = new HxTpl();
		tpl.loadTemplateFromFile('./tpl/browserMask.hxtpl');
		str = tpl.getOutput();
		#end
		return str;
	}
	
	public function getDirListing(path:String):String
	{
		var str:String;
		
		#if php
		checkLoggedIn();
		
		var tpl = new HxTpl();
		var ftpFileList = new FtpFileList(ftp,path);
		
		tpl.loadTemplateFromFile('./tpl/dirList.hxtpl');
		
		// ([^\/]+) means anything except a slash, at least one character. The () group it for capturing
		// \/$    means a forward slash (/) at the end of the line
		var onlyLastName:EReg = ~/([^\/]+)\/$/;
		var name:String = (onlyLastName.match(path)) ? onlyLastName.matched(1) : "Home";
		
		tpl.assign("dir.name", name);
		tpl.assign("dir.path", path);
		//tpl.assign("dir.pathID", getID(path));
		
		for (link in ftpFileList.links)
		{
			// If we're allowing all symlinks, or if we're filtering them, and this link is allowed
			if (!AppConfig.limitSymlinks || AppConfig.allowedSymlinks.has(link.name))
			{
				var ftpItem:HxTpl = tpl.newLoop("ftpItem");
				ftpItem.assignObject("file", link);
				ftpItem.assign("file.type", "dir");
				ftpItem.assign("file.path", link.path + '/');
			}
		}
		for (dir in ftpFileList.dirs)
		{
			var ftpItem:HxTpl = tpl.newLoop("ftpItem");
			ftpItem.assignObject("file", dir);
		}
		for (file in ftpFileList.files)
		{
			var ftpItem:HxTpl = tpl.newLoop("ftpItem");
			ftpItem.assignObject("file", file);
		}
		
		str = tpl.getOutput();
		
		#end
		
		return str;
	}
	
	public function moveFile(oldPath:String, newPath:String):Bool
	{
		#if php
		checkLoggedIn();
		
		var file:FtpItem;
		file = ftp.getFileAt(oldPath);
		if (file.type != FtpFileType.link)
		{
			file.move(newPath);
		}
		#end
		
		return true;
	}
	
	public function deleteFile(path:String):Bool
	{
		#if php
		checkLoggedIn();
		
		var file:FtpItem;
		file = ftp.getFileAt(path);
		if (file.type != FtpFileType.link)
		{
			file.delete();
		}
		#end
		
		return true;
	}
	
	public function deleteFiles(pathsToDelete:Array<String>):Bool
	{
		#if php
		checkLoggedIn();
		var file:FtpItem;
		
		for (path in pathsToDelete)
		{
			file = ftp.getFileAt(path);
			if (file.type != FtpFileType.link)
			{
				file.delete();
			}
		}
		#end
		
		return true;
	}
	
	public function pasteFromCut(filesToMove:Array<Dynamic<String>>, newDir:String):Bool
	{
		#if php
		checkLoggedIn();
		var file:FtpItem;
		
		for (fileData in filesToMove)
		{
			var newPath = newDir + fileData.name;
			file = ftp.getFileAt(fileData.oldPath);
			if (file.type != FtpFileType.link)
			{
				file.move(newPath);
			}
		}
		#end
		
		return true;
	}
	
	public function pasteFromCopy(filesToMove:Array<Dynamic<String>>, newDir:String):Bool
	{
		#if php
		checkLoggedIn();
		var file:FtpItem;
		
		for (fileData in filesToMove)
		{
			var newPath = newDir + fileData.name;
			file = ftp.getFileAt(fileData.oldPath);
			file.copy(newPath);
		}
		#end
		
		return true;
	}
	
	public function upload(localFilePath:String, ftpPath:String):Void
	{
		#if php
		checkLoggedIn();
		ftp.uploadFile(localFilePath, ftpPath);
		#end
	}
	
	public function mkdir(path:String):Bool
	{
		#if php
		checkLoggedIn();
		ftp.mkdir(path);
		#end
		
		return true;
	}
	
	/*
	How this process works.
	JS interface selects a file, gets the URL and passes it to this function.
	This function downloads it from FTP, and saves it to a tmp file.
	When it's done, it saves the download URL and a 'key' to the session.
	It passes this key back to the AJAX interface.
	The AJAX interface then accesses 'download.php', and passes it the key.
	If the key fits (is correct), then it passes that tmp file through to the user to download.
	Once done, it destroys the tmp file and the session variables.
	*/
	public function download(path:String):String
	{
		var downloadKey:String;
		
		#if php
		checkLoggedIn();
		var file = ftp.getFileAt(path);
		var url = file.download();
		downloadKey = Std.string(Math.random());
		session.set('DOWNLOAD.url',url).set('DOWNLOAD.key',downloadKey);
		
		#end
		return downloadKey;
	}
	
	public function test(x : Int, y : Int) : Int 
	{
		return x + y;
	}
}
