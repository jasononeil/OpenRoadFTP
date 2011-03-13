/*
This class gets sent a POST variable for username and password.
We then see if we can establish an FTP connection.  If we can, start a session...
*/

import ftp.FtpConnection;
import Api;

class Download
{
	static function main() 
    	{
		var api:Api = new Api();
		api.checkLoggedIn();
		
		var params = php.Web.getParams();
		if (params.exists('key'))
		{
			var key = params.get('key');
			var sessionKey = api.session.get('DOWNLOAD.key');
			var str = StringTools.replace(key,sessionKey,"");
			
			if (str == "")
			{
				// Okay, that's enough that we'll assume it's the right person for the right file.
				var url = api.session.get('DOWNLOAD.url');
				untyped __php__('
				
				header("Content-type: application/force-download");
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-length: ".filesize($url));
				header("Content-disposition: attachment;filename=\\"".basename($url)."\\""); 
				readfile($url); 
				unlink($url);
				
				');
				
			}
		}
	}
}
