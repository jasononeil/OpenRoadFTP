<?php

class ftp_FtpFileType extends Enum {
	public static $dir;
	public static $file;
	public static $link;
	public static $unknown;
	public static $__constructors = array(0 => 'dir', 1 => 'file', 2 => 'link', 3 => 'unknown');
	}
ftp_FtpFileType::$dir = new ftp_FtpFileType("dir", 0);
ftp_FtpFileType::$file = new ftp_FtpFileType("file", 1);
ftp_FtpFileType::$link = new ftp_FtpFileType("link", 2);
ftp_FtpFileType::$unknown = new ftp_FtpFileType("unknown", 3);
