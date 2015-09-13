/**   
   This class aims to provide a much easier way to navigate and manipulate
   XML in haxe.  This implementation should be platform independent, though
   I haven't done the testing yet.
   
   The idea is, if all platforms can implement the same XML class, then
   the code portability is going to be even higher.  Which is sweet!
   
   I've tried to copy alot of the functionality from libraries like jquery.
   So at the moment most functions return themselves, so they can be chained
   together.  For example:
   
   | bodyElm.setInnerHTML("&lt;h1&gt;Inner HTML of Body&lt;/h1&gt;").firstChild.setAtt("class","Title");
   
   Should produce...
   
   | &lt;body&gt;&lt;h1 class="Title"&gt;Inner HTML of Body&lt;/h1&gt;
*/

//
//
// WHERE I'M AT
//  This class largely works.  Woop woop.  
//  I need to keep moving, so perhaps spend an hour or two documenting this
//  using natural docs.  
//
//  Then, move back to the templating class.
//  You may need to rewrite that entire function, based on the same PSEUDOCODE
//  Because pretty much all the details of XML objects and functions has changed
//  
//  But keep moving.  Aim to have HxTpl running fine by Monday
//
//  Then you can move on to real stuff, like accessing the filesystem!
//
//  And one day it'll be worth testing this on other platforms, particularly JS.  Flash also.  Not sure about neko/cpp
//

package hxbase.xml;
import Xml;
import haxe.xml.Fast;
import hxbase.xml.XmlList;
import hxbase.Log;

class XmlNode
{
	// Public variables
	
	/** A reference to the internal Xml object we're using.  (Read Only) */
	public var xml(get,null):Xml;
	
	/** A reference to the internal Fast object we're using.  (Read Only) */
	public var fast(get,null):Fast;
	
	/** The name of the current Element node.  
	  If the node is not an Element, then this will return the node type.*/
	public var name(get,set):String;
	
	/** The value of the current node.  
	  Does not work on Document or Element nodes.  */
	public var value(get,set):String;
	
	
	/**
	  Variable: type
	  (String) The type of the current node.  (Read Only)
	  
	  Will be one of Cdata, PCData, Prolog, DocType, Document, Comment or Element
	*/
	public var type(get,null):XmlType;
		/**
		  Variable: isCData
		  (Bool) Is this a CData node?
		*/
		public var isCData(get,null):Bool;
		
		/**
		  Variable: isPCData
		  (Bool) Is this a PCData node?
		*/
		public var isPCData(get,null):Bool;
		
		/**
		  Variable: isProlog
		  (Bool) Is this a Prolog node?
		*/
		//public var isProlog(get,null):Bool;
		
		/**
		  Variable: isDocType
		  (Bool) Is this a DocType node?
		*/
		public var isDocType(get,null):Bool;
		
		/**
		  Variable: isDocument
		  (Bool) Is this a Document node?
		*/
		public var isDocument(get,null):Bool;
		
		/**
		  Variable: isComment
		  (Bool) Is this a Comment node?
		*/
		public var isComment(get,null):Bool;
		
		/**
		  Variable: isElement
		  (Bool) Is this a Element node?
		*/
		public var isElement(get,null):Bool;
		
		/**
		  Variable: isText
		  (Bool) Is this a text node, that is, CData OR PCData?
		*/
		public var isText(get,null):Bool;
	
	/**
	  Variable: index
	  (Int) The index of this node in relation to it's siblings.
	  
	  The first child of a parent has index 1, the last child has index
	  parent.numChildren.  
	  
	  Returns 0 if this object doesn't have a parent.
	*/
	public var index(get,null):Int;
	
	/**
	  Variable: numChildren
	  (Int) The number of children this node has.
	  
	  Returns 0 if this object isn't a parent.
	*/
	public var numChildren(get, null):Int;
	
	/**
	  Variable: parent
	  (<XmlNode>) Shortcut for this.getParent(); (Read Only)
	*/
	public var parent(get, null):XmlNode;
	
	/**
	Get's the root document node.  
	(Not the document element, the node, which the element is contained in)
	*/
	public var document(get, null):XmlNode;
	
	/**
	Shortcut for this.getDepthFromDocument().  Immediate childs of document node return 1, grandchildren 2, etc.
	*/
	public var depth(get, null):Int;
	
	/**
	  Variable: children
	  (<XmlList>) Shortcut for this.getChildren(); (Read Only)
	*/	
	public var children(get, null):XmlList;
	
	/**
	  Variable: firstChild
	  (<XmlNode>) Shortcut for this.getFirstChild(); (Read Only)
	*/
	public var firstChild(get, null):XmlNode;
	
