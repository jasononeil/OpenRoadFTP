<?php

class jasononeil_xml_XmlList extends HList {
	public function __construct() { if( !php_Boot::$skip_constructor ) {
		parent::__construct();
	}}
	public function addList($newList) {
		$»it = $newList->iterator();
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
		$list2 = new jasononeil_xml_XmlList();
		$list2->addList(parent::filter($f));
		return $list2;
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
