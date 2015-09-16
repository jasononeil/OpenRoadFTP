/*
This class gets sent a POST variable for username and password.
We then see if we can establish an FTP connection.  If we can, start a session...
*/

import ftp.FtpConnection;
import hxbase.session.SessionHandler;
import Api;

class Upload
{
	static function main() 
    	{
		/*
		Straight PHP code
		$uploaddir = '/home/jason/WebRoot/studentlogin/tmp/';
		$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) 
		{
			echo "success";
		} 
		else 
		{
			// WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
			// Otherwise onSubmit event will not be fired
			echo "error";
		}
		*/
		
		var api:Api = new Api();
		api.checkLoggedIn();
		
		try
		{
			var params = php.Web.getParams();
			var folder = (params.exists('path')) ? params.get('path') : '/';
			
			var localPath:String = untyped __php__("$_FILES[\"userfile\"][\"tmp_name\"]");
			var ftpPath:String = folder + untyped __php__("basename($_FILES[\"userfile\"][\"name\"])");
			api.upload(localPath, ftpPath);
			php.Lib.print("success");
		}
		catch(e:Dynamic)
		{
			php.Lib.print("error: " + e);
		}
		
	}
}
