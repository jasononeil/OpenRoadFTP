<?php

interface haxe_remoting_Connection {
	function resolve($name);
	function call($params);
}
