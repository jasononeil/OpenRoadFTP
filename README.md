OpenRoadFTP
===========

**Also known as "Student Logon".**

This is a web-app which allows you to log in to an FTP server
and browse around, upload and download easily.  Essentially,
it's a Javascript/PHP FTP Client.

It has been put to use in several high schools as a way for 
students to access their school files from home.

To Install
----------

 * Log in as root on a server on your network
 * Create a directory to download our files, such as "studentlogon"
 * Copy our file "tools/update.sh" and paste it in to your new directory.
 * Check "update.sh" to make sure `target` is the place you want to install this.
 * Run `./update.sh` (Note: you will need Git installed `apt-get install git-core`)
 * Copy `tools/index.php` to the Student Server.  Edit to make sure it 
   redirects to the right address.
 * Copy `tools/createYDriveLink.php` to `/root/` on the server.
 * Run `/root/createYDriveLink.php` (you will need `php5-cli` installed).  This
   will create "Y Drive" and "S Drive" symlinks in users folders.
 * Schedule `createYDriveLink` to run every hour, by adding this line to 
   `crontab -e`: `42 * * * * /root/createYDriveLink.php`
 * Make sure that both Apache and FTPd are being kept alive and restarted if they die.


To Update
---------

Rerunning `./update.sh` will update the code to the latest version on github.

See https://github.com/jasononeil/OpenRoadFTP if you want to check before
updating.
