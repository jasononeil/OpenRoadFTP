<?php

class hxbase_LogType extends Enum {
		public static $AssertionFailed;
		public static $Error;
		public static $Info;
		public static $Trace;
		public static $Warning;
	}
	hxbase_LogType::$AssertionFailed = new hxbase_LogType("AssertionFailed", 4);
	hxbase_LogType::$Error = new hxbase_LogType("Error", 3);
	hxbase_LogType::$Info = new hxbase_LogType("Info", 1);
	hxbase_LogType::$Trace = new hxbase_LogType("Trace", 0);
	hxbase_LogType::$Warning = new hxbase_LogType("Warning", 2);
