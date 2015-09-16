/*
Where I'm up to:

- imports a template from a file, changes it into an XML object
- cycles through the XML tree, finding important types and ignoring others
- finds all occurances of {varname}

Next

- uncomment the string outputs in the function
- after the for(everything but blocks) loop, go through and
- do a for(only the blocks, and only at this level) loop that
     - captures the OuterHTML, sends it through the function
     - Deletes the entire block and replaces it with the innerHTML
       (essentially removing the container element)
- implement the class stuff from pseudocode, including
     - varName array
     - varValue array
     - some sort of block mechanism
     - assign()
     - newLoop()
     - include()
- change the digThroughXML function so that all the "replace variable" and "new block" actions etc. are in seperate functions
- make the assign function work
- make the digThroughXML function actually replace the variables used in assign();
*/

/***********************************************************************
   Class: HxTpl
   Package: jasononeil.tpl   
   
   A very simple templating class that processes live using any string, 
   rather than requiring pre-processing.  This would allow you to load 
   templates from a database, a file, or even user input.
***********************************************************************/


package hxbase.tpl;

import php.Lib;
import sys.io.File;
import haxe.xml.Fast;
import StringTools;

import hxbase.xml.XmlNode;
import hxbase.xml.XmlList;

class HxTpl
{
	private var templateString:String;
	private var templateXML:XmlNode;
	private var ready:Bool;
	private var output:String;
	private var assignedVariables:Map<String, String>;
	
	private var switches:Map<String, Bool>;
	private var loopCount:Map<String, Int>;
	private var includeURLs:Map<String, String>;
	
	//
	// Switches, Loops and Includes all will occupy the same namespace
	// And so they are all kept in this same hash object.
	//
	// The name of the switches and includes remain the same.
	// The name of the loop becomes "loopName:1", "loopName:2" etc.
	//
	// Also worth noting, these blocks can be declared willy nilly, without
	// having to track down what data the template block contains, or even if
	// it exists.
	// 
	private var blocks:Map<String, HxTpl>;
	
	/***********************************************************************
	Constructor: new
	Initializes the template object.
	***********************************************************************/
	/**
	 * Constructor. 
	 * 
	 * <p>Initializes the template object</p>
	 */
	
	public function new()
	{
		assignedVariables = new Map();
		blocks = new Map();
		switches = new Map();
		loopCount = new Map();
		includeURLs = new Map();
		
		this.assignObject('this', 
		{
			#if neko
			URL : neko.Web.getURI()
			#elseif php
			URL : php.Web.getURI()
			#end
		});
		
		ready = false;
	}
	
	/***********************************************************************
	Function: loadTemplateFromString
	************************************************************************
	
	Sets the template to be used for this object, based on a string.  
	This could be based off an included file, a template from a database, 
	a block of an existing template, or user inputted data.  Any valid XML will do.
	
	Parameters:
		tpl:String - The string to use as a template. 
		
	Returns:
		(Bool) Should: True if the template is a valid XML String, 
		  False otherwise.  Reality: Always returns true.
		
	***********************************************************************/
	
	public function loadTemplateFromString(tpl:String):Bool
	{
		templateString = tpl;
		templateXML = new XmlNode(templateString);
		
		ready = true; // really need to put some sort of checking here...
		
		return ready;
	}
	
	/***********************************************************************
	Function: loadTemplateFromFile
	************************************************************************
	
	Loads a template for this object from a file.  
	
	Parameters:
		url:String - The url or path to the root directory for this filesystem
		
	Returns:
		(Bool) Should: True if the template is valid, False otherwise.  
		  Reality: Always returns true.
		
	***********************************************************************/
	
	public function loadTemplateFromFile(url:String):Bool
	{
		ready = false;
		
		templateString = sys.io.File.getContent(url);
		loadTemplateFromString(templateString);
		
		ready = true;
		
		return ready;
	}
	
	/***********************************************************************
	Function: getOutput
	************************************************************************
	Processes the template and returns a string with the full XML output.
	
	Please note this does not actually print the template to the browser etc. 
	It is up to the class calling the template to take this output and do that.
		
	Returns:
		(String) Full XML Output, the end product of the template
	***********************************************************************/
	
	public function getOutput()
	{
		output = processXML(templateXML);
		//return output;
		return templateXML.outerXML;
	}
	