	/**
	  Variable: lastChild
	  (<XmlNode>) Shortcut for this.getLastChild(); (Read Only)
	*/
	public var lastChild(get, null):XmlNode;
	
	/**
	  Variable: ancestors
	  (<XmlList>) Shortcut for this.getAncestors(); (Read Only)
	*/
	public var ancestors(get, null):XmlList;
	
	/**
	  Variable: descendants
	  (<XmlList>) Shortcut for this.getDescendants(); (Read Only)
	*/
	public var descendants(get, null):XmlList;
	
	/**
	  Variable: siblings
	  (<XmlList>) Shortcut for this.getSiblings(); (Read Only)
	*/
	public var siblings(get, null):XmlList;
	
	/**
	  Variable: next
	  (<XmlNode>) Shortcut for this.getNext(); (Read Only)
	*/
	public var next(get, null):XmlNode;
	
	/**
	  Variable: prev
	  (<XmlNode>) Shortcut for this.getPrev(); (Read Only)
	*/
	public var prev(get, null):XmlNode;
	
	/**
	  Variable: siblingsBefore
	  (<XmlList>) Shortcut for this.getSiblingsBefore(); (Read Only)
	*/
	public var siblingsBefore(get, null):XmlList; 
	
	/**
	  Variable: siblingsAfter
	  (<XmlList>) Shortcut for this.getSiblingsAfter(); (Read Only)
	*/
	public var siblingsAfter(get, null):XmlList;
	
	/**
	  Variable: innerXML
	  (String) Gets and sets the inner XML.
	  
	  The innerXML is anything between the main tags.
	  
	  > trace (xml);        	// "&lt;h1&gt;Old &lt;b&gt;Title&lt;/b&gt;&lt;/h1&gt;"
	  > trace (xml.innerHTML);	// "Old &lt;b&gt;Title&lt;/b&gt;"
	  >
	  > xml.innerHTML = "New <i>Title</i>";
	  >
	  > trace (xml);       		// "&lt;h1&gt;New &lt;i&gt;Title&lt;/i&gt;&lt;/h1&gt;"
	  
	*/
	public var innerXML(get, set):String;
	
	/**
	  Variable: outerXML
	  (String) Gets and sets the outer XML.
	  
	  <pre>
trace (body);           	// "&lt;body&gt;&lt;h1 class="title"&gt;Old Title&lt;/h1&gt;&lt;/body&gt;"
trace (header);         	// "&lt;h1 class="title"&gt;Old Title&lt;/h1&gt;"
trace (header.outerXML);	// "&lt;h1 class="title"&gt;Old Title&lt;/h1&gt;"
header.outerXML = "&lt;header size='1'&gt;New Title&lt;/header&gt;";
trace (xml);       	    	// "&lt;body&gt;&lt;header size='1'&gt;New Title&lt;/header&gt;&lt;/body&gt;"
</pre>
	  
	  Please note that after setting outerXML, this object is no longer useful.
	  There are reasons for this, mainly we don't know how many nodes have been inserted
	  And we can only wrap one at a time, so which one do we choose?
	  
	  You will have to find the object again, like this
	  
	  &gt; header = body.firstChild;
	  &gt; header.outerXML = "&lt;h2&gt;New Header&lt;/h2&gt;";
	  &gt; header = body.firstChild;
	  &gt; header.setAtt("class", "pageHead");
	  
	  I should set it up to latch on to the first child, but haven't done so yet.
	  
	*/
	public var outerXML(get, set):String;
	
	/*
	  Variable: innerText
	  (String) Gets the combined text content of all child nodes, and allows
	           you to set the text content of this node.  (This will remove
	           all other children).
	  
	  The innerXML is anything between the main tags.
	  
	  > trace (xml);        	// "<h1>Old <b>Title</b></h1>"
	  > trace (xml.innerXML);	// "Old <b>Title</b>"
	  > trace (xml.innerText);	// "Old Title"
	  >
	  > xml.innerText = "New Title";
	  >
	  > trace (xml);       		// "<h1>New Title</h1>"
	  
	*/
	public var innerText(get, set):String;
	
	// Private variables
	var x:Xml;
	var f:Fast;
	
