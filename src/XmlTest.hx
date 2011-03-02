import hxbase.xml.AdvXmlItem;
import hxbase.xml.XmlList;
import hxbase.tpl.HxTpl;

class XmlTest
{
	private var tpl:HxTpl;
	
	static function main() 
    	{
    		
		php.Lib.print("Hello!");
    		
		/*
		
		var advXml:AdvXmlItem;
    		var children:XmlList;
    		var xmlStr:String;
		
    		xmlStr = "<parent><child>1 one</child>
    			<child>2 two</child>
    			<child>3 three</child></parent>";
    		
		advXml = new AdvXmlItem(xmlStr);
		
		var str:String;
		str = advXml.xml.toString();
		str = Std.string(advXml.xml.nodeType);						// "document"
		str = Std.string(advXml.xml.firstChild().nodeType);				// "element"
		str = Std.string(advXml.xml.firstChild().nodeName);				// "parent"
		str = Std.string(advXml.xml.firstChild().firstChild().nodeType);		// "element"
		str = Std.string(advXml.xml.firstChild().firstChild().nodeName);		// "child"
		str = Std.string(advXml.xml.firstChild().firstChild().firstChild().nodeType);	// "pcdata"
		str = Std.string(advXml.xml.firstChild().firstChild().firstChild().nodeValue);	// "1 one"
		
		str = "";
		
		str = Std.string(advXml.type);						// document
		str = Std.string(advXml.getChildren().length);				// 1
		str = Std.string(advXml.getChildren().first());				// <child>1 one</child>
		
		str = "";
		
		
		for (child in advXml)
		{
			str += "\n-> " + child;						// working as expected! 
		}
		
		str = Std.string(advXml.getChildren());					// working as expected!
		
		// okay, try it multiple root elements, see how it behaves
		xmlStr = "<child>1 one</child><child>2 two</child><child>3 three</child>";
		advXml = new AdvXmlItem(xmlStr);
		
		str = Std.string(advXml);
		str = Std.string(advXml.type);						// document
		str = Std.string(advXml.firstChild);					// <child>1 one</child>
		str = Std.string(new AdvXmlItem(null, null, advXml.firstChild).type);	// document
		str = Std.string(advXml.numChildren);
		
		
		// that was all to do with checking children.  Still a little dodgy I believe.
		// Let's try some attributes
		
		xmlStr = "<myField size='big'>Hi</myField>";
		advXml = new AdvXmlItem(xmlStr);
		
		str = "Does this have the attribute size: " + advXml.firstChild.hasAtt('size');	// 1
		str = "Does this have the attribute sizes: " + advXml.firstChild.hasAtt('size');	// -nothing,null-
		str = "What is that attribute value? " + advXml.firstChild.getAtt('size');		// 'big'
		str = "What about a value that doesn't exist? " + advXml.firstChild.getAtt('sizes');	// ""
		
		str = advXml.toString();								// <myField size="big">Hi</myField>
		str = advXml.firstChild.setAtt("color", "red").toString();				// <myField size="big" color="red">Hi</myField>
		str = advXml.firstChild.setAtt("size", "huge-o").toString();				// <myField size="huge-o" color="red">Hi</myField>
		
		var attList:List<String> = advXml.firstChild.getAttList();
		str = Std.string(attList.length);							// 2
		str = "Attributes: ";
		for (attName in attList)
		{
			str += attName + ", ";
		}
		
		str = advXml.toString();								// <myField size="huge-o" color="red">Hi</myField>
		str = "Number of children: " + advXml.numChildren;					// 1
		
		xmlStr = "<parent><child>2 two</child>
    			<child>2 two</child>
    			<child>3 three</child></parent>";
    		
		advXml = new AdvXmlItem(xmlStr);
		var xmlParent:AdvXmlItem = advXml.firstChild;
		str = "Name: " + xmlParent.name;									// parent
		str = "Number: " + xmlParent.numChildren;								// 5 (text nodes included...)
		
		var first:AdvXmlItem = xmlParent.firstChild;
		str = "Position of found child: " + xmlParent.children.indexOf(first);					// 1
		str = "Position of found child: " + xmlParent.children.indexOf(new AdvXmlItem("FAKE"));			// 0
		
		var third:AdvXmlItem = xmlParent.children.getAt(3);
		str = "3rd is: " + third;										// <child>2 two</child>
		str = "Position of this child: " + xmlParent.children.indexOf(third);					// 3
		
		//
		//
		// Okay, now that getAt() and indexOf() are working, we can go through and do the "add child" methods
		//
		//
		
		var childOfThird:AdvXmlItem;
		childOfThird = new AdvXmlItem("<b>YAR!</b>").firstChild;
		
		str = childOfThird.toString();										// <b>YAR!</b>
		str = Std.string(third.addChildAt(childOfThird,0));							// <child><b>YAR!</b>2 two</child>
		str = Std.string(third.addChildAt(new AdvXmlItem(childOfThird),2));						// <child><b>YAR!</b>2 two<b>YAR!</b></child>
		
		str = Std.string(third.addChildAt(new AdvXmlItem("<i>This index is too high</i>"),32));			// putting an index that is too high just puts it at the end
		str = Std.string(third.addChildAt(new AdvXmlItem("<i>This index <u>is</u> too low</i>"), -5));			// putting an index too low just puts it at the front
		//str = Std.string(third.addChildAt(new AdvXmlItem("<i>This index is not an integer</i>"), 1.5));		// will not compile (go strict typing!)
		
		str = Std.string(xmlParent.appendChild(new AdvXmlItem("<firstborn>This goes at the end</firstborn>")));	// places element at end of children
		str = Std.string(xmlParent.prependChild(new AdvXmlItem("<baby>This should be at the front</baby>")));	// places element at front of children
		
		var child3:AdvXmlItem = xmlParent.getChildren().getAt(6);							// <child>3 three</child>
		str = Std.string(xmlParent.getChildren().indexOf(child3));						// 6
		str = Std.string(child3.index);										// 6
		str = Std.string(xmlParent.addChildBefore(new AdvXmlItem("<note>This is before child 3</note>"), child3));	// appears the very node before child3
		str = Std.string(xmlParent.addChildAfter(new AdvXmlItem("<note>This is after child 3</note>"), child3));	// appears the very node after child3
		
		*/
		
		pushResults('AdvXmlItem Test page');
		pushResults('This is a test page to test as many of the functions of my AdvXmlItem class as I can');
		
		var xmlStr:String, xmlDoc:AdvXmlItem;
		
		xmlStr = "
		<html>
		<head>
			<title>Hey There</title>
		</head>
		<body>
			<h1>Hey There</h1>
			<h2>This is my page</h2>
			<ul>
				<li>These are duplicate menu items</li>
				<li>These are duplicate menu items</li>
			</ul>
		</body>
		</html>
		";
		
		xmlDoc = new AdvXmlItem(xmlStr);
		
		pushResults();
		pushResults('Our starting XML is: ');
		pushResults(xmlDoc);
		
		pushResults('So we know constructing from a string works, provide you give it valid XML');
		
		var htmlElm:AdvXmlItem = xmlDoc.children.getAt(2);
		
		pushResults();
		pushResults('By default when you construct from a string, the AdvXmlItem object will have the type "document".');
		pushResults('You need to access the children to reach the real elements you have defined.');
		pushResults('So to get the HTML element through xmlDoc.children.getAt() ');
		pushResults('   xmlDoc.children.getAt(2).name = ' + xmlDoc.getChildAt(2).name);
		
		var bodyElm:AdvXmlItem = htmlElm.getChildAt(4);
		
		pushResults(bodyElm.name);
		
		pushResults();
		pushResults('See if body has attribute bgcolor using bodyElm.hasAtt("bgcolor")');
		pushResults('   hasAtt(): ' + bodyElm.hasAtt("bgcolor"));
		pushResults('Set the attribute using bodyElm.setAtt("bgcolor", "red")');
		bodyElm.setAtt("bgcolor", "red");
		pushResults('Now check again if it is there...');
		pushResults('   hasAtt(): ' + bodyElm.hasAtt("bgcolor"));
		pushResults('So get the value using bodyElm.getAtt("bgcolor")');
		pushResults('   getAtt(): ' + bodyElm.getAtt("bgcolor"));
		
		pushResults();
		pushResults("Body: " + bodyElm.name);
		pushResults("Previous: " + bodyElm.prev.name);
		pushResults("Previous->Previous: " + bodyElm.prev.prev.name);
		pushResults("Previous->Previous->FirstChild: " + bodyElm.prev.prev.firstChild.name);
		pushResults("Previous->Previous->FirstChild->next: " + bodyElm.prev.prev.firstChild.next.name);
		pushResults("Body->LastChild: " + bodyElm.lastChild.name);
		pushResults("Body->LastChild->Previous: " + bodyElm.lastChild.prev.name);
		pushResults("Body->LastChild->Previous->FirstChild->Next: " + bodyElm.lastChild.prev.firstChild.next.name);
		
		var list:XmlList;
		list = bodyElm.lastChild.prev.firstChild.next.getAncestors();
		
		pushResults('List of elements in li.getAncestors()');
		printXmlList(list);
		
		list = bodyElm.descendants;
		
		pushResults('List of elements in body.getDescendants()');
		printXmlList(list);
		
		
		list = bodyElm.children;
		
		pushResults('List of elements in body elements children');
		printXmlList(list);
		
		list = bodyElm.getChildAt(4).siblingsBefore;
		
		pushResults('List of elements in h2.getSiblingsBefore()');
		printXmlList(list);
		
		list = bodyElm.getChildAt(4).siblingsAfter;
		
		pushResults('List of elements in h2.getSiblingsAfter()');
		printXmlList(list);
		
		list = bodyElm.getChildAt(4).siblings;
		
		pushResults('List of elements in h2.getSiblings()');
		printXmlList(list);
		
		//
		// Working so far: attributes, adding children, navigation
		// Not working so far: addThisTo, removing children, copy.....
		//
		
		pushResults();
		pushResults('Our starting XML is: ');
		pushResults(xmlDoc);
		
		var ul:AdvXmlItem;
		ul = bodyElm.lastChild.prev;
		ul.empty();
		
		ul.appendChild(new AdvXmlItem("<li>YEAH</li>"));
		ul.prependChild(new AdvXmlItem("
			<li>Combined Add</li>
			<li>Combined Add 2</li>
			"));
		var loserMenuItem:AdvXmlItem;
		loserMenuItem = new AdvXmlItem("<test />");
		loserMenuItem.addThisTo(ul,3);
		
		ul.removeChildAt(3).removeChildAt(1);					// removes whitespace, working correctly
		
		
		
		pushResults();
		pushResults('Our XML is now: ');
		pushResults(xmlDoc);
		
		pushResults();
		pushResults('The text in here is (xmlDoc.innerText):');
		pushResults(xmlDoc.innerText);
		
		pushResults();
		pushResults('The innerXML of the body in here is (bodyElm.innerXML):');
		pushResults(bodyElm.innerXML);
		
		
		pushResults();
		pushResults('Now set the innerXML (bodyElm.innerXML = "" OR bodyElm.setInnerXML("")):');
		bodyElm.setInnerXML("<h1>New Body Title</h1>");
		pushResults(xmlDoc);
		
		pushResults();
		pushResults('Now read the outerXML (bodyElm.outerXML:');
		pushResults(bodyElm.outerXML);
		
		
		pushResults();
		pushResults('Now set the outerXML (bodyElm.outerXML = "" OR bodyElm.setOuterXML("")):');
		bodyElm.outerXML = "<crazyBody>Hey hey!</crazyBody>";
		pushResults(xmlDoc);
		
		pushResults();
		pushResults('Now try to copy that ridiculous body:');
		bodyElm = htmlElm.getChildAt(4);
		var duplicate:AdvXmlItem;
		duplicate = bodyElm.copy();
		bodyElm.parent.addChildAfter(duplicate, bodyElm);
		pushResults(xmlDoc);
		
		pushResults();
		pushResults();
		pushResults('Testing how the constructor works.');
		
		var constructByString:AdvXmlItem, constructByXml:AdvXmlItem, constructByAdvXmlItem:AdvXmlItem;
		
		constructByString = new AdvXmlItem("<b>Hey you</b>");
		constructByXml = new AdvXmlItem(constructByString.xml);
		constructByAdvXmlItem = new AdvXmlItem(constructByString);
		pushResults("By String: 	" + constructByString);
		pushResults("By Xml:    	" + constructByXml);
		pushResults("By AdvXmlItem: 	" + constructByAdvXmlItem);
		
		pushResults();
		pushResults("So if we change constructByString, then constructByXml should also change.");
		constructByString.firstChild.setInnerXML('test');
		
		pushResults("By String: 	" + constructByString);
		pushResults("By Xml:    	" + constructByXml);
		pushResults("By AdvXmlItem: 	" + constructByAdvXmlItem);
		
		
		//new AdvXmlItem("<invalue>Bad markup</invalid>");  // Fails, as it should
		
		php.Lib.print('<pre style="border: 1px solid black;">' + StringTools.htmlEscape(str) + '</pre>');
		
		
		
		
		/*
		children = advXml.getChildren();
		
		php.Lib.print("<br /><br />1)" + advXml.name + " - " + children.length + ' - <!-- ' + advXml.outerXML + ' -->');
		
		php.Lib.print("<br /><br />2)");
		if (advXml.getChildren().first() != null)
		{
			php.Lib.print(advXml.getChildren().first().name + " - " + children.length + ' - <!-- ' + advXml.getChildren().first().outerXML + ' -->');
		}
		else { php.Lib.print('No children found muchly...'); }
		
		
		trace ('LENGTH ' + str.length + '/LENGTH');
		
		var i:Int = 0;
		for (child in advXml)
		{
			i++;
			php.Lib.print("<br />" + i + " - " + child);
		}*/
		
		
    	}
    	
    	private static var str:String = "";
    	public static function pushResults(?in_str:Dynamic = '')
    	{
    		str += '\n' + Std.string(in_str);
    	}
    	
    	
    	public static function printXmlList(list:XmlList)
    	{
    		for (elm in list)
    		{
    			pushResults('	' + elm.name);
    		}
    	}
}
