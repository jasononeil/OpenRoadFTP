package hxbase.util;
class Error
{
	public function new(str:String, ?pos:haxe.PosInfos)
	{
		code = "ERROR";
		error = str;
		explanation = "This is an error that hasn't been explained properly yet.";
		suggestion = "You should try bribing Jason to fix it.";
		this.pos = pos;
		if (errorTypes.exists(str))
		{
			var type = errorTypes.get(str);
			code = str;
			error = type.error;
			explanation = type.explanation;
			suggestion = type.suggestion;
		}
	}
	
	public var code:String;
	public var error:String;
	public var explanation:String;
	public var suggestion:String;
	public var pos:haxe.PosInfos;
	
	public function toString()
	{
		return code + ": " + error + "\n\n" + explanation + "\n\n" + suggestion;
	}
	
	static var errorTypes:Map<String, Dynamic> = new Map();
	public static function registerErrorType(code_in,?error_in,?explanation_in,?suggestion_in)
	{
		if (suggestion_in == null) suggestion_in = "";
		if (explanation_in == null) explanation_in = "";
		var type = {
			code: code_in,
			error: error_in,
			explanation: explanation_in,
			suggestion: suggestion_in
		}
		errorTypes.set(code_in,type);
	}
}