	/***********************************************************************
	Constructor: new()
	
	Constructs an XmlNode object.
	
	It can be constructed based on a string, a HaXe Xml object, or an 
	existing XmlNode object.  Please note, the parameters are optional,
	and so you can enter any single one of them, rather than having to 
	remember the order.
	
	It would be nice in this situation to use different constructor methods
	Java style.  Oh well.
	
	Constructing by string:
	
	If you pass the new() constructor a String, it will use Xml.Parse() 
	to create a new set of nodes.  If the stringi s not valid XML, an 
	"Xml parse error" will be thrown, and the script will fail.  (We 
	should probably be a little nicer).
	
	When constructing from a string, a Document node is created.  Any other
	nodes (elements, text nodes, etc) are childs of this document node.
	
	Constructing by Xml object:
	
	If you pass the new() constructor an Xml object, then it wraps this
	with XmlNode functionality.  Please note this will not create a new
	node in the XML document, merely wrap our functionality around an 
	existing node.
	
	Constructing by an existing XmlNode object:
	
	If you pass the new() constructor an existing XmlNode, it will use
	the <XmlNode.copy()> method of the existing object to create the new
	object.
	
	
	
	
	So an example or two:
	
	> // Declare the object we'll be using
	> var objFromString:XmlNode;
	> var objFromXml:XmlNode;
	> var objFromXml:XmlNode;
	>
	> // Initialise the string object
	> objFromString = new XmlNode("<myElement>Hey!</myElement>");
	> trace (objFromString)				// <myElement>Hey!</myElement>
	> trace (objFromString.type)			// Document
	> trace (objFromString.firstChild.type) 	// Element
	> trace (objFromString.firstChild.name) 	// myElement
	>
	> // Then construct from an XML object
	> 
	> // And then from an existing XmlNode object.
	>
	
	Parameters:

		str_in - Initialise an XmlNode object from a string.  
		xml_in - Create a new XmlNode object, based on an existing Xml
		         object.  This won't duplicate the existing node, rather
		         it will wrap the XmlNode functionality around that 
		         node.
		advXmlItem_in - Create a new XmlNode object, copying 
		                an existing object.  This will duplicate the XML
		                node.

	Returns:

		(<XmlNode>) The object that has been created.

	***********************************************************************/
	
	public function new(?str_in:String, ?xml_in:Xml, ?advXmlItem_in:XmlNode)
	{
		this.x = null;
		this.f = null;
		
		// if all we've got is a string
		if (str_in != null && xml_in == null && advXmlItem_in == null)
			{ this.outerXML = str_in; }
		
		//
		// worth noting, i'm referencing these objects XML properties 
		// directly, not creating copies of them.  So we're referencing
		// the same object in memory, the same piece of the XML tree.
		//
			
		// we have is an Xml object, but not an XmlNode
		if (xml_in != null && advXmlItem_in == null)
		{
			this.x = xml_in;
		}
		
		//
		// and here we're duplicating the object, by copying it's xml string
		//
		
		// all we have is an advXmlItem 
		if (advXmlItem_in != null)
		{
			this.x = advXmlItem_in.copy().xml;
		}
		
		// clear the whitespace (should make this optional.  especially considering it's probably dodgy)
		this.clearWhitespace(); 
	}
	
	/*
	Function: copy()
	
	Returns a duplicate of the current opject.  This will duplicate the
	current XML node as well, so you can add the cloned child to the 
	children.
	
	This more or less uses new XmlNode(this);
		
	Returns:

		(<XmlNode>) A duplicate of this object.  (Including 
		               duplicating the XML node).
	*/
	
	public function copy():XmlNode
	{
		var parsedObj:XmlNode;
		var returnObj:XmlNode;
		
		// Make sure we're ending up with the same type here.  IE. If it's not a document, then this document will end up
		parsedObj = new XmlNode(this.outerXML);	
		
		returnObj = (this.isDocument) ? parsedObj : parsedObj.firstChild;
		
		Log.assert (this.type == returnObj.type, "The duplicate object doesn't have the same type, when it should.");
		Log.assert (this.numChildren == returnObj.numChildren, "The duplicate object has less children than the original.");
		Log.assert (this.name == returnObj.name, "The duplicate object has a different name property to the original.");
		
		return returnObj;
	}
	
	//
	// Functions for adding children
	// 	All working!
	//
	
