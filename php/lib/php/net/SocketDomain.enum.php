<?php

class php_net_SocketDomain extends Enum {
		public static $AfInet;
		public static $AfInet6;
		public static $AfUnix;
	}
	php_net_SocketDomain::$AfInet = new php_net_SocketDomain("AfInet", 0);
	php_net_SocketDomain::$AfInet6 = new php_net_SocketDomain("AfInet6", 1);
	php_net_SocketDomain::$AfUnix = new php_net_SocketDomain("AfUnix", 2);
