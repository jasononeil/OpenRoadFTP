import php.FileSystem;
//import hxbase.wfs.WebFile;
import hxbase.wfs.WebFileSystem;
import hxbase.tpl.HxTpl;


class Hello 
{
	private var tpl:HxTpl;
	
	static function main() 
    	{
    		var files:Array<String>;
		//files = php.FileSystem.readDirectory('/home/jason/');
		
		var tpl:HxTpl;
		tpl = new HxTpl();
		tpl.loadTemplateFromFile('./tpl/test.hxtpl');
		tpl.assign("title", "The Title is Being Set Dynamically!!!!");
		tpl.assign("subtitle", "And the subtitle too, showing that we can do many variables");
		
		tpl.assignObject('page', 
		{
			title		:'New Website',
			url		:'http://google.com/myhouse.html',
			urlParts	:
					{
					protocol : 'http://',
					domain : 'google.com',
					filename : 'myhouse',
					extension : '.html',
					size : 2
					}
		});
		
		var errBlock:HxTpl;
		
		errBlock = tpl.setSwitch('error', true);
		errBlock.assign("message", "NOT SO EPIC FAIL!");
		
		var m1:HxTpl;
		var m2:HxTpl;
		
		m1 = tpl.newLoop("menuItem");
		m1.assignObject("page", {
			url : 'http://slashdot.org',
			name : 'A big waste of time'
		});
		
		m2 = tpl.newLoop("menuItem");
		m2.assignObject("page", {
			url : 'http://www.wbc.wa.edu.au',
			name : 'One of my employers'
		});
		
		var content:HxTpl;
		var copyright:HxTpl;
		
		copyright = tpl.include('copyright');
		copyright.assign('year', '2009');
		
		content = tpl.include('content', './tpl/content.hxtpl');
		content.assign('pageTitle', 'The Blue Header');
		
		php.Lib.print(tpl.getOutput());
		
		
		
		/*
		for (i in 0...files.length)
		{
			trace ("<br />files[i] where i=" + i + " => " + files[i]);
		}
		*/
		
		
    	}
	
    	
    	
    	
	/*
	Function: multiply
	Multiplies two integers and returns the result.
	*/
	function multiply()
	{
	 	 trace ("Test this out!");	    
	}
}