	/*
	Function: addChildAt()
	
	Adds a given node as a child of this node, at a certain index.  
	
	The index of firstChild is 1, and the index of lastChild is numChildren.
	
	> parentElm.addChildAt(child, 0);                       	// Will insert at the very beginning
	> parentElm.addChildAt(child, parentElm.numChildren);		// Will insert at the very end
	> parentElm.addChildAt(child, 4);                       	// Will insert after Child4, before Child5
	> parentElm.addChildAt(child, 5);                       	// Will insert after Child5, before Child6
	
	Only works on Document or Element nodes.  If the index is too low, it
	will go to the front of the children, if it is too high, it will go to
	the back.
	
	Parameters:
	
		child - (<XmlNode>) The child object to add. 
		index - (Int) The position to add it at.  
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function addChildAt(child:XmlNode, ?index:Int = 0):XmlNode
	{
		if (child.isDocument)
		{
			var indexOffset:Int;
			indexOffset = 0;
			
			for (documentChild in child.children)
			{
				this.xml.insertChild(documentChild.xml, (index + indexOffset));
				indexOffset++;
			}
		}
		else
		{
			this.xml.insertChild(child.xml, index);
		}
		
		return this;
	}
	
	/*
	Function: appendChild()
	
	Adds a given node as a child of this node, at the very end.
	This is equivalent to:
	
	> parentElm.addChildAt(child, parentElm.numChildren);	
	
	Only works on Document or Element nodes.  
	
	Parameters:
	
		child - (<XmlNode>) The child object to add. 
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function appendChild(child:XmlNode):XmlNode
	{
		this.addChildAt(child,this.numChildren);
		return this;
	}
	
	/*
	Function: prependChild()
	
	Adds a given node as a child of this node, at the very beginning.
	This is equivalent to:
	
	> parentElm.addChildAt(child, 0);	
	
	Only works on Document or Element nodes.  
	
	Parameters:
	
		child - (<XmlNode>) The child object to add. 
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function prependChild(child:XmlNode):XmlNode
	{
		this.addChildAt(child,0);
		return this;
	}
	
	/*
	Function: addChildBefore()
	
	Adds a given node as a child of this node, and inserts it just *before* 
	another child.	
	
	Only works on Document or Element nodes.  
	
	Parameters:
	
		newChild - (<XmlNode>) The child object to add. 
		existingChild - (<XmlNode>) The existing child that newChild
		                will be inserted before. 
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function addChildBefore(newChild:XmlNode, existingChild:XmlNode):XmlNode
	{
		// to go before the newChild index should be (existingChild.index - 1)
		this.addChildAt(newChild,existingChild.index - 1);
		return this;
	}
	
	/*
	Function: addChildAfter()
	
	Adds a given node as a child of this node, and inserts it just *after* 
	another child.	
	
	Only works on Document or Element nodes.  
	
	Parameters:
	
		newChild - (<XmlNode>) The child object to add. 
		existingChild - (<XmlNode>) The existing child that newChild
		                will be inserted after. 
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function addChildAfter(newChild:XmlNode, existingChild:XmlNode):XmlNode
	{
		// to go after the newChild index should be (existingChild.index)
		this.addChildAt(newChild,existingChild.index);
		return this;
	}
	
	//
	// Add this element to another
	//
	
	/*
	Function: addThisTo()
	
	Adds this node to another.  So this node becomes the child, and the 
	object we pass becomes the parent.  This is similar to calling:

	> newParent.addChildAt(this,pos);

	And the position index works the same way as <addChildAt()>.	
	
	Only works if newParent is a Document or Element node.
	
	
	
	Parameters:
	
		newChild - (<XmlNode>) The child object to add. 
		existingChild - (<XmlNode>) The existing child that newChild
		                will be inserted after. 
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function addThisTo(newParent:XmlNode, ?pos:Int = -1):XmlNode
	{
		if (pos == -1)
		{
			pos = newParent.numChildren;
			
		}
		
		if (newParent.isElement || newParent.isDocument)
		{
			newParent.addChildAt(this,pos);
		}
		
		return this;
	}
	
	//
	// Removing children
	// 	Should all be working...
	//
	
	
	/*
	Function: removeChild()
	
	Remove the given node, so it is no longer a child of this object.
	
	For example:
	
	> bodyElm = new XmlNode("<body><first>1</first><second>2</second></body>");
	> first = bodyElm.getChildAt(1);
	> bodyElm.removeChild(first);
	> trace(bodyElm);			//<body><second>2</second></body>
	
	Only works if this is a Document or Element node.
	
	Parameters:
	
		child - (<XmlNode>) The child node we want to remove.
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function removeChild(child:XmlNode):XmlNode
	{
		this.xml.removeChild(child.xml);
		return this;
	}
	
	/*
	Function: removeChildAt()
	
	Remove the child at a given index.
	
	This is similar to
	
	> this.removeChild( this.getChildAt(pos) );
	
	Only works if this is a Document or Element node.
	
	Parameters:
	
		pos - (<XmlNode>) The index of the child node we want to remove.
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function removeChildAt(pos:Int):XmlNode
	{
		this.removeChild(this.getChildAt(pos));
		return this;
	}
	
	/*
	Function: removeChilren()
	
	Remove all the children in a given XmlList
	
	Example:
	
	> var list:XmlList, str:String, xml:XmlNode, parent:XmlNode;
	> 
	> str = "<parent><first /><second /><third /><fourth /></parent>";
	> xml = new XmlNode(str);	
	> // xml.type = document
	> 
	> parent = xml.firstChild;	
	> // parent.type = element, parent.name = parent
	> 
	> trace(parent); 		
	> // <parent><first /><second /><third /><fourth /></parent>
	> 
	> list = new XmlList();
	> list.add(parent.firstChild);	// list now has 1 item
	> list.add(parent.lastChild);	// list now has 2 items
	>
	> parent.removeChildren(list);
	>
	> trace(parent);	
	> // <parent><second /><third /></parent>
	
	Only works if this is a Document or Element node.
	
	Parameters:
	
		children - (<XmlList>) An XmlList containing the objects to 
		           be removed.
	
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function removeChildren(children:XmlList):XmlNode
	{
		for (child in children)
		{
			this.removeChild(child);
		}
		
		return this;
	}
	
	/**
	collapseWhitespace() will go through all child text nodes (not descendants yet)
	and delete any whitespace-only nodes.  
	
	Returns the number of nodes deleted in the process.
	*/
	public function clearWhitespace():Int
	{
		var numChildrenRemoved:Int;
		numChildrenRemoved = 0;
		
		for (child in children)
		{
			if (child.isPCData || child.isText)
			{
				var isWhitespaceOnly = ~/^\s+$/;
				var str:String = child.value;
				
				if (isWhitespaceOnly.match(str))
				{
					this.removeChild(child);
					numChildrenRemoved++;
				}
				else
				{
					var whitespaceAtFront = ~/^\s+/;
					var whitespaceAtBack = ~/\s+$/;
					
					child.value = whitespaceAtFront.replace(str,"");
					child.value = whitespaceAtFront.replace(str,"");
				}
			}
		}
		
		return numChildrenRemoved;
	}
	
