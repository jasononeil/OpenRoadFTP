package controllers;

import hxbase.Dispatcher;
<hxLoop name="controller">
import controllers.{className};
</hxLoop>

/** This whole class is auto-generated from registercontrollers.n in tools. */
class ControllerRegistry
{
	public static function registerAll()
	{
	// For each controller, set the default path to the lowercase name.
	// And if the controller class has a field "aliases", add each of those too
	
	<hxLoop name="controller">
		// Registering controller {className}
		Dispatcher.registerController("{lowerCaseName}", {className}); 
		if (Lambda.has(Type.getClassFields({className}), "aliases"))
		{
			for (alias in {className}.aliases)
			{
				Dispatcher.registerController(alias, {className}); 
			}
		}
		// 
	</hxLoop>
	}
}
