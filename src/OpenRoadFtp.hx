import hxbase.tpl.HxTpl;

import ftp.FtpConnection;
import ftp.FtpItem;
import ftp.FtpFileList;
import hxbase.session.SessionHandler;
import hxbase.util.Error;

class OpenRoadFtp
{
	static function main() 
    	{	
		var tpl:HxTpl;
		var session:SessionHandler;
		
		tpl = new HxTpl();
		tpl.loadTemplateFromFile('./tpl/index.hxtpl');
		tpl.assign("site.title", "WBC Student Server 1");
		
		tpl.assignObject("site", {
			title : "OpenRoad FTP",
			subtitle : "Student Logon",
			copyright : "Created 2010 Jason O'Neil.  Released under a GPL license."
		});
		
		php.Lib.print('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
		php.Lib.print(tpl.getOutput());
		
		
		new Error('This is an error in the main body');
    	}
}