	/*
	Function: empty()
	
	Remove all the children of this node.
	
	Only works if this is a Document or Element node.
		           
	Returns:

		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	
	public function empty():XmlNode
	{
		var child:Xml;
		while (this.xml.firstChild() != null)
		{
			this.xml.removeChild(this.xml.firstChild());
		}
		return this;
	}
	
	//
	// Attributes
	//   (all working!!!)
	//
	
	/*
	Function: hasAtt()
	
	Parameters:
		attName - (String) The name of the attribute to search for.
	
	Returns:
		(Bool) Whether this element has this attribute, true or false. 
	*/
	
	public function hasAtt(attName:String):Bool
	{
		return this.xml.exists(attName);
	}
	
	/*
	Function: getAtt()
	
	Parameters:
		attName - (String) The name of the attribute to search for.
	
	Returns:
		(String) The value of the attribute, or "" if it doesn't exist. 
	*/
	public function getAtt(attName:String):String
	{
		var value:String;
		value = "";
		return this.xml.get(attName);
	}
	
	/*
	Function: setAtt()
	
	Parameters:
		attName - (String) The name of the attribute to set.
		attValue - (String) What to set the attribute to.
	
	Returns:
		(<XmlNode>) *this*, the parent object, so we can chain 
		               methods together. 
	*/
	public function setAtt(attName:String, attValue:String):XmlNode
	{
		this.xml.set(attName,attValue);
		return this;
	}
	
	//
	// Should probably switch this to use a HASH instead
	//
	public function getAttList():List<String>
	{
		var attIterator:Iterator<String>;
		var list:List<String>;
		
		list = new List();
		attIterator = this.xml.attributes();
		
		while (attIterator.hasNext())
		{
			list.add(attIterator.next());
		}
		
		return list;
	}
	
	//
	// Navigating
	//	WORKING! (except for the filtering... but that can wait)
	//
	
	public function getParent():XmlNode
	{
		var parent:XmlNode;
		parent = null;
		if (this.xml.parent != null)
		{
			parent = new XmlNode(this.xml.parent);
		}
		
		return parent;
	}
	
	public function getDocument():XmlNode
	{
		var elementToTest:Xml = this.xml;
		
		// go up through the XML parents until we reach a nodeType of Document
		while (elementToTest.parent != null && elementToTest.nodeType != Xml.Document)
		{
			elementToTest = elementToTest.parent;
		}
		
		// just check we did find one, if not, set to null
		var finalResult:Xml = (elementToTest.nodeType == Xml.Document) ? elementToTest : null;
		
		// Done
		return new XmlNode(finalResult);
	}
	
	/** Count the depth from the document.  Ie. An immediate child is 1, a granchild is 2 etc. */
	public function getDepthFromDocument():Int
	{
		var elementToTest:Xml = this.xml;
		var levelsDown:Int = 0;
		
		// go up through the XML parents until we reach a nodeType of Document
		while (elementToTest.parent != null && elementToTest.nodeType != Xml.Document)
		{
			elementToTest = elementToTest.parent;
			levelsDown++;
		}
		
		// Done
		return levelsDown;
	}
	
