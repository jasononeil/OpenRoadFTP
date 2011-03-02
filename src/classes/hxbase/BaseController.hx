package hxbase;
import hxbase.tpl.HxTpl;
using StringTools;

/**
Your controllers should inherit from this base class.
*/
class BaseController
{
	/** Can this controller be cached?  (Read only)
	In your Controller Class definition, set this to true or false */
	public var isCacheable(default,null):Bool;
	private var actions:Hash<String>;
	private var output:String;
	
	/** View is the bit of the template specific to this action */
	private var view:HxTpl;
	/** Template is the overall HTML of the page.  Either a site wide 
	or controller wide template.  */
	private var template:HxTpl;
	
	/** If all your actions in this controller sit inside a specific
	template, add this template here.  The specific actions will show
	up inside the &gthxInclude name="content">.  If you do use a 
	pageTemplateFile, you can initialise it (assign values, loops,
	other includes etc) in the initiatePageTemplate() function, which
	you override for your controller.  */
	private var pageTemplateFile:String;
	private function initiatePageTemplate()
	{
		template = new HxTpl();
		template.loadTemplateFromFile(pageTemplateFile);
	}
	
	/** Create a static array "aliases" with any alternate URL requests
	that you want to point to this controller.  */
	static public var aliases = [];
	
	
	/** The new() constructor will probably be called by the Dispatcher
	if it decides this is the Controller to use.  The constructor should
	take the arguments, decide on which "action" (method) should be called,
	and call it.  */
	public function new(args:Array<String>)
	{
		// Make sure we don't have empty arguments
		// MAKE SURE THIS HAPPENS --^
		
		//
		// This bit of code goes through all the properties of this
		// and takes out the functions that are our actions
		//
		actions = new Hash();
		var thisClass = Type.getClass(this);
		var fields:Array<String> = Type.getInstanceFields(thisClass);
		for (field in fields)
		{
			if (Reflect.isFunction(Reflect.field(this,field)))
			{
				if (field != "hprint" 
				&& field != "toString" 
				&& field != "clearOutput"
				&& field != "loadTemplate"
				&& field != "initiatePageTemplate"
				&& field != "printTemplate")
				{
					actions.set(field.toLowerCase(), Reflect.field(this,field));
				}
			}
		}
		
		
		// If first part is one of our actions, then load that action
		var firstArg = args[0];
		if (actions.exists(firstArg))
		{
			// how can we make sure the arguments are of correct type?
			// I think HaxIgniter might do this so take a look at that?
			args.shift();
			Reflect.callMethod(this,actions.get(firstArg),args);
		}
		else
		{
			// use the default one...
			Reflect.callMethod(this,this.getDefaultAction(),args);
		}
	}
	
	/** Override this method to set your default action.  
	Your override method should call:
	
	super.defaultAction(args,"myDefaultAction");
	
	or something like that.*/
	public function getDefaultAction()
	{
		return function () {};
	}
	
	/** Load the template.  Either pass the file path to load,
	or else use convention (views/controller/action.tpl).
	This may have to be re-thought if we want to allow loading
	templates from databases. */
	private function loadTemplate(?str:String = null, ?pos:haxe.PosInfos)
	{
		// Find the path for the view template
		var viewPath:String;
		if (str != null) 
		{
			// use the one the user specified
			viewPath = str; 
		}
		else 
		{
			// none specified.  Use convention to decide.
			var controller = pos.className.replace("Controller","")
							.replace(".", "/")
							.replace("controllers/", "")
							.toLowerCase();
			var action = pos.methodName.toLowerCase();
			viewPath = "views/" + controller + "/" + action + ".tpl";
		}
		
		// Now decide if we have the view by itself, or if it sits in a template.
		// Priority: 1) Controller template 2) App template 3) By itself
		template = null;
		if (this.pageTemplateFile != null)
		{
			// Use the template for this controller
			initiatePageTemplate();
			view = template.include("content",viewPath);
		}
		else if (MainApp.pageTemplateFile != null)
		{
			// Use the template for the App
			template = MainApp.initiatePageTemplate();
			view = template.include("content",viewPath);
		}
		else
		{
			// Just use the view by itself
			view = new HxTpl();
			view.loadTemplateFromFile(viewPath);
			template = view; // for all purposes, they're the same now.
		}
		
	}
	
	private function printTemplate()
	{
		clearOutput();
		if (view != null)
		{
			print(template.getOutput());
		}
		else
		{
			Log.error("Trying to printTemplate() when loadTemplate() hasn't run yet.");
		}
	}
	
	/** The toString() method should give the output from the various
	actions we've called.  This means elsewhere you'll be able to use:
	<pre>	myController = new MyController(args);
	php.Lib.print(myController);</pre>
	to print all the output.*/
	public function toString():String
	{
		return view.getOutput();
	}
	
	/** In your methods, use print() to write to the output */
	private function print(str)
	{
		output = output + Std.string(str);
	}
	
	/** In your methods, if you want to clear the output, use this */
	private function clearOutput()
	{
		output = "";
	}
}