	/***********************************************************************
	Function: assign
	************************************************************************
	Assign a simple variable name and value.
	An example might be helpful.  If we call
	
	eg - 	my_tpl.assign("title", "Cheesecakes");
	
	Then in the template, any reference to either
	
	eg - 	{title} OR <hxVar name="title">Default Title</hxVar>
	
	Will be replaced with "Cheesecakes".  Simple, no?  
	
	Also, because this object returns itself, statements can be 
	chained together.  
	
	eg -	my_tpl.assign("page", "Five Fruits").assign("url","fruit.html");
	
	Parameters:
		name:String - The name of the template variable we are assigning.
		  You can use Uppercase and Lowercase letters, numbers and 
		  decimal points.
		
		value:String - The text to replace it with.  
		
		useHTMLEncode:Bool - Do we encode this string for HTML?  
		  If *true*, all & < > etc. will be turned into HTML entities.
		  Defaults is *true*
		
	Returns:
		(HxTpl) Returns this template object, so we can chain together 
		assign statements.
	***********************************************************************/
	
	public function assign(name:String, value:String, ?useHTMLEncode:Bool = true):HxTpl
	{
		// do we add the HTML escaping?
		value = (useHTMLEncode) ? StringTools.htmlEscape(value) : value;
		
		assignedVariables.set(name, value);
		return this;
	}
	
	/***********************************************************************
	Function: assignObject
	************************************************************************
	Assign a simple object containing name value pairs to a template variable.
	An example might be helpful.  If we call
	
	> tpl.assignObject('page', 
	>	{
	>		title		:'New Website',
	>		url		:'http://google.com/myhouse.html'
	>	});
	
	Then they will correspond with atemplate like this -
	
	> title: {page.title}
	> url:   {page.url}
	
	You can also go multiple levels deep.
	
	> tpl.assignObject('page', 
	>	{
	>		url		:'http://google.com/myhouse.html',
	>		urlParts	:
	>				{
	>				protocol : 'http://',
	>				domain : 'google.com',
	>				filename : 'myhouse',
	>				extension : '.html'
	>				}
	>	});
	
	And then access that with
	
	> {page.urlParts.protocol}, {page.urlParts.domain} etc.
	
	As with assign(), assignObject() returns the template object, so you can
	chain together assign commands.
	
	Parameters:
		name:String - The name of the template variable we are assigning.
		  You can use Uppercase and Lowercase letters, numbers and 
		  decimal points.
		
		obj:Dynamic - An object consisting of name:value pairs, possibly
		  multiple levels deep. 
		
		useHTMLEncode:Bool - Do we encode this string for HTML?  
		  If *true*, all & < > etc. will be turned into HTML entities.
		  Defaults is *true*
		
	Returns:
		(HxTpl) Returns this template object, so we can chain together 
		assign statements.
	***********************************************************************/
	
	public function assignObject(name:String, obj:Dynamic, ?useHTMLEncode:Bool = true):HxTpl
	{
		// for i=propertyName in obj
		for (propName in Reflect.fields(obj))
		{
			var propValue = Reflect.field(obj, propName);
			
			// For the moment we'll just check for strings and objects containing strings
			// In the future it could be useful to take any object and see if we can't convert it to a string
			
			if (Reflect.isObject(propValue) && !Std.is(propValue, String))
			{
				// this is an object, possibly containing more name/value pairs.
				// Recursively call this function to search through all children
				assignObject(name + "." + propName, propValue, useHTMLEncode);
			}
			else
			{
				// if it's not a string,make it one!
				if (!Std.is(propValue, String)) 
					propValue = Std.string(propValue);
				
				// we have a name and a value to assign
				this.assign(name + "." + propName, propValue, useHTMLEncode);
			}
		}
		
		return this;
	}
	
	/***********************************************************************
	Function: setSwitch
	************************************************************************
	
	Define whether a switch will be visible or not.  This returns the block
	inside the switch, so you can assign variables to that block.
	
	Example:
	
	We'll start with this template.
	
	> <h1>Result:</h1>
	> 
	> <p>{message}</p>
	>
	> <hxSwitch name="error">
	>    <p><b>Error:</b> {message}</p>
	> </hxSwitch>
	>
	> <hxSwitch name="theHiddenSection">
	>    The secret of life is something about the number 42
	> </hxSwitch>
	>
	
	Now we run this script.
	
	> var tpl:HxTpl;	// pretend we've loaded the template above
	> tpl.assign("message", "Notice that this message is only affecting the current block, not the block underneath");
	> 
	> var errBlock:HxTpl;
	> errBlock = tpl.setSwitch("error", true);
	> errBlock.assign("message", "The problem is that you're too cool for school.");
	>
	> // if we uncommented the next line, it would show "theHiddenSection"
	> // tpl.setSwitch("theHiddenSection", true);
	
	And it produces this output
	
	> <h1>Result:</h1>
	> 
	> <p>Notice that this message is only affecting the current block, not the block underneath</p>
	>
	> 
	>    <p><b>Error:</b> The problem is that you're too cool for school.</p>
	> 
	
	Parameters:
		name:String - The name of the template switch that we're 
		  looking for.
		
		value:Bool - True to show the switch, false to hide it.  
		
	Returns:
		(HxTpl) Returns a template object of the switch template block, 
		so we can assign variables specifically for the switch.
	***********************************************************************/
	
