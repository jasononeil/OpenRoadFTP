/*
This class gets sent a POST variable for username and password.
We then see if we can establish an FTP connection.  If we can, start a session...
*/

import ftp.FtpConnection;
import hxbase.

class Login
{
	static function main() 
    	{
		var params:Hash<String>;
		var username:String;
		var password:String;
		var ftp:FtpConnection;
		var loginOkay:Bool;
        	var error:String;
		var session:SessionHandler;
		
        	session = new SessionHandler('WbcStudentLoginSessionID');
		
        	loginOkay = false;
		var params = php.Web.getParams();
		if (params.exists('username') && params.exists('password'))
		{
			username = params.get('username');
			password = params.get('password');
			try
        		{
				ftp = new FtpConnection('localhost', username, password);
        			session.start().set('username',username).set('password',password);
        			loginOkay = true;
        		}
        		catch (e:String)
        		{
        			error = e;
        		}
		}
		
		var msg:String;
		if (!loginOkay)
		{
			session.end();
			msg = "FAILURE";
		}
		else
		{
			msg = "SUCCESS";
		}
		
		php.Lib.print(msg);
	}
}
