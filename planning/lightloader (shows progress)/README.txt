Instructions for installation:

1) Upload all files to a folder on your web server.  

2) Set the file permissions of "tempfiles" and "uploadedfiles" to 777. See your FTP program documentation for instructions on how to set file permissions.

3) Set the file permissions of "upload.cgi" to 755.  Since upload.cgi is a PERL script, you may have to install this into another folder (and modify the upload_test.php file to point to this location) - please consult your web hosting providor for this.

4) The file sr_c.js is a compressed version of SendReceive.js.  Use the SendRecieve.js file in the "source" folder to play around with the code.  

  All copyright notices must remain intact.  If you wish to distribute your modifications, you may append to the header with the date that you made the changes and what you have done to the file.  Please see lgpl.txt for the full license of the JavaScript files.  The filestatus.php and upload.cgi files are released under a different license - please see these files for details.

  If you are not familiar with JSON, visit http://www.json.org. This is a much better way than XML to transmit data for most needs.

  Please visit our forum at http://www.SeeMySites.net/forum for questions and help.


=====================
Description of files:
=====================
  upload_tester.php - a html file that allows you to test whether you have uploaded all the files correctly and set the proper permissions.

  upload.css - support CSS file for upload_test.php.  You can also use this for your own HTML pages.

  upload_form.js - Form that uses SendRecieve (sr_c.js) to submit the file upload, and then process the results coming from filestatus.php.  You can customize this easily to fit your needs.  

  blank.html - empty file for use with the IFRAMES.  This file is useful for stopping a file upload that had an error.

  upload.cgi - Accepts the incoming file stream and writes output for filestatus.php.  

  filestatus.php - Listens to output from upload.cgi and outputs it to a listening script (sr_c.js) in the browser.

  JSON.php - Enables JSON support for PHP and is required by filestatus.php

  sr_c.js - Compressed version of SendReceive.js.  Enables simple remote communication via JSON with very little code.
  
  json_c.js - Compressed version of the JSON stringifier for JavaScript.

  lgpl.txt - contains the license for the JavaScript files.  

  README.txt - this file.



I hope this makes your life a little bit easier,

Jeremy Nicoll
www.SeeMySites.net