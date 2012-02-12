import haxe.remoting.HttpAsyncConnection;

// Create the proxy class that will talk to the server "Api" class.
class ApiProxy extends haxe.remoting.AsyncProxy<Api> { }

// Now the main class we'll load, that in turn will load the proxy etc...
class AjaxClient
{
	static var url:String;
	static var cnx:HttpAsyncConnection;
	static var proxy:ApiProxy;
	
	static function main()
	{	
		url = "api.php";
		cnx = HttpAsyncConnection.urlConnect(url);
		proxy = untyped new ApiProxy(cnx.api);
		
	}
	
}