	public function setSwitch(name:String, ?value:Bool = true):HxTpl
	{
		// set the value in the hash
		switches.set(name, value);
		
		// create a HxTpl object if one doesn't exist, or get an existing one
		var switchBlock:HxTpl;
		switchBlock = this.getBlock(name);
		
		// return it
		return switchBlock;
	}
	
	/***********************************************************************
	Function: newLoop
	************************************************************************
	
	Create a new instance of a template loop (hxLoop).  No examples yet 
	sorry, though it should work mostly as you'd expect.
	
	Parameters:
		name:String - The name of the template loop that we're 
		  looking for.
		
	Returns:
		(HxTpl) Returns a template object of the loop template block, 
		so we can assign variables specifically for this instance
		of the loop.
	***********************************************************************/
	
	public function newLoop(name:String):HxTpl
	{
		// get the number of loops so far;
		var i:Int;
		i = 0;
		if (loopCount.exists(name)) 
			{ i = loopCount.get(name); }
		
		// increment the loop count
		i++;
		loopCount.set(name, i);
		
		// create a HxTpl object if one doesn't exist, or get an existing one
		var loopBlock:HxTpl;
		loopBlock = this.getBlock(name + ":" + i);
		
		// return it
		return loopBlock;
	}
	
	/***********************************************************************
	Function: useListInLoop
	************************************************************************
	
	This is a shortcut to get a list of objects to each have their own 
	template block.
	
	It essentially is just running this code:
	
	> for (obj in list)
	> {
	> 	loopTpl = this.newLoop(loopName);
	> 	loopTpl.assignObject(varName, obj, useHTMLEncode);
	> }
	
	Parameters:
		list:List - A list of objects containing variables to be 
		            assigned.  A new loop will be created for every 
		            item in the list.
		loopName:String - The name of the template loop (hxLoop) to use.
		varName:String - The name of the template variable that the 
		                 object should be assigned to.
		useHTMLEncode:Bool - Use HTML Encoding on the data?
		
	Returns:
		(nothing yet)
	***********************************************************************/
	
	public function useListInLoop(list:List<Dynamic>, loopName:String, varName:String, ?useHTMLEncode:Bool = true)
	{
		var loopTpl:HxTpl;
		
		for (obj in list)
		{
			loopTpl = this.newLoop(loopName);
			loopTpl.assignObject(varName, obj, useHTMLEncode);
		}
	}
	
