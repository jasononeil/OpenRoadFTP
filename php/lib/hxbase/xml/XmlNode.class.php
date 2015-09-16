<?php

class hxbase_xml_XmlNode {
	public function __construct($str_in = null, $xml_in = null, $advXmlItem_in = null) {
		if(!php_Boot::$skip_constructor) {
		$this->x = null;
		$this->f = null;
		if($str_in !== null && $xml_in === null && $advXmlItem_in === null) {
			$this->set_outerXML($str_in);
		}
		if($xml_in !== null && $advXmlItem_in === null) {
			$this->x = $xml_in;
		}
		if($advXmlItem_in !== null) {
			$this->x = $advXmlItem_in->copy()->get_xml();
		}
		$this->clearWhitespace();
	}}
	public $xml;
	public $fast;
	public $type;
	public $isCData;
	public $isPCData;
	public $isDocType;
	public $isDocument;
	public $isComment;
	public $isElement;
	public $isText;
	public $index;
	public $numChildren;
	public $parent;
	public $document;
	public $depth;
	public $children;
	public $firstChild;
	public $lastChild;
	public $ancestors;
	public $descendants;
	public $siblings;
	public $next;
	public $prev;
	public $siblingsBefore;
	public $siblingsAfter;
	public $x;
	public $f;
	public function copy() {
		$parsedObj = null;
		$returnObj = null;
		$parsedObj = new hxbase_xml_XmlNode($this->get_outerXML(), null, null);
		if($this->get_isDocument()) {
			$returnObj = $parsedObj;
		} else {
			$returnObj = $parsedObj->get_firstChild();
		}
		hxbase_Log::assert((is_object($_t = $this->get_type()) && !($_t instanceof Enum) ? $_t === $returnObj->get_type() : $_t == $returnObj->get_type()), "The duplicate object doesn't have the same type, when it should.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 416, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		hxbase_Log::assert($this->get_numChildren() === $returnObj->get_numChildren(), "The duplicate object has less children than the original.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 417, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		hxbase_Log::assert($this->get_name() === $returnObj->get_name(), "The duplicate object has a different name property to the original.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 418, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		return $returnObj;
	}
	public function addChildAt($child, $index = null) {
		if($index === null) {
			$index = 0;
		}
		if($child->get_isDocument()) {
			$indexOffset = null;
			$indexOffset = 0;
			if(null == $child->get_children()) throw new HException('null iterable');
			$__hx__it = $child->get_children()->iterator();
			while($__hx__it->hasNext()) {
				$documentChild = $__hx__it->next();
				$this->get_xml()->insertChild($documentChild->get_xml(), $index + $indexOffset);
				$indexOffset++;
			}
		} else {
			$this->get_xml()->insertChild($child->get_xml(), $index);
		}
		return $this;
	}
	public function appendChild($child) {
		$this->addChildAt($child, $this->get_numChildren());
		return $this;
	}
	public function prependChild($child) {
		$this->addChildAt($child, 0);
		return $this;
	}
	public function addChildBefore($newChild, $existingChild) {
		$this->addChildAt($newChild, $existingChild->get_index() - 1);
		return $this;
	}
	public function addChildAfter($newChild, $existingChild) {
		$this->addChildAt($newChild, $existingChild->get_index());
		return $this;
	}
	public function addThisTo($newParent, $pos = null) {
		if($pos === null) {
			$pos = -1;
		}
		if($pos === -1) {
			$pos = $newParent->get_numChildren();
		}
		if($newParent->get_isElement() || $newParent->get_isDocument()) {
			$newParent->addChildAt($this, $pos);
		}
		return $this;
	}
	public function removeChild($child) {
		$this->get_xml()->removeChild($child->get_xml());
		return $this;
	}
	public function removeChildAt($pos) {
		$this->removeChild($this->getChildAt($pos));
		return $this;
	}
	public function removeChildren($children) {
		if(null == $children) throw new HException('null iterable');
		$__hx__it = $children->iterator();
		while($__hx__it->hasNext()) {
			$child = $__hx__it->next();
			$this->removeChild($child);
		}
		return $this;
	}
	public function clearWhitespace() {
		$numChildrenRemoved = null;
		$numChildrenRemoved = 0;
		if(null == $this->get_children()) throw new HException('null iterable');
		$__hx__it = $this->get_children()->iterator();
		while($__hx__it->hasNext()) {
			$child = $__hx__it->next();
			if($child->get_isPCData() || $child->get_isText()) {
				$isWhitespaceOnly = new EReg("^\\s+\$", "");
				$str = $child->get_value();
				if($isWhitespaceOnly->match($str)) {
					$this->removeChild($child);
					$numChildrenRemoved++;
				} else {
					$whitespaceAtFront = new EReg("^\\s+", "");
					$whitespaceAtBack = new EReg("\\s+\$", "");
					$child->set_value($whitespaceAtFront->replace($str, ""));
					$child->set_value($whitespaceAtFront->replace($str, ""));
					unset($whitespaceAtFront,$whitespaceAtBack);
				}
				unset($str,$isWhitespaceOnly);
			}
		}
		return $numChildrenRemoved;
	}
	public function hempty() {
		$child = null;
		while($this->get_xml()->firstChild() !== null) {
			$this->get_xml()->removeChild($this->get_xml()->firstChild());
		}
		return $this;
	}
	public function hasAtt($attName) {
		return $this->get_xml()->exists($attName);
	}
	public function getAtt($attName) {
		$value = null;
		$value = "";
		return $this->get_xml()->get($attName);
	}
	public function setAtt($attName, $attValue) {
		$this->get_xml()->set($attName, $attValue);
		return $this;
	}
	public function getAttList() {
		$attIterator = null;
		$list = null;
		$list = new HList();
		$attIterator = $this->get_xml()->attributes();
		while($attIterator->hasNext()) {
			$list->add($attIterator->next());
		}
		return $list;
	}
	public function getParent() {
		$parent = null;
		$parent = null;
		if(hxbase_xml_XmlNode_0($this, $parent) !== null) {
			$parent = new hxbase_xml_XmlNode(null, hxbase_xml_XmlNode_1($this, $parent), null);
		}
		return $parent;
	}
	public function getDocument() {
		$elementToTest = $this->get_xml();
		while($elementToTest->_parent !== null && (is_object($_t = $elementToTest->nodeType) && !($_t instanceof Enum) ? $_t !== Xml::$Document : $_t != Xml::$Document)) {
			$elementToTest = $elementToTest->_parent;
		}
		$finalResult = null;
		if((is_object($_t = $elementToTest->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$Document : $_t == Xml::$Document)) {
			$finalResult = $elementToTest;
		} else {
			$finalResult = null;
		}
		return new hxbase_xml_XmlNode(null, $finalResult, null);
	}
	public function getDepthFromDocument() {
		$elementToTest = $this->get_xml();
		$levelsDown = 0;
		while($elementToTest->_parent !== null && (is_object($_t = $elementToTest->nodeType) && !($_t instanceof Enum) ? $_t !== Xml::$Document : $_t != Xml::$Document)) {
			$elementToTest = $elementToTest->_parent;
			$levelsDown++;
		}
		return $levelsDown;
	}
	public function getNthParent($n) {
		return $this->get_ancestors()->getAt($n);
	}
	public function getAncestors($nameFilter = null, $limit = null) {
		$currentElm = null;
		$childList = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$currentElm = $this;
		while($currentElm->get_parent() !== null) {
			$currentElm = $currentElm->get_parent();
			$childList->add($currentElm);
		}
		return $childList;
	}
	public function getChildren($nameFilter = null, $limit = null) {
		$childList = null;
		$childList = new hxbase_xml_XmlList(null, null);
		if($this->get_isElement() || $this->get_isDocument()) {
			if(null == $this->get_xml()) throw new HException('null iterable');
			$__hx__it = $this->get_xml()->iterator();
			while($__hx__it->hasNext()) {
				$childXml = $__hx__it->next();
				$child = new hxbase_xml_XmlNode(null, $childXml, null);
				$childList->add($child);
				$name = null;
				if($child->get_isElement()) {
					$name = $child->get_name();
				} else {
					$name = "";
				}
				unset($name,$child);
			}
		}
		return $childList;
	}
	public function getChildAt($index) {
		return $this->get_children()->getAt($index);
	}
	public function getFirstChild() {
		return new hxbase_xml_XmlNode(null, $this->get_xml()->firstChild(), null);
	}
	public function getLastChild() {
		return $this->get_children()->getAt($this->get_numChildren());
	}
	public function getDescendants() {
		$descendantList = new hxbase_xml_XmlList(null, null);
		$descendantList->add($this);
		if($this->get_isElement() || $this->get_isDocument()) {
			if(null == $this->getChildren(null, null)) throw new HException('null iterable');
			$__hx__it = $this->getChildren(null, null)->iterator();
			while($__hx__it->hasNext()) {
				$child = $__hx__it->next();
				$descendantList->addList($child->getDescendants());
			}
		}
		return $descendantList;
	}
	public function getSiblings() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->get_parent();
		if($thisParent !== null) {
			$childList->addList($this->getSiblingsBefore());
			$childList->addList($this->getSiblingsAfter());
		}
		return $childList;
	}
	public function getNext() {
		$prevElm = null;
		$prevElm = null;
		if($this->get_parent() !== null) {
			$prevElm = $this->get_parent()->getChildAt($this->get_index() + 1);
		}
		return $prevElm;
	}
	public function getPrev() {
		$prevElm = null;
		$prevElm = null;
		if($this->get_parent() !== null) {
			$prevElm = $this->get_parent()->getChildAt($this->get_index() - 1);
		}
		return $prevElm;
	}
	public function getSiblingsBefore() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->get_parent();
		if($thisParent !== null) {
			$_g1 = 1;
			$_g = $this->get_index();
			while($_g1 < $_g) {
				$i = $_g1++;
				$childList->add($thisParent->getChildAt($i));
				unset($i);
			}
		}
		return $childList;
	}
	public function getSiblingsAfter() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->get_parent();
		if($thisParent !== null) {
			$_g1 = $this->get_index() + 1;
			$_g = $this->get_parent()->get_numChildren() + 1;
			while($_g1 < $_g) {
				$i = $_g1++;
				$childList->add($thisParent->getChildAt($i));
				unset($i);
			}
		}
		return $childList;
	}
	public function getElementsById($id_in) {
		return $this->getDescendants()->filterByAttribute("id", $id_in);
	}
	public function getElementsByTagName($name_in) {
		return $this->getDescendants()->filterByTagName($name_in);
	}
	public function compareDocumentPosition($otherNode) {
		$positionOfOtherNode = null;
		$positionOfOtherNode = hxbase_xml_XmlNodePosition::$DISCONNECTED;
		if((is_object($_t = hxbase_xml_XmlNode_2($this, $_t, $otherNode, $positionOfOtherNode)) && !($_t instanceof Enum) ? $_t === hxbase_xml_XmlNode_3($this, $_t, $otherNode, $positionOfOtherNode) : $_t == hxbase_xml_XmlNode_4($this, $_t, $otherNode, $positionOfOtherNode))) {
			if($otherNode->get_index() > $this->get_index()) {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$FOLLOWING;
			} else {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$PRECEDING;
			}
		} else {
			if($this->isDescendantOf($otherNode)) {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$CONTAINS;
			} else {
				if($otherNode->isDescendantOf($this)) {
					$positionOfOtherNode = hxbase_xml_XmlNodePosition::$CONTAINED_BY;
				} else {
					if((is_object($_t2 = $this->get_document()->get_xml()) && !($_t2 instanceof Enum) ? $_t2 === $otherNode->get_document()->get_xml() : $_t2 == $otherNode->get_document()->get_xml())) {
						$isOtherNodeDeeperThanThis = null;
						$thisDepth = $this->get_depth();
						$otherDepth = $otherNode->get_depth();
						$isOtherNodeDeeperThanThis = $otherDepth > $thisDepth;
						if($isOtherNodeDeeperThanThis) {
							$diff = $otherDepth - $thisDepth;
							$otherAncestorOfCommonDepth = $otherNode->getNthParent($diff);
							$positionOfOtherNode = $this->compareDocumentPosition($otherAncestorOfCommonDepth);
						} else {
							$diff1 = $thisDepth - $otherDepth;
							$thisAncestorOfCommonDepth = $this->getNthParent($diff1);
							$positionOfOtherNode = $thisAncestorOfCommonDepth->compareDocumentPosition($otherNode);
						}
					}
				}
			}
		}
		return $positionOfOtherNode;
	}
	public function isDescendantOf($otherNode) {
		$nodeToTest = null;
		{
			$_this = $this->get_xml();
			$nodeToTest = $_this->_parent;
		}
		$possibleAncestor = $otherNode->get_xml();
		$isDescendant = false;
		while(!$isDescendant && $nodeToTest->_parent !== null) {
			$isDescendant = $nodeToTest === $possibleAncestor;
			$nodeToTest = $nodeToTest->_parent;
		}
		return $isDescendant;
	}
	public function setInnerXML($str) {
		$this->set_innerXML($str);
		return $this;
	}
	public function setOuterXML($str) {
		$this->set_outerXML($str);
		return $this;
	}
	public function toString() {
		return $this->get_outerXML();
	}
	public function iterator() {
		return $this->getChildren(null, null)->iterator();
	}
	public function get_xml() {
		if($this->x !== null) {
			$this->x = $this->x;
		} else {
			$this->x = Xml::parse("<empty />");
		}
		return $this->x;
	}
	public function get_fast() {
		if($this->f !== null) {
			$this->f = $this->f;
		} else {
			$this->f = new haxe_xml_Fast($this->get_xml());
		}
		return $this->f;
	}
	public function get_name() {
		if($this->get_isElement()) {
			return $this->get_xml()->get_nodeName();
		} else {
			return "#" . Std::string($this->get_type());
		}
	}
	public function set_name($newName) {
		$this->get_xml()->set_nodeName($newName);
		return $this->get_xml()->get_nodeName();
	}
	public function get_type() {
		return $this->get_xml()->nodeType;
	}
	public function get_isCData() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$CData : $_t == Xml::$CData);
	}
	public function get_isComment() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$Comment : $_t == Xml::$Comment);
	}
	public function get_isDocType() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$DocType : $_t == Xml::$DocType);
	}
	public function get_isDocument() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$Document : $_t == Xml::$Document);
	}
	public function get_isElement() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$Element : $_t == Xml::$Element);
	}
	public function get_isPCData() {
		return (is_object($_t = $this->get_xml()->nodeType) && !($_t instanceof Enum) ? $_t === Xml::$PCData : $_t == Xml::$PCData);
	}
	public function get_isText() {
		return $this->get_isCData() || $this->get_isPCData();
	}
	public function get_value() {
		$v = null;
		if($this->get_isElement() || $this->get_isDocument()) {
			$v = "";
		} else {
			$v = $this->get_xml()->get_nodeValue();
		}
		return $v;
	}
	public function set_value($v) {
		$returnVal = "";
		if(!$this->get_isElement() && !$this->get_isDocument()) {
			$this->get_xml()->set_nodeValue($v);
			$returnVal = $v;
		}
		return $returnVal;
	}
	public function get_parent() {
		return $this->getParent();
	}
	public function get_document() {
		return $this->getDocument();
	}
	public function get_depth() {
		return $this->getDepthFromDocument();
	}
	public function get_children() {
		return $this->getChildren(null, null);
	}
	public function get_firstChild() {
		return $this->getFirstChild();
	}
	public function get_lastChild() {
		return $this->getLastChild();
	}
	public function get_ancestors() {
		return $this->getAncestors(null, null);
	}
	public function get_descendants() {
		return $this->getDescendants();
	}
	public function get_siblings() {
		return $this->getSiblings();
	}
	public function get_next() {
		return $this->getNext();
	}
	public function get_prev() {
		return $this->getPrev();
	}
	public function get_siblingsBefore() {
		return $this->getSiblingsBefore();
	}
	public function get_siblingsAfter() {
		return $this->getSiblingsAfter();
	}
	public function get_index() {
		$index = null;
		$parent = null;
		$index = 0;
		if($this->get_parent() !== null) {
			$index = $this->get_parent()->getChildren(null, null)->indexOf($this);
		}
		return $index;
	}
	public function get_numChildren() {
		return Lambda::count($this->get_xml(), null);
	}
	public function get_innerXML() {
		return $this->get_fast()->get_innerHTML();
	}
	public function set_innerText($str) {
		return $this->set_innerXML($str);
	}
	public function set_innerXML($str) {
		$newXml = null;
		$this->hempty();
		$newXml = new hxbase_xml_XmlNode($str, null, null);
		$this->addChildAt($newXml, 0);
		return $str;
	}
	public function get_outerXML() {
		return $this->get_xml()->toString();
	}
	public function set_outerXML($str) {
		if($this->get_parent() === null) {
			$this->x = Xml::parse($str);
		} else {
			$newXml = null;
			$parent = null;
			$index = null;
			$parent = $this->get_parent();
			$index = $this->get_index();
			$newXml = new hxbase_xml_XmlNode($str, null, null);
			$newXml->addThisTo($parent, $index);
			$parent->removeChild($this);
		}
		return $str;
	}
	public function get_innerText() {
		$allDescendants = null;
		$textDescendants = null;
		$s = null;
		$allDescendants = $this->get_descendants();
		$textDescendants = $allDescendants->filterByFunction(array(new _hx_lambda(array(&$allDescendants, &$s, &$textDescendants), "hxbase_xml_XmlNode_5"), 'execute'));
		$s1 = new StringBuf();
		if(null == $textDescendants) throw new HException('null iterable');
		$__hx__it = $textDescendants->iterator();
		while($__hx__it->hasNext()) {
			$textNode = $__hx__it->next();
			$s1->add($textNode->toString());
		}
		return $textDescendants->join(" ");
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
	static $__properties__ = array("set_innerText" => "set_innerText","get_innerText" => "get_innerText","set_outerXML" => "set_outerXML","get_outerXML" => "get_outerXML","set_innerXML" => "set_innerXML","get_innerXML" => "get_innerXML","get_siblingsAfter" => "get_siblingsAfter","get_siblingsBefore" => "get_siblingsBefore","get_prev" => "get_prev","get_next" => "get_next","get_siblings" => "get_siblings","get_descendants" => "get_descendants","get_ancestors" => "get_ancestors","get_lastChild" => "get_lastChild","get_firstChild" => "get_firstChild","get_children" => "get_children","get_depth" => "get_depth","get_document" => "get_document","get_parent" => "get_parent","get_numChildren" => "get_numChildren","get_index" => "get_index","get_isText" => "get_isText","get_isElement" => "get_isElement","get_isComment" => "get_isComment","get_isDocument" => "get_isDocument","get_isDocType" => "get_isDocType","get_isPCData" => "get_isPCData","get_isCData" => "get_isCData","get_type" => "get_type","set_value" => "set_value","get_value" => "get_value","set_name" => "set_name","get_name" => "get_name","get_fast" => "get_fast","get_xml" => "get_xml");
	function __toString() { return $this->toString(); }
}
function hxbase_xml_XmlNode_0(&$__hx__this, &$parent) {
	{
		$_this = $__hx__this->get_xml();
		return $_this->_parent;
	}
}
function hxbase_xml_XmlNode_1(&$__hx__this, &$parent) {
	{
		$_this1 = $__hx__this->get_xml();
		return $_this1->_parent;
	}
}
function hxbase_xml_XmlNode_2(&$__hx__this, &$_t, &$otherNode, &$positionOfOtherNode) {
	{
		$_this = $__hx__this->get_xml();
		return $_this->_parent;
	}
}
function hxbase_xml_XmlNode_3(&$__hx__this, &$_t, &$otherNode, &$positionOfOtherNode) {
	{
		$_this1 = $otherNode->get_xml();
		return $_this1->_parent;
	}
}
function hxbase_xml_XmlNode_4(&$__hx__this, &$_t, &$otherNode, &$positionOfOtherNode) {
	{
		$_this1 = $otherNode->get_xml();
		return $_this1->_parent;
	}
}
function hxbase_xml_XmlNode_5(&$allDescendants, &$s, &$textDescendants, $x) {
	{
		return $x->get_isText() || $x->get_isPCData();
	}
}
