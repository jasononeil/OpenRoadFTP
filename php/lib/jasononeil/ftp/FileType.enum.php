<?php

class jasononeil_ftp_FileType extends Enum {
		public static $dir;
		public static $file;
		public static $link;
		public static $unknown;
	}
	jasononeil_ftp_FileType::$dir = new jasononeil_ftp_FileType("dir", 0);
	jasononeil_ftp_FileType::$file = new jasononeil_ftp_FileType("file", 1);
	jasononeil_ftp_FileType::$link = new jasononeil_ftp_FileType("link", 2);
	jasononeil_ftp_FileType::$unknown = new jasononeil_ftp_FileType("unknown", 3);