	public function getNthParent(n:Int):XmlNode
	{
		// Not optimized, but oh well...
		return this.ancestors.getAt(n);
	}
	
	public function getAncestors(?nameFilter:String = null, ?limit:Int = null):XmlList
	{
		var currentElm:XmlNode;
		var childList:XmlList;
		
		childList = new XmlList();
		currentElm = this;
		
		while (currentElm.parent != null)
		{
			currentElm = currentElm.parent;
			childList.add(currentElm);
		}
		
		return childList;
	}
	
	public function getChildren(?nameFilter:String = null, ?limit:Int = null):XmlList
	{
		var childList:XmlList;
		childList = new XmlList();
		
		if (this.isElement || this.isDocument)
		{
			for (childXml in this.xml)
			{
				var child = new XmlNode(childXml);
				childList.add(child);
				var name = (child.isElement) ? child.name : "";
			}
		}
		
		return childList;
	}
	
	public function getChildAt(index:Int)
	{
		return this.children.getAt(index);
	}
	
	public function getFirstChild():XmlNode
	{
		return new XmlNode(this.xml.firstChild());
	}
	
	public function getLastChild():XmlNode
	{
		//return new XmlNode(this.xml.lastChild());
		return this.children.getAt(this.numChildren);
	}
	
	/**
	Get all descendants as an XmlList.  
	
	By default this doesn't do any filtering.  
	You can run filters on the resulting XmlList though
	*/
	public function getDescendants():XmlList
	{
		// If this is null, this is the first level, 
		// subsequent levels of descendants should add to this list
		// as we'll pass it down when we recurse
		var descendantList = new XmlList();
		
		descendantList.add(this);
		if (this.isElement || this.isDocument)
		{
			for (child in this.getChildren())
			{
				descendantList.addList(child.getDescendants());
			}
		}
		
		return descendantList;
	}
	
	/**
	Get all Sibling nodes.  This doesn't do any filtering, but you can filter the resulting list though.
	*/
	public function getSiblings():XmlList
	{
		var childList:XmlList;
		var thisParent:XmlNode;
		
		childList = new XmlList();
		
		thisParent = this.parent; 
		if (thisParent != null)
		{
			childList.addList(this.getSiblingsBefore());
			childList.addList(this.getSiblingsAfter());
		}
		
		return childList;
	}
	
	// working
	public function getNext():XmlNode
	{
		var prevElm:XmlNode;
		
		prevElm = null;
		if (this.parent != null)
		{
			prevElm = this.parent.getChildAt(this.index + 1);
		}
		
		return prevElm;
	}
	
	// working
	public function getPrev():XmlNode
	{
		var prevElm:XmlNode;
		
		prevElm = null;
		if (this.parent != null)
		{
			prevElm = this.parent.getChildAt(this.index - 1);
		}
		
		return prevElm;
	}
	
	// working
	public function getSiblingsBefore():XmlList
	{
		var childList:XmlList;
		var thisParent:XmlNode;
		
		childList = new XmlList();
		
		thisParent = this.parent; 
		if (thisParent != null)
		{
			for (i in 1...(this.index))
			{
				childList.add(thisParent.getChildAt(i));
			}
		}
		
		return childList;
	}
	
	// working
	public function getSiblingsAfter():XmlList
	{
		var childList:XmlList;
		var thisParent:XmlNode;
		
		childList = new XmlList();
		
		thisParent = this.parent; 
		if (thisParent != null)
		{
			for (i in (this.index+1)...(this.parent.numChildren+1))
			{
				childList.add(thisParent.getChildAt(i));
			}
		}
		
		return childList;
	}
	
	/**
	Designed to be like Javascripts document.getElementById();
	
	Will find any child (or descendant) element with an attribute ID that matches.  
	Note this is different to javascript - this will not just give you the first result,
	but will give you all matching results.  If you just want the first one you can use
	
	myElement = getElementById("myID")[0];
	*/
	public function getElementsById(id_in:String):XmlList
	{
		return this.getDescendants().filterByAttribute("id",id_in);
	}
	
	public function getElementsByTagName(name_in:String):XmlList
	{
		return this.getDescendants().filterByTagName(name_in);
	}
	
