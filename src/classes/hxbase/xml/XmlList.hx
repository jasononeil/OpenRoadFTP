/**
A flat list of XmlNode objects.  Basically just List<XmlNode>, but with
the same functions as XmlNode, which will now apply to all items in the
list.  Pretty neato hey!
***********************************************************************/

package hxbase.xml;
import hxbase.xml.XmlNode;

class XmlList extends List<XmlNode>
{
	/**
	New Constructor that allows us to create an empty list, create one with one XmlNode element, or clone an existing list
	*/
	public override function new(?node:XmlNode = null, ?list:XmlList = null)
	{
		super();
		if (node != null)
		{
			this.add(node);
		}
		
		if (list != null)
		{
			for (n in list)
			{
				this.add(n);
			}
		}
	}
	
	/**
	Append an existing XmlList to this XmlList.
	
	Parameters:
		The child object to add. 
	
	Returns:
		<b>this</b>, the parent object, so we can chain 
		               methods together. 
	*/
	// This function can be used to add a List<XmlNode> object and convert it to this XmlList object.  I wish Haxe knew to do that (eg. with the filter() function)
	public function addList(listToAdd:List<XmlNode>):XmlList
	{
		for (item in listToAdd)
		{
			this.add(item);
		}
		return this;
	}
	
	/**
	Filter this list by a function.  
	The function should take an XmlNode and return true/false.
	*/
	public function filterByFunction(f : XmlNode -> Bool ):XmlList 
	{
		var list2:XmlList;
		list2 = new XmlList();
		list2.addList(super.filter(f));
		return list2;
	}
	
	/**
	Return a subset of this list containing only nodes that have the matching attribute
	
	You could possibly do regexp matches here, just check for a string that's "~/something/"
	*/
	public function filterByAttribute(attName:String, attValue:String):XmlList
	{
		return filterByFunction(function (n:XmlNode):Bool {
			// return true if it is an element, has the attribute, and the attribute has the right value
			return (n.isElement && n.hasAtt(attName) && n.getAtt(attName) == attValue);
		});
	}
	
	/**
	Return a subset of this list containing only elements with this name
	*/
	public function filterByTagName(tagName:String)
	{
		return filterByFunction(function (n:XmlNode):Bool {
			// return true if it is an element, and it's name is right
			return (n.isElement && n.name == tagName);
		});
	}
	
	//
	// Functions for indexing
	//	Need to find out - is it more effecient to extend a list or base it on an array?
	//
	
	/**
	Get the XmlNode at a specific index (0 based, I believe)
	*/
	public function getAt(index:Int):XmlNode
	{
		var iter:Iterator<XmlNode>;
		var xml:XmlNode;
		var i:Int;
		
		iter = this.iterator();
		xml = null;
		i = 1;
		while (i <= index && iter.hasNext())
		{
			if (i == index)
			{
				xml = iter.next();
			}
			else
			{
				iter.next();
			}
			
			i++;
		}
		
		return xml;
	}
	
	//
	// Please note this may be imperfect!
	// See how we're testing it...
	//
	public function indexOf(childToSearchFor:XmlNode):Int
	{
		var iter:Iterator<XmlNode>;
		var foundItem:Bool;
		var itemIndex:Int;
		var i:Int;
		
		i=0;
		itemIndex = 0;
		foundItem = false;
		iter = this.iterator();
		
		while (itemIndex==0 && iter.hasNext())
		{
			i++;
			var currentChild:XmlNode = iter.next();
			
			//
			// What's going on here:
			// PHP's "==" operator compares all the properties for equality, rather than comparing the memory address.
			// We want to compare the memory address, but Haxe won't let us send out a "===" operator by default.  This
			// works around that.  
			// I need to test this on other platforms, particularly Javascript, to see if this works.
			// I've got a test case in XmlTest.hx that does has two identical children and searches for the second one.  That should work...
			//
			
			#if php
				untyped __php__("if($currentChild->get_xml() === $childToSearchFor->get_xml()) { $itemIndex = $i; }");
			#else
				if (currentChild.xml == childToSearchFor.xml)
				{
					itemIndex = i;
				}
			#end
			
		}
		
		return itemIndex;
	}
	
	//----------------------------------------------------------------------
	//
	// These functions are all copies of XmlNode
	// You can run them and they will apply to all the child objects
	// Designed so we can work kind of like jQuery
	//
	//----------------------------------------------------------------------
	
	
	
	
	
	
	
	
	
	//----------------------------------------------------------------------
	//
	// Finish implementing all the functions of AdvXml
	//
	//----------------------------------------------------------------------
	
	
	// return a complete string of all these toString()
	// see Fast.getInnerHTML() for how to implement
	public override function toString():String
	{
		var s = new StringBuf();
		for(child in this)
			s.add(child.toString());
		return s.toString();
	}
	
	
	
	
}
