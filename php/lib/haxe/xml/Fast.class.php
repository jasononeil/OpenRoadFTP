<?php

class haxe_xml_Fast {
	public function __construct($x) {
		if(!php_Boot::$skip_constructor) {
		if((is_object($_t = $x->nodeType) && !($_t instanceof Enum) ? $_t !== Xml::$Document : $_t != Xml::$Document) && (is_object($_t2 = $x->nodeType) && !($_t2 instanceof Enum) ? $_t2 !== Xml::$Element : $_t2 != Xml::$Element)) {
			throw new HException("Invalid nodeType " . Std::string($x->nodeType));
		}
		$this->x = $x;
		$this->node = new haxe_xml__Fast_NodeAccess($x);
		$this->nodes = new haxe_xml__Fast_NodeListAccess($x);
		$this->att = new haxe_xml__Fast_AttribAccess($x);
		$this->has = new haxe_xml__Fast_HasAttribAccess($x);
		$this->hasNode = new haxe_xml__Fast_HasNodeAccess($x);
	}}
	public $x;
	public $node;
	public $nodes;
	public $att;
	public $has;
	public $hasNode;
	public function get_innerHTML() {
		$s = new StringBuf();
		if(null == $this->x) throw new HException('null iterable');
		$__hx__it = $this->x->iterator();
		while($__hx__it->hasNext()) {
			$x = $__hx__it->next();
			$s->add($x->toString());
		}
		return $s->b;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static $__properties__ = array("get_innerHTML" => "get_innerHTML");
	function __toString() { return 'haxe.xml.Fast'; }
}