	public function compareDocumentPosition(otherNode:XmlNode):XmlNodePosition
	{
		var positionOfOtherNode:XmlNodePosition;
		positionOfOtherNode = XmlNodePosition.DISCONNECTED;
		
		// check if they're siblings
		if (this.xml.parent == otherNode.xml.parent)
		{
			// check if it's before or after
			if (otherNode.index > this.index)
			{
				// lower index means this one comes before the otherNode, therefore
				positionOfOtherNode = XmlNodePosition.FOLLOWING;
			}
			else
			{
				// otherwise it's preceeding
				positionOfOtherNode = XmlNodePosition.PRECEDING;
			}
		}
		else
		{
			// check if one is parent/child ancestor/descendant
			if (isDescendantOf(otherNode))
			{
				// this is child of otherNode, otherNode contains this
				positionOfOtherNode = XmlNodePosition.CONTAINS;
			}
			else if (otherNode.isDescendantOf(this))
			{
				// otherNode is a child of this, it is contained by this
				positionOfOtherNode = XmlNodePosition.CONTAINED_BY;
			}
			else if (this.document.xml == otherNode.document.xml)
			{
				// last option, they're part of the same document
				// but in different sections of the tree
				
				// we'll find out which ancestor is at the same level, 
				// and then check which one is in front
				var isOtherNodeDeeperThanThis:Bool;
				var thisDepth:Int = this.depth;
				var otherDepth:Int = otherNode.depth;
				isOtherNodeDeeperThanThis = (otherDepth > thisDepth);
				
				if (isOtherNodeDeeperThanThis)
				{
					var diff:Int = otherDepth - thisDepth;
					var otherAncestorOfCommonDepth:XmlNode = otherNode.getNthParent(diff);
					positionOfOtherNode = this.compareDocumentPosition(otherAncestorOfCommonDepth);
				}
				else
				{
					var diff:Int = thisDepth - otherDepth;
					var thisAncestorOfCommonDepth:XmlNode = this.getNthParent(diff);
					positionOfOtherNode = thisAncestorOfCommonDepth.compareDocumentPosition(otherNode);
				}
			}
		}
		
		return positionOfOtherNode;
	}
	
	/**
	Test if this is a child of another node.  I'm using this in compareDocumentPosition, but I guess it could be useful for someone else...
	*/
	public function isDescendantOf(otherNode:XmlNode):Bool
	{
		var nodeToTest:Xml = this.xml.parent;
		var possibleAncestor:Xml = otherNode.xml;
		var isDescendant:Bool = false;
		
		// while the node we're on is not the possible ancestor, 
		// and while there's still a parent, switch to that
		while (!isDescendant && nodeToTest.parent != null)
		{
			isDescendant = (nodeToTest == possibleAncestor);
			nodeToTest = nodeToTest.parent;
		}
		
		return isDescendant;
	}
	
	//
	// Functions relating to strings
	//
	
	public function setInnerXML(str:String):XmlNode
	{
		this.innerXML = str;
		return this;
	}
	
	public function setOuterXML(str:String):XmlNode
	{
		this.outerXML = str;
		return this;
	}
	
	public function toString():String
	{
		return this.outerXML;
	}
	
	public function iterator():Iterator<XmlNode>
	{
		return this.getChildren().iterator();
	}
	
	//
	//
	//
	// PRIVATE METHODS
	//  mostly setter and getter stuff
	//
	//
	//
	
	function get_xml():Xml
	{
		// make sure this.x isn't null
		this.x = (this.x != null) ? this.x : Xml.parse('<empty />'); 
		
		// return the Xml object
		return this.x;
	}
	
	function get_fast():Fast
	{
		// make sure this.f isn't null
		this.f = (this.f != null) ? this.f : new Fast(this.xml);
		
		// return the Fast object
		return this.f;
	}
	
	function get_name():String
	{
		return (this.isElement) ? this.xml.nodeName : "#" + Std.string(this.type);
	}
	
	function set_name(newName:String):String
	{
		this.xml.nodeName = newName;
		return this.xml.nodeName;
	}
	
	function get_type():XmlType
	{
		return this.xml.nodeType;
	}
	
		// very simple functions to test if it is a certain type
		function get_isCData():Bool { return (this.xml.nodeType == Xml.CData); }
		function get_isComment():Bool { return (this.xml.nodeType == Xml.Comment); }
		function get_isDocType():Bool { return (this.xml.nodeType == Xml.DocType); }
		function get_isDocument():Bool { return (this.xml.nodeType == Xml.Document); }
		function get_isElement():Bool { return (this.xml.nodeType == Xml.Element); }
		function get_isPCData():Bool { return (this.xml.nodeType == Xml.PCData); }
		//function get_isProlog():Bool { return (this.xml.nodeType == Xml.Prolog); }
		function get_isText() { return (this.isCData || this.isPCData); }
	
	function get_value():String
	{
		var v:String;
		v = (this.isElement || this.isDocument) ? "" : this.xml.nodeValue;
		return v;
	}
	
