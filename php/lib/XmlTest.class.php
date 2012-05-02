<?php

class XmlTest {
	public function __construct(){}
	public $tpl;
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
	static function main() {
		php_Lib::hprint("Hello!");
		XmlTest::pushResults("XmlNode Test page");
		XmlTest::pushResults("This is a test page to test as many of the functions of my XmlNode class as I can");
		$xmlStr = null; $xmlDoc = null;
		$xmlStr = "\x0A\x09\x09<html>\x0A\x09\x09<head>\x0A\x09\x09\x09<title>Hey There</title>\x0A\x09\x09</head>\x0A\x09\x09<body>\x0A\x09\x09\x09<h1>Hey There</h1>\x0A\x09\x09\x09<h2>This is my page</h2>\x0A\x09\x09\x09<ul>\x0A\x09\x09\x09\x09<li>These are duplicate menu items</li>\x0A\x09\x09\x09\x09<li>These are duplicate menu items</li>\x0A\x09\x09\x09</ul>\x0A\x09\x09</body>\x0A\x09\x09</html>\x0A\x09\x09";
		$xmlDoc = new hxbase_xml_XmlNode($xmlStr, null, null);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Our starting XML is: ");
		XmlTest::pushResults($xmlDoc);
		XmlTest::pushResults("So we know constructing from a string works, provide you give it valid XML");
		$htmlElm = $xmlDoc->getter_children()->getAt(2);
		XmlTest::pushResults(null);
		XmlTest::pushResults("By default when you construct from a string, the XmlNode object will have the type \"document\".");
		XmlTest::pushResults("You need to access the children to reach the real elements you have defined.");
		XmlTest::pushResults("So to get the HTML element through xmlDoc.children.getAt() ");
		XmlTest::pushResults("   xmlDoc.children.getAt(2).name = " . $xmlDoc->getChildAt(2)->getter_name());
		$bodyElm = $htmlElm->getChildAt(4);
		XmlTest::pushResults($bodyElm->getter_name());
		XmlTest::pushResults(null);
		XmlTest::pushResults("See if body has attribute bgcolor using bodyElm.hasAtt(\"bgcolor\")");
		XmlTest::pushResults("   hasAtt(): " . $bodyElm->hasAtt("bgcolor"));
		XmlTest::pushResults("Set the attribute using bodyElm.setAtt(\"bgcolor\", \"red\")");
		$bodyElm->setAtt("bgcolor", "red");
		XmlTest::pushResults("Now check again if it is there...");
		XmlTest::pushResults("   hasAtt(): " . $bodyElm->hasAtt("bgcolor"));
		XmlTest::pushResults("So get the value using bodyElm.getAtt(\"bgcolor\")");
		XmlTest::pushResults("   getAtt(): " . $bodyElm->getAtt("bgcolor"));
		XmlTest::pushResults(null);
		XmlTest::pushResults("Body: " . $bodyElm->getter_name());
		XmlTest::pushResults("Previous: " . $bodyElm->getter_prev()->getter_name());
		XmlTest::pushResults("Previous->Previous: " . $bodyElm->getter_prev()->getter_prev()->getter_name());
		XmlTest::pushResults("Previous->Previous->FirstChild: " . $bodyElm->getter_prev()->getter_prev()->getter_firstChild()->getter_name());
		XmlTest::pushResults("Previous->Previous->FirstChild->next: " . $bodyElm->getter_prev()->getter_prev()->getter_firstChild()->getter_next()->getter_name());
		XmlTest::pushResults("Body->LastChild: " . $bodyElm->getter_lastChild()->getter_name());
		XmlTest::pushResults("Body->LastChild->Previous: " . $bodyElm->getter_lastChild()->getter_prev()->getter_name());
		XmlTest::pushResults("Body->LastChild->Previous->FirstChild->Next: " . $bodyElm->getter_lastChild()->getter_prev()->getter_firstChild()->getter_next()->getter_name());
		$list = null;
		$list = $bodyElm->getter_lastChild()->getter_prev()->getter_firstChild()->getter_next()->getAncestors(null, null);
		XmlTest::pushResults("List of elements in li.getAncestors()");
		XmlTest::printXmlList($list);
		$list = $bodyElm->getter_descendants();
		XmlTest::pushResults("List of elements in body.getDescendants()");
		XmlTest::printXmlList($list);
		$list = $bodyElm->getter_children();
		XmlTest::pushResults("List of elements in body elements children");
		XmlTest::printXmlList($list);
		$list = $bodyElm->getChildAt(4)->getter_siblingsBefore();
		XmlTest::pushResults("List of elements in h2.getSiblingsBefore()");
		XmlTest::printXmlList($list);
		$list = $bodyElm->getChildAt(4)->getter_siblingsAfter();
		XmlTest::pushResults("List of elements in h2.getSiblingsAfter()");
		XmlTest::printXmlList($list);
		$list = $bodyElm->getChildAt(4)->getter_siblings();
		XmlTest::pushResults("List of elements in h2.getSiblings()");
		XmlTest::printXmlList($list);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Our starting XML is: ");
		XmlTest::pushResults($xmlDoc);
		$ul = null;
		$ul = $bodyElm->getter_lastChild()->getter_prev();
		$ul->hempty();
		$ul->appendChild(new hxbase_xml_XmlNode("<li>YEAH</li>", null, null));
		$ul->prependChild(new hxbase_xml_XmlNode("\x0A\x09\x09\x09<li>Combined Add</li>\x0A\x09\x09\x09<li>Combined Add 2</li>\x0A\x09\x09\x09", null, null));
		$loserMenuItem = null;
		$loserMenuItem = new hxbase_xml_XmlNode("<test />", null, null);
		$loserMenuItem->addThisTo($ul, 3);
		$ul->removeChildAt(3)->removeChildAt(1);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Our XML is now: ");
		XmlTest::pushResults($xmlDoc);
		XmlTest::pushResults(null);
		XmlTest::pushResults("The text in here is (xmlDoc.innerText):");
		XmlTest::pushResults($xmlDoc->getter_innerText());
		XmlTest::pushResults(null);
		XmlTest::pushResults("The innerXML of the body in here is (bodyElm.innerXML):");
		XmlTest::pushResults($bodyElm->getter_innerXml());
		XmlTest::pushResults(null);
		XmlTest::pushResults("Now set the innerXML (bodyElm.innerXML = \"\" OR bodyElm.setInnerXML(\"\")):");
		$bodyElm->setInnerXML("<h1>New Body Title</h1>");
		XmlTest::pushResults($xmlDoc);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Now read the outerXML (bodyElm.outerXML:");
		XmlTest::pushResults($bodyElm->getter_outerXml());
		XmlTest::pushResults(null);
		XmlTest::pushResults("Now set the outerXML (bodyElm.outerXML = \"\" OR bodyElm.setOuterXML(\"\")):");
		$bodyElm->setter_outerXml("<crazyBody>Hey hey!</crazyBody>");
		XmlTest::pushResults($xmlDoc);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Now try to copy that ridiculous body:");
		$bodyElm = $htmlElm->getChildAt(4);
		$duplicate = null;
		$duplicate = $bodyElm->copy();
		$bodyElm->getter_parent()->addChildAfter($duplicate, $bodyElm);
		XmlTest::pushResults($xmlDoc);
		XmlTest::pushResults(null);
		XmlTest::pushResults(null);
		XmlTest::pushResults("Testing how the constructor works.");
		$constructByString = null; $constructByXml = null; $constructByXmlNode = null;
		$constructByString = new hxbase_xml_XmlNode("<b>Hey you</b>", null, null);
		$constructByXml = new hxbase_xml_XmlNode(null, $constructByString->getter_xml(), null);
		$constructByXmlNode = new hxbase_xml_XmlNode(null, null, $constructByString);
		XmlTest::pushResults("By String: \x09" . $constructByString);
		XmlTest::pushResults("By Xml:    \x09" . $constructByXml);
		XmlTest::pushResults("By XmlNode: \x09" . $constructByXmlNode);
		XmlTest::pushResults(null);
		XmlTest::pushResults("So if we change constructByString, then constructByXml should also change.");
		$constructByString->getter_firstChild()->setInnerXML("test");
		XmlTest::pushResults("By String: \x09" . $constructByString);
		XmlTest::pushResults("By Xml:    \x09" . $constructByXml);
		XmlTest::pushResults("By XmlNode: \x09" . $constructByXmlNode);
		php_Lib::hprint("<pre style=\"border: 1px solid black;\">" . StringTools::htmlEscape(XmlTest::$str) . "</pre>");
	}
	static $str = "";
	static function pushResults($in_str) {
		if($in_str === null) {
			$in_str = "";
		}
		XmlTest::$str .= "\x0A" . Std::string($in_str);
	}
	static function printXmlList($list) {
		if(null == $list) throw new HException('null iterable');
		$»it = $list->iterator();
		while($»it->hasNext()) {
			$elm = $»it->next();
			XmlTest::pushResults("\x09" . $elm->getter_name());
		}
	}
	function __toString() { return 'XmlTest'; }
}
