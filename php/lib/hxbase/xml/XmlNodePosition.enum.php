<?php

class hxbase_xml_XmlNodePosition extends Enum {
		public static $CONTAINED_BY;
		public static $CONTAINS;
		public static $DISCONNECTED;
		public static $FOLLOWING;
		public static $PRECEDING;
	}
	hxbase_xml_XmlNodePosition::$CONTAINED_BY = new hxbase_xml_XmlNodePosition("CONTAINED_BY", 4);
	hxbase_xml_XmlNodePosition::$CONTAINS = new hxbase_xml_XmlNodePosition("CONTAINS", 3);
	hxbase_xml_XmlNodePosition::$DISCONNECTED = new hxbase_xml_XmlNodePosition("DISCONNECTED", 0);
	hxbase_xml_XmlNodePosition::$FOLLOWING = new hxbase_xml_XmlNodePosition("FOLLOWING", 2);
	hxbase_xml_XmlNodePosition::$PRECEDING = new hxbase_xml_XmlNodePosition("PRECEDING", 1);
