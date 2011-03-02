<?php

class hxbase_xml_XmlNode {
	public function __construct($str_in, $xml_in, $advXmlItem_in) {
		if( !php_Boot::$skip_constructor ) {
		$this->x = null;
		$this->f = null;
		if($str_in !== null && $xml_in === null && $advXmlItem_in === null) {
			$this->setter_outerXml($str_in);
		}
		if($xml_in !== null && $advXmlItem_in === null) {
			$this->x = $xml_in;
		}
		if($advXmlItem_in !== null) {
			$this->x = $advXmlItem_in->copy()->getter_xml();
		}
		$this->clearWhitespace();
	}}
	public $xml;
	public $fast;
	//;
	//;
	public $type;
	public $isCData;
	public $isPCData;
	public $isProlog;
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
	//;
	//;
	//;
	public $x;
	public $f;
	public function copy() {
		$parsedObj = null;
		$returnObj = null;
		$parsedObj = new hxbase_xml_XmlNode($this->getter_outerXml(), null, null);
		$returnObj = (($this->testIsDocument()) ? $parsedObj : $parsedObj->getter_firstChild());
		hxbase_Log::assert($this->getter_type() == $returnObj->getter_type(), "The duplicate object doesn't have the same type, when it should.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 416, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		hxbase_Log::assert($this->getter_numChildren() === $returnObj->getter_numChildren(), "The duplicate object has less children than the original.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 417, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		hxbase_Log::assert($this->getter_name() == $returnObj->getter_name(), "The duplicate object has a different name property to the original.", _hx_anonymous(array("fileName" => "XmlNode.hx", "lineNumber" => 418, "className" => "hxbase.xml.XmlNode", "methodName" => "copy")));
		return $returnObj;
	}
	public function addChildAt($child, $index) {
		if($index === null) {
			$index = 0;
		}
		if($child->testIsDocument()) {
			$indexOffset = null;
			$indexOffset = 0;
			$»it = $child->getter_children()->iterator();
			while($»it->hasNext()) {
			$documentChild = $»it->next();
			{
				$this->getter_xml()->insertChild($documentChild->getter_xml(), ($index + $indexOffset));
				$indexOffset++;
				;
			}
			}
		}
		else {
			$this->getter_xml()->insertChild($child->getter_xml(), $index);
		}
		return $this;
	}
	public function appendChild($child) {
		$this->addChildAt($child, $this->getter_numChildren());
		return $this;
	}
	public function prependChild($child) {
		$this->addChildAt($child, 0);
		return $this;
	}
	public function addChildBefore($newChild, $existingChild) {
		$this->addChildAt($newChild, $existingChild->getter_index() - 1);
		return $this;
	}
	public function addChildAfter($newChild, $existingChild) {
		$this->addChildAt($newChild, $existingChild->getter_index());
		return $this;
	}
	public function addThisTo($newParent, $pos) {
		if($pos === null) {
			$pos = -1;
		}
		if($pos === -1) {
			$pos = $newParent->getter_numChildren();
		}
		if($newParent->testIsElement() || $newParent->testIsDocument()) {
			$newParent->addChildAt($this, $pos);
		}
		return $this;
	}
	public function removeChild($child) {
		$this->getter_xml()->removeChild($child->getter_xml());
		return $this;
	}
	public function removeChildAt($pos) {
		$this->removeChild($this->getChildAt($pos));
		return $this;
	}
	public function removeChildren($children) {
		$»it = $children->iterator();
		while($»it->hasNext()) {
		$child = $»it->next();
		{
			$this->removeChild($child);
			;
		}
		}
		return $this;
	}
	public function clearWhitespace() {
		$numChildrenRemoved = null;
		$numChildrenRemoved = 0;
		$»it = $this->getter_children()->iterator();
		while($»it->hasNext()) {
		$child = $»it->next();
		{
			if($child->testIsPCData() || $child->testIsText()) {
				$isWhitespaceOnly = new EReg("^\\s+\$", "");
				$str = $child->getter_value();
				if($isWhitespaceOnly->match($str)) {
					$this->removeChild($child);
					$numChildrenRemoved++;
				}
				else {
					$whitespaceAtFront = new EReg("^\\s+", "");
					$whitespaceAtBack = new EReg("\\s+\$", "");
					$child->setter_value($whitespaceAtFront->replace($str, ""));
					$child->setter_value($whitespaceAtFront->replace($str, ""));
				}
			}
			unset($whitespaceAtFront,$whitespaceAtBack,$str,$isWhitespaceOnly);
		}
		}
		return $numChildrenRemoved;
	}
	public function hempty() {
		$child = null;
		while($this->getter_xml()->firstChild() !== null) {
			$this->getter_xml()->removeChild($this->getter_xml()->firstChild());
			;
		}
		return $this;
	}
	public function hasAtt($attName) {
		return $this->getter_xml()->exists($attName);
	}
	public function getAtt($attName) {
		$value = null;
		$value = "";
		return $this->getter_xml()->get($attName);
	}
	public function setAtt($attName, $attValue) {
		$this->getter_xml()->set($attName, $attValue);
		return $this;
	}
	public function getAttList() {
		$attIterator = null;
		$list = null;
		$list = new HList();
		$attIterator = $this->getter_xml()->attributes();
		while($attIterator->hasNext()) {
			$list->add($attIterator->next());
			;
		}
		return $list;
	}
	public function getParent() {
		$parent = null;
		$parent = null;
		if($this->getter_xml()->getParent() !== null) {
			$parent = new hxbase_xml_XmlNode(null, $this->getter_xml()->getParent(), null);
		}
		return $parent;
	}
	public function getDocument() {
		$elementToTest = $this->getter_xml();
		while($elementToTest->getParent() !== null && $elementToTest->nodeType != Xml::$Document) {
			$elementToTest = $elementToTest->getParent();
			;
		}
		$finalResult = (($elementToTest->nodeType == Xml::$Document) ? $elementToTest : null);
		return new hxbase_xml_XmlNode(null, $finalResult, null);
	}
	public function getDepthFromDocument() {
		$elementToTest = $this->getter_xml();
		$levelsDown = 0;
		while($elementToTest->getParent() !== null && $elementToTest->nodeType != Xml::$Document) {
			$elementToTest = $elementToTest->getParent();
			$levelsDown++;
			;
		}
		return $levelsDown;
	}
	public function getNthParent($n) {
		return $this->getter_ancestors()->getAt($n);
	}
	public function getAncestors($nameFilter, $limit) {
		$currentElm = null;
		$childList = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$currentElm = $this;
		while($currentElm->getter_parent() !== null) {
			$currentElm = $currentElm->getter_parent();
			$childList->add($currentElm);
			;
		}
		return $childList;
	}
	public function getChildren($nameFilter, $limit) {
		$childList = null;
		$childList = new hxbase_xml_XmlList(null, null);
		if($this->testIsElement() || $this->testIsDocument()) {
			$»it = $this->getter_xml()->iterator();
			while($»it->hasNext()) {
			$childXml = $»it->next();
			{
				$child = new hxbase_xml_XmlNode(null, $childXml, null);
				$childList->add($child);
				$name = (($child->testIsElement()) ? $child->getter_name() : "");
				unset($name,$child);
			}
			}
		}
		return $childList;
	}
	public function getChildAt($index) {
		return $this->getter_children()->getAt($index);
	}
	public function getFirstChild() {
		return new hxbase_xml_XmlNode(null, $this->getter_xml()->firstChild(), null);
	}
	public function getLastChild() {
		return $this->getter_children()->getAt($this->getter_numChildren());
	}
	public function getDescendants() {
		$descendantList = new hxbase_xml_XmlList(null, null);
		$descendantList->add($this);
		if($this->testIsElement() || $this->testIsDocument()) {
			$»it = $this->getChildren(null, null)->iterator();
			while($»it->hasNext()) {
			$child = $»it->next();
			{
				$descendantList->addList($child->getDescendants());
				;
			}
			}
		}
		return $descendantList;
	}
	public function getSiblings() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->getter_parent();
		if($thisParent !== null) {
			$childList->addList($this->getSiblingsBefore());
			$childList->addList($this->getSiblingsAfter());
		}
		return $childList;
	}
	public function getNext() {
		$prevElm = null;
		$prevElm = null;
		if($this->getter_parent() !== null) {
			$prevElm = $this->getter_parent()->getChildAt($this->getter_index() + 1);
		}
		return $prevElm;
	}
	public function getPrev() {
		$prevElm = null;
		$prevElm = null;
		if($this->getter_parent() !== null) {
			$prevElm = $this->getter_parent()->getChildAt($this->getter_index() - 1);
		}
		return $prevElm;
	}
	public function getSiblingsBefore() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->getter_parent();
		if($thisParent !== null) {
			{
				$_g1 = 1; $_g = ($this->getter_index());
				while($_g1 < $_g) {
					$i = $_g1++;
					$childList->add($thisParent->getChildAt($i));
					unset($i);
				}
			}
		}
		return $childList;
	}
	public function getSiblingsAfter() {
		$childList = null;
		$thisParent = null;
		$childList = new hxbase_xml_XmlList(null, null);
		$thisParent = $this->getter_parent();
		if($thisParent !== null) {
			{
				$_g1 = ($this->getter_index() + 1); $_g = ($this->getter_parent()->getter_numChildren() + 1);
				while($_g1 < $_g) {
					$i = $_g1++;
					$childList->add($thisParent->getChildAt($i));
					unset($i);
				}
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
		if($this->getter_xml()->getParent() == $otherNode->getter_xml()->getParent()) {
			if($otherNode->getter_index() > $this->getter_index()) {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$FOLLOWING;
			}
			else {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$PRECEDING;
			}
		}
		else {
			if($this->isDescendantOf($otherNode)) {
				$positionOfOtherNode = hxbase_xml_XmlNodePosition::$CONTAINS;
			}
			else {
				if($otherNode->isDescendantOf($this)) {
					$positionOfOtherNode = hxbase_xml_XmlNodePosition::$CONTAINED_BY;
				}
				else {
					if($this->getter_document()->getter_xml() == $otherNode->getter_document()->getter_xml()) {
						$isOtherNodeDeeperThanThis = null;
						$thisDepth = $this->getter_depth();
						$otherDepth = $otherNode->getter_depth();
						$isOtherNodeDeeperThanThis = ($otherDepth > $thisDepth);
						if($isOtherNodeDeeperThanThis) {
							$diff = $otherDepth - $thisDepth;
							$otherAncestorOfCommonDepth = $otherNode->getNthParent($diff);
							$positionOfOtherNode = $this->compareDocumentPosition($otherAncestorOfCommonDepth);
						}
						else {
							$diff2 = $thisDepth - $otherDepth;
							$thisAncestorOfCommonDepth = $this->getNthParent($diff2);
							$positionOfOtherNode = $thisAncestorOfCommonDepth->compareDocumentPosition($otherNode);
						}
					}
				}
			}
		}
		return $positionOfOtherNode;
	}
	public function isDescendantOf($otherNode) {
		$nodeToTest = $this->getter_xml()->getParent();
		$possibleAncestor = $otherNode->getter_xml();
		$isDescendant = false;
		while(!$isDescendant && $nodeToTest->getParent() !== null) {
			$isDescendant = ($nodeToTest === $possibleAncestor);
			$nodeToTest = $nodeToTest->getParent();
			;
		}
		return $isDescendant;
	}
	public function setInnerXML($str) {
		$this->setter_innerXml($str);
		return $this;
	}
	public function setOuterXML($str) {
		$this->setter_outerXml($str);
		return $this;
	}
	public function toString() {
		return $this->getter_outerXml();
	}
	public function iterator() {
		return $this->getChildren(null, null)->iterator();
	}
	public function getter_xml() {
		$this->x = (($this->x !== null) ? $this->x : Xml::parse("<empty />"));
		return $this->x;
	}
	public function getter_fast() {
		$this->f = (($this->f !== null) ? $this->f : new haxe_xml_Fast($this->getter_xml()));
		return $this->f;
	}
	public function getter_name() {
		return (($this->testIsElement()) ? $this->getter_xml()->getNodeName() : "#" . Std::string($this->getter_type()));
	}
	public function setter_name($newName) {
		$this->getter_xml()->setNodeName($newName);
		return $this->getter_xml()->getNodeName();
	}
	public function getter_type() {
		return $this->getter_xml()->nodeType;
	}
	public function testIsCData() {
		return ($this->getter_xml()->nodeType == Xml::$CData);
	}
	public function testIsComment() {
		return ($this->getter_xml()->nodeType == Xml::$Comment);
	}
	public function testIsDocType() {
		return ($this->getter_xml()->nodeType == Xml::$DocType);
	}
	public function testIsDocument() {
		return ($this->getter_xml()->nodeType == Xml::$Document);
	}
	public function testIsElement() {
		return ($this->getter_xml()->nodeType == Xml::$Element);
	}
	public function testIsPCData() {
		return ($this->getter_xml()->nodeType == Xml::$PCData);
	}
	public function testIsProlog() {
		return ($this->getter_xml()->nodeType == Xml::$Prolog);
	}
	public function testIsText() {
		return ($this->testIsCData() || $this->testIsPCData());
	}
	public function getter_value() {
		$v = null;
		$v = (($this->testIsElement() || $this->testIsDocument()) ? "" : $this->getter_xml()->getNodeValue());
		return $v;
	}
	public function setter_value($v) {
		$returnVal = "";
		if(!$this->testIsElement() && !$this->testIsDocument()) {
			$this->getter_xml()->setNodeValue($v);
			$returnVal = $v;
		}
		return $returnVal;
	}
	public function getter_parent() {
		return $this->getParent();
	}
	public function getter_document() {
		return $this->getDocument();
	}
	public function getter_depth() {
		return $this->getDepthFromDocument();
	}
	public function getter_children() {
		return $this->getChildren(null, null);
	}
	public function getter_firstChild() {
		return $this->getFirstChild();
	}
	public function getter_lastChild() {
		return $this->getLastChild();
	}
	public function getter_ancestors() {
		return $this->getAncestors(null, null);
	}
	public function getter_descendants() {
		return $this->getDescendants();
	}
	public function getter_siblings() {
		return $this->getSiblings();
	}
	public function getter_next() {
		return $this->getNext();
	}
	public function getter_prev() {
		return $this->getPrev();
	}
	public function getter_siblingsBefore() {
		return $this->getSiblingsBefore();
	}
	public function getter_siblingsAfter() {
		return $this->getSiblingsAfter();
	}
	public function getter_index() {
		$index = null;
		$parent = null;
		$index = 0;
		if($this->getter_parent() !== null) {
			$index = $this->getter_parent()->getChildren(null, null)->indexOf($this);
		}
		return $index;
	}
	public function getter_numChildren() {
		return Lambda::count($this->getter_xml());
	}
	public function getter_innerXml() {
		return $this->getter_fast()->getInnerHTML();
	}
	public function setter_innerXml($str) {
		$newXml = null;
		$this->hempty();
		$newXml = new hxbase_xml_XmlNode($str, null, null);
		$this->addChildAt($newXml, 0);
		return $str;
	}
	public function getter_outerXml() {
		return $this->getter_xml()->toString();
	}
	public function setter_outerXml($str) {
		if($this->getter_parent() === null) {
			$this->x = Xml::parse($str);
		}
		else {
			$newXml = null;
			$parent = null;
			$index = null;
			$parent = $this->getter_parent();
			$index = $this->getter_index();
			$newXml = new hxbase_xml_XmlNode($str, null, null);
			$newXml->addThisTo($parent, $index);
			$parent->removeChild($this);
		}
		return $str;
	}
	public function getter_innerText() {
		$allDescendants = null;
		$textDescendants = null;
		$s = null;
		$allDescendants = $this->getter_descendants();
		$textDescendants = $allDescendants->filterByFunction(array(new _hx_lambda(array("allDescendants" => &$allDescendants, "s" => &$s, "textDescendants" => &$textDescendants), null, array('x'), "{
			return \$x->testIsText() || \$x->testIsPCData();
		}"), 'execute1'));
		$s1 = new StringBuf();
		$»it = $textDescendants->iterator();
		while($»it->hasNext()) {
		$textNode = $»it->next();
		{
			$s1->b .= $textNode->toString();
			;
		}
		}
		return $textDescendants->join(" ");
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return $this->toString(); }
}