	function set_value(v:String):String
	{
		var returnVal = "";
		
		if (!this.isElement && !this.isDocument)
		{
			this.xml.nodeValue = v;
			returnVal = v;
		}
		
		return returnVal;
	}
	
	function get_parent():XmlNode
	{
		return this.getParent();
	}
	
	function get_document():XmlNode
	{
		return this.getDocument();
	}
	
	function get_depth():Int
	{
		return this.getDepthFromDocument();
	}
	
	function get_children():XmlList
	{
		return this.getChildren();
	}
	
	function get_firstChild():XmlNode
	{
		return this.getFirstChild();
	}
	
	function get_lastChild():XmlNode
	{
		return this.getLastChild();
	}
	
	function get_ancestors():XmlList
	{
		return this.getAncestors();
	}
	
	function get_descendants():XmlList
	{
		return this.getDescendants();
	}
	
	function get_siblings():XmlList
	{
		return this.getSiblings();
	}
	
	function get_next():XmlNode
	{
		return this.getNext();
	}
	
	function get_prev():XmlNode
	{
		return this.getPrev();
	}
	
	function get_siblingsBefore():XmlList
	{
		return this.getSiblingsBefore();
	}
	
	function get_siblingsAfter():XmlList
	{
		return this.getSiblingsAfter();
	}
	
	function get_index():Int
	{
		// get parent -> get list of children -> getIndexOf(this)
		var index:Int;
		var parent:XmlNode;
		
		index = 0;
		if (this.parent != null)
		{
			index = this.parent.getChildren().indexOf(this);
		}
		
		return index;
	}
	
	function get_numChildren():Int
	{
		return Lambda.count(this.xml);	
	}
	
	function get_innerXML():String
	{
		return this.fast.innerHTML;
	}
	
	function set_innerText(str:String):String
	{
		return this.set_innerXML(str);
	}

	function set_innerXML(str:String):String
	{
		var newXml:XmlNode;
		
		// empty the current children out
		this.empty();
		
		// parse string, add child at 0
		newXml = new XmlNode(str);
		this.addChildAt(newXml,0);
		
		// addChild should take care of getting rid of the document element etc...
		return str;
	}
	
	function get_outerXML():String
	{
		return this.xml.toString(); 	
	}
	
	//
	// Please note that after using this, you will have to re-find your variables
	// eg. bodyElm.outerXML = "<hey>";
	// bodyElm will currently be null, but <hey> is in it's place.
	// This is because we don't know how many objects this will return, it might not even exist...
	// 
	//
	function set_outerXML(str:String):String
	{
		// Parse and create a new XmlNode object (whether it's a single element or many, it will be wrapped in a document node)
		// newElement.addThisTo(this.parent, this.index);
		// parent.removeChild(this);
		
		if (this.parent == null)
		{
			// This is creating a new document, probably from new XmlNode();
			// What this means
			//   - We just parse the XML, and the document node we produce is all good
			
			this.x = Xml.parse(str);
		}
		else
		{
			// This is changing the outerXML of an element in a document
			// What that means:
			//   - We need to filter out any document elements
			//   - We need to append the new content to the parent, in the old ones place
			
			var newXml:XmlNode;
			var parent:XmlNode;
			var index:Int;
			
			parent = this.parent;
			index = this.index;
			
			// Parse the new data into an object
			newXml = new XmlNode(str);
			
			// Add the new data in place of this one
			newXml.addThisTo(parent, index);
			
			// Delete this one
			parent.removeChild(this);
		}
		return str;
	}
	
	function get_innerText():String
	{
		var allDescendants:XmlList;
		var textDescendants:XmlList;
		var s:StringBuf;
		
		
		allDescendants = this.descendants;
		
		//descendants = listPCData.add(
		
		textDescendants = allDescendants.filterByFunction(function(x:XmlNode)
		{
			return x.isText || x.isPCData;
		});
		
		
		var s = new StringBuf();
		for (textNode in textDescendants)
		{
			s.add(textNode.toString());
		}
		
		
		return textDescendants.join(" ");	
	}
	
	
}

/**
XmlNodePosition is used to describe the relationship between two nodes.  
It is used by XmlNode.compareDocumentPosition()

Please note haxedoc seems to have screwed up the order of the descriptions below.
*/
enum XmlNodePosition
{
	DISCONNECTED; /** The elements either don't belong to the same document or MAYBE are in unrelated parts of the DOM tree */
	PRECEDING; /** The elements are siblings, and this element we're testing against comes after */
	FOLLOWING; /** The elements are siblings, and the element we're testing against comes first */
	CONTAINS; /** The element we're testing against is a parent of this element */
	CONTAINED_BY; /** The element we're testing against is a descendant of this element */
}
