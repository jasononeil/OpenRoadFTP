package hxbase;

class Log
{
	/** This is not made to be called directly.  
	Use trace, info, warning or error instead. */
	static public function log (type:LogType, msg:String, pos:haxe.PosInfos)
	{
		var str:String;
		
		str = "<b>" + type + "</b> ";
		str += "[" + pos.className + "." + pos.methodName + "() ";
		str += pos.lineNumber + "] : ";
		str += msg;
		
		php.Lib.print("<br>" + str);
	}
	
	static public function trace(v:Dynamic, ?pos:haxe.PosInfos)
	{
		log(LogType.Trace, v, pos);
	}
	
	static public function info(info:String, ?pos:haxe.PosInfos)
	{
		log(LogType.Info, info, pos);
	}
	
	static public function warning(warning:String, ?pos:haxe.PosInfos)
	{
		log(LogType.Warning, warning, pos);
	}
	
	static public function error(error:String, ?pos:haxe.PosInfos)
	{
		log(LogType.Error, error, pos);
		throw (error);
	}
	
	static public function assert(cond:Bool, ?desc:String = "", ?pos : haxe.PosInfos)
	{
		if (!cond)
		{
			log(LogType.AssertionFailed, desc, pos);
		}
	}
	
}

enum LogType
{
	Trace;
	Info;
	Warning;
	Error;
	AssertionFailed;
}
