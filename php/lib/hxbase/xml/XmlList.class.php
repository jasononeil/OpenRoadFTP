<?php

class hxbase_xml_XmlList extends HList {
	public function __construct($node, $list) { if( !php_Boot::$skip_constructor ) {
		parent::__construct();
		if($node !== null) {
			$this->add($node);
		}
		if($list !== null) {
			$»it = $list->iterator();
			while($»it->hasNext()) {
			$n = $»it->next();
			{
				$this->add($n);
				;
			}
			}
		}
	}}
	public function addList($listToAdd) {
		$»it = $listToAdd->iterator();
		while($»it->hasNext()) {
		$item = $»it->next();
		{
			$this->add($item);
			;
		}
		}
		return $this;
	}
	public function filterByFunction($f) {
		$list2 = null;
		$list2 = new hxbase_xml_XmlList(null, null);
		$list2->addList(parent::filter($f));
		return $list2;
	}
	public function filterByAttribute($attName, $attValue) {
		return $this->filterByFunction(array(new _hx_lambda(array("attName" => &$attName, "attValue" => &$attValue), null, array('n'), "{
			return (\$n->testIsElement() && \$n->hasAtt(\$attName) && \$n->getAtt(\$attName) == \$attValue);
		}"), 'execute1'));
	}
	public function filterByTagName($tagName) {
		return $this->filterByFunction(array(new _hx_lambda(array("tagName" => &$tagName), null, array('n'), "{
			return (\$n->testIsElement() && \$n->getter_name() == \$tagName);
		}"), 'execute1'));
	}
	public function getAt($index) {
		$iter = null;
		$xml = null;
		$i = null;
		$iter = $this->iterator();
		$xml = null;
		$i = 1;
		while($i <= $index && $iter->hasNext()) {
			if($i === $index) {
				$xml = $iter->next();
			}
			else {
				$iter->next();
			}
			$i++;
			;
		}
		return $xml;
	}
	public function indexOf($childToSearchFor) {
		$iter = null;
		$foundItem = null;
		$itemIndex = null;
		$i = null;
		$i = 0;
		$itemIndex = 0;
		$foundItem = false;
		$iter = $this->iterator();
		while($itemIndex === 0 && $iter->hasNext()) {
			$i++;
			$currentChild = $iter->next();
			if($currentChild->getter_xml() === $childToSearchFor->getter_xml()) { $itemIndex = $i; }
			unset($currentChild);
		}
		return $itemIndex;
	}
	public function toString() {
		$s = new StringBuf();
		$»it = $this->iterator();
		while($»it->hasNext()) {
		$child = $»it->next();
		$s->b .= $child->toString();
		}
		return $s->b;
	}
	function __toString() { return $this->toString(); }
}
