package hxbase.session;

import php.Session;
import hxbase.util.Error;

class SessionHandler
{
	public var name(default,null):String;
	private var timeout:Int;
	private var isSessionOkay:Bool; 
	
	public function new(name_in:String, ?timeout_in:Int = 300)
	{
		name = name_in;
		timeout = timeout_in;
		
		registerErrorMessages();
		
		Session.setName(name);
		isSessionOkay = false;
		
	}
	
	
	function registerErrorMessages()
	{
		Error.registerErrorType("SESSION.TIMEOUT", "Session timed out.");
		Error.registerErrorType("SESSION.NO_SESSION", "No existing session found.");
	}
	
	public function check():Void
	{
		// this doesn't do a return value, instead it'll 
		// throw errors if something goes wrong.
		// note, an error may also be thrown in the checkSecurity() method
		if (Session.exists('SESSION.active'))
		{
			checkSecurity();
			
			// if session exists, and security checks out, we're all good.
			Session.regenerateId();
			set('SESSION.lastUsed', Date.now().getTime());
		}
		else
		{
			throw new Error("SESSION.NO_SESSION");
		}
	}
	
	public function start():SessionHandler
	{
		
		Session.start();
		Session.regenerateId();
		
		var agent:String = untyped __var__('_SERVER', 'HTTP_USER_AGENT');
		Session.set('SESSION.active', true);
		Session.set('SESSION.agent', agent);
		Session.set('SESSION.ip', php.Web.getClientIP());
		Session.set('SESSION.lastUsed', Date.now().getTime());
		
		// this first ID can be used as the basis for a unique user/session identifier
		Session.set('SESSION.firstID', Session.getId());
		
		isSessionOkay = true;
		
		return this;
	}
	
	public function end()
	{
		// These 2 functions don't work for me.  Using my own instead
		//php.Session.clear();
		//php.Session.close();
		
		var sessionName = name;
		if (untyped __php__("isset($_COOKIE[$sessionName])"))
		{			
			untyped __call__("setcookie", sessionName, '', (untyped __call__("time"))-3600, '/');
		}
		untyped __call__("session_write_close");
		untyped __php__("$_SESSION = array()");
		
		isSessionOkay = false;
	}
	
	public function set(name_in:String, value_in:Dynamic):SessionHandler
	{
		Session.set(name_in, value_in);
		return this;
	}
	
	public function get(name_in:String):Dynamic
	{
		var r:Dynamic = null;
		
		r = Session.get(name_in);
				
		return r;
	}
	
	private function checkSecurity()
	{
		// this is a private function, and I know it will only be called if the session exists
		// so no need to check that.
		
		var oldTime:Float = Session.get('SESSION.lastUsed');
		var oldAgent:String = Session.get('SESSION.agent');
		var oldIP:String = Session.get('SESSION.ip');
		
		var newTime:Float = Date.now().getTime();
		var newAgent:String = untyped __var__('_SERVER', 'HTTP_USER_AGENT');
		var newIP:String = php.Web.getClientIP();
		
		var isSecurityOkay:Bool = false;
		
		if (newTime - oldTime < 1000 * timeout)
		{
			if (newAgent == oldAgent)
			{
				if (newIP == oldIP)
				{
					isSecurityOkay = true;
				}
			}
		}
		else
		{
			throw new Error("SESSION.TIMEOUT");
		}
		
		return isSecurityOkay;
	}
}
