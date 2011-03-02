<?php
# PHP File Uploader with progress bar - JSON version
# Based on progress.php, a contrib to Megaupload, by Mike Hodgson.
# Changed for use with AJAX by Tomas Larsson - http://tomas.epineer.se/

# Modified heavily by Jeremy Nicoll for use with JSON, and also added 
# code so that the uploaded files will actually WORK like they are 
# supposed to when you upload them.  Files get their original file name
# once uploaded. Added section so that potentially harmful files could 
# not be uploaded.

# Go to www.SeeMySites.net/forum for questions and support.

# Licence:
# The contents of this file are subject to the Mozilla Public
# License Version 1.1 (the "License"); you may not use this file
# except in compliance with the License. You may obtain a copy of
# the License at http://www.mozilla.org/MPL/
# 
# Software distributed under this License is distributed on an "AS
# IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
# implied. See the License for the specific language governing
# rights and limitations under the License.
# 

// Configurable variables:
$tempFolder = "tempfiles";       //Make sure that this is the same as in upload.cgi.
$moveToFolder = "uploadedfiles"; //Where the files will be moved upon upload, leave blank if you want it to be the same as $tempFolder.
$bad_files = array('exe', 'php', 'php3', 'php4', 'ph3', 'ph4', 'perl', 'cgi', 'bin', 'scr', 'bat', 'pif', 'aps', 'ssi', 'swf', 'js');

// End Configurable variables

error_reporting(E_ERROR | E_WARNING | E_PARSE);
require('JSON.php');
header('Content-Type: text/plain'); 
if (trim($moveToFolder) == '') {$moveToFolder = $tempFolder;}


$json = new Services_JSON;
$request = $json->decode($GLOBALS['HTTP_RAW_POST_DATA']);

$sessionID = $request->sid;;
$fileName = $request->fileName;

$temp = substr($fileName, strrpos($fileName, '.')+1);


$info_file = "$tempFolder/$sessionID"."_flength";
$data_file = "$tempFolder/$sessionID"."_postdata";
$error_file = "$tempFolder/$sessionID"."_err";

$files = array("_flength","_postdata","_err");

if (in_array($temp, $bad_files) && !file_exists($error_file)) {
  $request->status = 'error';
  $request->error_msg = 'Bad file extension: ' . $temp . '.  Please try uploading another file.';
  echo $json->encode($request);
  foreach($files as $file) {
	  @unlink("$tempFolder/$sessionID$file");
	}
  die;
}
// Removes files in the upload directory that are over 3 hours old, except for index.php
// You probably don't need it, but it might be nice for some people.  Uncomment if you need it.

/*if ($handle = opendir('tempfiles')) {
  while (false !== ($file = readdir($handle))) {
    if (filemtime('tempfiles/'.$file) < time() - 10800 && !is_dir('tempfiles/'.$file) && $file != 'index.php') {  
       @unlink('tempfiles/'.$file);
    }
  }
} */


if(file_exists($error_file)) {
	$request->status = 'error';
	$request->error_msg = file_get_contents($error_file);
	
	foreach($files as $file) {
	  @unlink("$tempFolder/$sessionID$file");
	}
	echo $json->encode($request);
	die;
}

$percent_done = 0;
$started = true;
if ($fp = @fopen($info_file, "rb")) {
		$fd = fread($fp,1000);
		fclose($fp);
		$total_size = $fd;
} else {
	$started = false;
}

if ($started == true) {
	$current_size = @filesize($data_file);
	$percent_done = intval(($current_size / $total_size) * 100);
}


if ($percent_done >= 100) {
	//Removes POST encoding data that is NOT part of the original file. 
  $handle = fopen("$data_file", "rb");
  $fileName = trim(stripslashes(urldecode($fileName)));
	$handle2 = fopen($moveToFolder. '/' . $fileName, 'wb');
	$file = array();
	// load file into array
	while (!feof($handle)) {
		$file[] = fgets($handle);
	}
	// remove lines that are from POST data, as well as last \r\n before ending delimiter (the ---------1234...)
  $scan_for_headers = true;
	for ($i=0; $i < sizeof($file); $i++) {
		$tester = strtolower(substr($file[$i], 0, 10));
		if (($tester == 'content-ty' || $tester == 'content-di' || $tester == '----------' || $tester == "\r\n" ) && $scan_for_headers) {
		 	if ($tester == '----------') {
		 		$end_of_file = trim($file[$i]) .'--';
		 	}
			//remove this stupid line
			array_splice($file, $i, 1);
			$i--;
      if ($tester == "\r\n") $scan_for_headers = false;
		} elseif (trim($file[$i]) == $end_of_file) {
			array_splice($file, $i, 1);
			$file[$i-1] = preg_replace('/\r\n$/', '', $file[$i-1]);
	  } 
	}
	//write the file
  foreach ($file as $str) {
		fputs($handle2, $str);
	}
	
  fclose($handle);
	fclose($handle2);
  
  
  foreach($files as $file) {
	  @unlink("$tempFolder/$sessionID$file");
	}
  
  $request->status = 'ok';
	$request->progress = 'done';
	$request->current_size = $total_size;
	echo $json->encode($request);
  exit;
}

$request->status = 'ok';
$request->progress = $percent_done;
if (!$current_size) $current_size = 0;
$request->current_size = $current_size;  // Fix suggested by ASDF
echo $json->encode($request);

?> 