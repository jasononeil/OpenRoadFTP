#!/usr/bin/php
<?php

function loopThroughAll()
{
	$calendarYear = date("Y");
	for ($group = $calendarYear; $group < ($calendarYear + 6); $group++)
	{
		loopThroughDir("/home/students/" . $group . "/");
	}
	loopThroughDir("/home/staff/");
}

function loopThroughDir($groupDir)
{
	echo "\n$groupDir";
	$filesInDir = scandir($groupDir);
	foreach ($filesInDir as $filename)
	{
		if ($filename != "." && $filename != "..")
		{
			echo "\n  $filename";
			createLinkForUser($filename);
		}
	}
}

function createLinkForUser($username)
{
	echo "\n    Creating Y Drive Link";
	echo "\n  For user: $username";
	
	// Figure out what yeargroup (or staff)
	$lastFourDigits = '/[0-9]{4}$/';
	$numMatches = preg_match($lastFourDigits, $username, $matches);
	$group = ($numMatches > 0) ? $matches[0] : "staff";
	echo "\n    User is in group: $group";
	
	if ($numMatches > 0)
	{
		// They're a student
		// Figure out what grade (8-12) they're in
		$calendarYear = date("Y");
		$yearsLeftInSchool = $group - $calendarYear;
		$grade = 12 - $yearsLeftInSchool;
		
		// Figure out their home directory
		$homedir = "/home/students/$group/$username/";
		
		// Get address to map Y Drive
		$yPath = "/home/subjects/year" . str_pad($grade,2,"0",STR_PAD_LEFT);
		
		echo "\n    They're a student in year $grade";
		echo "\n    Their home directory is $homedir";
		echo "\n    Map the Y drive to $yPath";
		
		createSymLink($yPath, $homedir . "Y-Drive");
	}
	else
	{
		// Figure out their home directory
		$homedir = "/home/staff/$username/";
		
		echo "\n    They're staff";
		echo "\n    Their home directory is $homedir";

		echo "\n    Map the Y drive to /home/subjects";
		createSymLink("/home/subjects", $homedir . "Y-Drive");

		echo "\n    Map the S drive to /home/students";
		createSymLink("/home/students", $homedir . "S-Drive");
	}
	
}

function createSymLink($target, $name)
{
	// If file exists, delete it
	if (file_exists($name)) 
	{
		unlink($name); 
	}
	
	// Create the link
	$success = symlink($target, $name);
	echo "\n    Did it work: $success";
}

if ($argc > 1)
{
	if ($argv[1] == "--help")
	{
		echo "\nUsage: ";
		echo "\n  ./createYDriveLink.php (Create for all users)";
		echo "\n  ./createYDriveLink.php studentname2010 (Create links only for this user) ";
	}
	else
	{
		$username = $argv[1];
		createLinkForUser($username);
	}
}
else
{
	echo "\nLoop through all.";
	loopThroughAll();
}


?>