	//
	// need to make it so that the URL is relative to the script
	//
	public function include(name:String, ?url:String = ""):HxTpl
	{
		// create a HxTpl object if one doesn't exist, or get an existing one
		var includeBlock:HxTpl;
		includeBlock = this.getBlock(name);
		
		// Set the URL in our hash of include URLs
		includeURLs.set(name, url);
		
		// return it
		return includeBlock;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//
	// This function will probably replace exportTemplate directly (wait... this is recursive.  exportTemplate should be available from outside)
	// Currently it cycles through all the elements recursively, and stops if it gets to a block level element
	//
	private function processXML(xmlElement:XmlNode):String  // later add attribute tplVariables
	{
		var list:XmlList;
		var string:String = "";
		
		
		// Go through every item in the XML, elements, PCData, Comments, all of it
		for (childElm in xmlElement)
		{	
			if (childElm.isElement || childElm.isDocument)
			{
				// if it is an element or document type
				string = processXML_element(childElm);
			}
			else
			{
				// or if it is text data, comments etc...
				childElm.value = processXML_textnode(childElm.value);
			}
		}
		
		
		
		return string;
	}
	
	private function processXML_element(elm:XmlNode)
	{
		var string:String = "";
				
		// if it is any element other than a block
		if (elm.isDocument || (elm.name != 'hxVar' && elm.name != 'hxSwitch' && elm.name != 'hxLoop' && elm.name != 'hxInclude'))
		{
			// process this as a regular (non-template-specific) xml element
			string = processXML_element_regularElement(elm);
		}
		else if (elm.name == 'hxVar')
		{
			string = processXML_element_hxVar(elm);
		}
		else
		// if it is a block
		{
			// process this as a template block
			processXML_element_templateBlock(elm);
		}
		
		return string;
		
	}
	
	private function processXML_element_regularElement(elm:XmlNode):String
	{
		
		var string:String;
		string = "";
		
		// Check if there's any {squigly.bracket.variables} in the attributes (attribute values only at the moment, not attribute names)
		for (attName in elm.getAttList())
		{
			var oldValue:String;
			
			oldValue = elm.getAtt(attName);
			if (oldValue.indexOf('{') != -1)
			{
				// squigly bracket found, process to search for variable
				elm.setAtt(attName, processXML_textnode(oldValue));
			}
		}
		
		// Loop through the children (if any)
		string += processXML(elm);
		
		return string;
	}
	
	private function processXML_element_hxVar(elm:XmlNode):String
	{
		var varName:String;
		var varValue:Dynamic;
		var replacementElements:Iterator<Xml>;
		
		// Check the node name attribute 
		varName = elm.getAtt("name");
		
		// if a variable has been assigned to this varName
		if ((varName != null) && assignedVariables.exists(varName))
		{
			varValue = assignedVariables.get(varName);
			
			// delete this node, replace it with varValue (probably just text)
			elm.outerXML = varValue;
		}
		else
		{
			varValue = elm.getChildren().toString(); // replace it with it's current children
			
			// delete this node, replace it with varValue (probably a list of elements)
			elm.outerXML = varValue;
		}
		
		return varValue;
	}
	
	private function processXML_element_templateBlock(elm:XmlNode):String
	{
		var string:String;
		string = "";
		
		
		// replace this block with the html output of it's template object
		
		var newXML:String;
		var blockName:String;
		
		newXML = "";
		blockName = elm.getAtt('name');
		
		switch (elm.name)
		{
			case "hxSwitch":
				newXML = processXML_element_templateBlock_hxSwitch(elm,blockName);
			case "hxLoop":
				 newXML = processXML_element_templateBlock_hxLoop(elm,blockName);
			case "hxInclude":
				newXML = processXML_element_templateBlock_hxInclude(elm,blockName);
		}
		
		elm.outerXML = newXML;
		
		return string;
	}
	
	private function processXML_element_templateBlock_hxSwitch(elm_in:XmlNode, blockName:String):String
	{
		var newXML:String;
		var blockTpl:HxTpl;
		
		newXML = "";
		
		if (switches.exists(blockName) && switches.get(blockName) == true)
		{
			// we're showing this switch
			blockTpl = this.getBlock(blockName);
			blockTpl.loadTemplateFromString(elm_in.innerXML);
			newXML = blockTpl.getOutput();
		}
		
		return newXML;
	}
	
	private function processXML_element_templateBlock_hxLoop(elm_in:XmlNode, blockName:String):String
	{
		var newXML:String;
		var blockTpl:HxTpl;
		
		newXML = "";
		
		if (loopCount.exists(blockName))
		{
			// check how many iterations of the loop there are
			var count:Int;
			count = loopCount.get(blockName);
			
			if (count > 0)
			{
				// at least one, make the template for each of them
				var blockTemplateXML:String;
				blockTemplateXML = elm_in.innerXML;
				
				for (i in 1 ... (count + 1))
				{
					blockTpl = this.getBlock(blockName + ":" + i);
					blockTpl.loadTemplateFromString(blockTemplateXML);
					newXML += blockTpl.getOutput();
				}
			}
		}
		
		return newXML;
	}
	
	private function processXML_element_templateBlock_hxInclude(elm_in:XmlNode, blockName:String):String
	{
		var newXML:String;
		var blockTpl:HxTpl;
		
		newXML = "";
		
		// find the URL
		var url:String;
		url = "";
		if (includeURLs.exists(blockName) && includeURLs.get(blockName) != "")
		{
			// the script has set an include URL, use that
			url = includeURLs.get(blockName);
			
			//************
			// Make it so the URL is relative to the script? or the templates dir?
			//************
		}
		else if (elm_in.hasAtt('url') && elm_in.getAtt('url') != "")
		{
			url = elm_in.getAtt('url');
			//************
			// Make it so the URL is relative to the closest template loaded from a file
			//************
		}
		
		if (url != "")
		{
			// load that URL
			blockTpl = this.getBlock(blockName);
			blockTpl.loadTemplateFromFile(url);
			newXML += blockTpl.getOutput();
		}
		
		return newXML;
	}
	
	private function processXML_textnode(str_in:String):String
	{
		var str_out:String;
		str_out = "";
		
		// match {string:With:Colons}
		var r:EReg = ~/{([A-Za-z0-9]+[.A-Za-z0-9]*)}/;
		while (r.match(str_in))
		{
			var varName = r.matched(1);
			var varValue = (assignedVariables.exists(varName)) ? assignedVariables.get(varName) : "";
			str_in = r.replace(str_in, varValue);
		}
		
		str_out = str_in;
		
		return str_out;
	}
	
	private function getBlock(name:String):HxTpl
	{
		// create a HxTpl object if one doesn't exist, or get an existing one
		var newBlock:HxTpl;
		
		if (blocks.exists(name))
		{
			// get the existing block
			newBlock = blocks.get(name);
		}
		else
		{
			// create a new block
			newBlock = new HxTpl();
			
			// Save it for future reference
			blocks.set(name, newBlock);
		}
		
		// return it
		return newBlock;
	}
} 
