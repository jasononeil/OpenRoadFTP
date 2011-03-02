import hxbase.tpl.HxTpl;
/** 
Register Controllers is a tool to go through all of our controllers and
generate the AllControllers.hx Class for us.  At the moment it just assumes
that all the controllers are in the directory we're passed (or subdirectories)
and that they are valid Controller classes.  I hope so...
*/
class RegisterControllers
{
	static function main()
	{
		var args = neko.Sys.args();
		if (args.length < 1)
		{
			neko.Lib.print('Usage: tools/registercontrollers.n src/controllers/ \n'); 
		}
		else
		{
			var dir:String = args.shift();
			var file:String = args.shift();
			execute(dir);
		}
	}
	
	static function execute(dirPath:String)
	{
		// Get our template (this is loaded into neko at compile time)
		var templateString = haxe.Resource.getString("template");
		var tpl = new HxTpl();
		tpl.loadTemplateFromString(templateString);
		
		// go over all the files in the dir
		var files = neko.FileSystem.readDirectory(dirPath);
		for (file in files)
		{
			// test if the filename is "*Controller.hx"
			var fileEndsInControllerHx = ~/(.+)Controller.hx$/;
			if (fileEndsInControllerHx.match(file))
			{
				var controller = tpl.newLoop("controller");
				var className = fileEndsInControllerHx.matched(1);
				controller.assign("className", className + "Controller");
				controller.assign("lowerCaseName", className.toLowerCase());
			}
		}
		var file = neko.io.File.write("src/controllers/ControllerRegistry.hx", false);
		file.writeString(tpl.getOutput());
	}
}
