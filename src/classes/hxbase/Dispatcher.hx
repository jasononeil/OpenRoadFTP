package hxbase;

import controllers.ControllerRegistry;

/**
The Dispatcher class is responsible for deciding which
Controller class is being requested and which action 
should be executed.

This class is called on most page requests.  
*/
class Dispatcher
{
	private static var controllerRegistry:Hash<Class<Dynamic>> = new Hash();
	
	/** Takes a request query (usually from the URL) decides
	which controller class to use, passing any extra parameters
	to it as necessary. */
	public static function dispatch(request:String)
	{
		// Register all of our controllers
		ControllerRegistry.registerAll();
		
		// Get the various parts of the input
		var parts:Array<String> = getRequestParts(request);
		
		// Get the name of the first part
		var firstPart:String = parts[0];
		
		// See if mysite is a controller
		var controllerClass = controllerRegistry.get(firstPart);
		
		if (controllerClass != null)
		{
			// We have the Controller class, so git rid of that
			// from our list of parameters.
			parts.shift();
		}
		else
		{
			// What we had was not a Controller class, so load the
			// default one (defined in AppConfig).  And leave all 
			// parameters in tact to pass to this class.
			controllerClass = AppConfig.defaultController;
		}
		
		// just check our parts aren't empty
		if (parts.length == 0) parts.push("");
		
		// Now pass control to whichever controller class we have
		var controller:BaseController = Type.createInstance(controllerClass, [parts]);
		php.Lib.print(controller);
		
	}
	
	/** Each controller should register itself here.  
	By default, controllers will register a lower-case version of their
	name, without the word controller.  "PeopleController" becomes "people", 
	etc.  Now that controller can be accessed through "http://mysite.com/people/".
	You could add a custom URL scheme by registering your controller again, but
	with a different alias.  Eg. registerController("members", PeopleController)
	would allow you to access the controller at "http://mysite.com/members/" 
	
	<b>This should be done through the <i>aliases</i> array in your controller</b>
	*/
	public static function registerController(url:String, controller:Class<Dynamic>)
	{
		controllerRegistry.set(url, controller);
	}
	
	private static function getRequestParts(request:String):Array<String>
	{
		// if there's a trailing slash, get rid of it, we don't need it
		if (request.charAt(request.length - 1) == "/")
		{
			request = request.substr(0, request.length - 1);
		}
		
		return request.split('/');
	}
}
