<pre>

You'll notice this can get quite slow quite quickly.
We'll want to limit it to only dig a certain number of directories deep each time.
We'll also want to cache a copy on the server, I think the FTP calls legitimately slow it down.

And then there's the question, is it better to get one directory list at a time, and have it load fast
Or to pre cache a few deep?

<?php
	$connection = ftp_connect("localhost");
	$isLoginOkay = @ftp_login($connection,"jason","1Cor13") ? 'true' : 'false';
	echo "Connected and logged in? " . $isLoginOkay;
	echo "\n" . "Current working directory: " . ftp_pwd($connection);
	$limit = 1;
	printDirectory("/WebRoot/winthrop/");
	

	function printDirectory($dir, $gap = "")
	{
		global $connection;
		global $limit; echo $limit;
		if (strlen($gap) > $limit) { return false; }

		$fileList = ftp_nlist($connection, $dir);
		foreach ($fileList as $id=>$filename)
		{
			echo "\n" . $gap . $id . " - " . $filename;
			if (ftp_is_dir($filename))
			{
				printDirectory($filename, $gap . "=");
			}
		}
		
		
	}

	function ftp_is_dir($file) 
	{
		global $connection;
		if (@ftp_chdir($connection, $file)) 
		{
			ftp_chdir($connection, '/');
			return true;
		} 
		else 
		{
			return false;
		}
	}
?>
</pre>
