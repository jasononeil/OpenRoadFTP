/************************************************************
upload_form.js - Function to implement JSON requested AJAX upload.
Copyright (C) 2006  Jeremy Nicoll

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Please see lgpl.txt for a copy of the license - this notice and the file 
lgpl.txt must accompany this code.

Please go to www.SeeMySites.net/forum for questions and support.
***************************************************************/

//This function takes two paramaters: form (the ID of the form), and sid, which 
//differentiates the different uploads from each other.
var ul_vars = {
  interval : 3000, //The time in milleseconds between each status request.
  speeds : []      //Keeps track of the speeds of each upload.
};
function uploadForm(form, sid) {
  var theForm = $(form);
  // The variable request is an object that will contain details of the request.
  var request = {};
  var fileName = '';
  
  // This little bit loops through all the elements in the form, locates the
  // file input field, and extracts the file name of the file being uploaded.
  for (var i=0; i < theForm.elements.length; i++) { 
    ele = theForm.elements[i];
    if (ele.type == 'file') {
      var fileName = ele.value;
    	if (fileName.indexOf('/') > -1) { 
    	  fileName = fileName.substring(fileName.lastIndexOf('/')+1, fileName.length);
      }	else {
    	  fileName = fileName.substring(fileName.lastIndexOf('\\')+1, fileName.length);
      }
      //Since there can only be one file per form for this script, we'll exit the loop here. 
      break;
    }
  }
  if (fileName.replace('/\s/', '').toString() === '') {return;}
  
  
  
  theForm.submit();
  var ele;
    
  //These three parts of 'request' are required in order for the filestatus server-side script to work.
  request.sid = sid;
  request.fileName = fileName;
  request.iframe = theForm.target;
  
  
  // I hope this is self explanatory, but if not, this will send a JSON request to filestatus.php
  // every 3 seconds.  RepeatGetAction is in SendRecieve.js or sr_c.js.  If a single call is
  // successful, the successFunc will be called. Otherwise, failFunc will be called.  
   
  repeater = new RepeatGetAction('filestatus.php', request, ul_vars.interval);
  repeater.successFunc = function (getBack) {
    if (getBack.progress >= 100 || getBack.progress=='done') {
      getBack.progress = 100;
      this.stop();
    }
    if (!ul_vars.speeds[getBack.sid]) {ul_vars.speeds[getBack.sid] = [];}
    else if (ul_vars.speeds[getBack.sid].length == 3) {ul_vars.speeds[getBack.sid].shift();}
    
    ul_vars.speeds[getBack.sid].push(getBack.current_size);
    ul_vars.speeds[getBack.sid].sort(function(a, b) {return a - b;});  //Sorts numerically instead of by string. 
    
    var bytes_sec = 0;
    var bytes_append = 'B/sec';
    if (ul_vars.speeds[getBack.sid].length == 3) {
      var dif = ul_vars.speeds[getBack.sid][2]  - ul_vars.speeds[getBack.sid][0];
      bytes_sec = dif / ((ul_vars.interval / 1000) * 3);
      if (bytes_sec > 1024) {
        bytes_sec = bytes_sec / 1024;
        bytes_append = 'KB/sec';
      } 
    }
    //Due to a stupid IE bug, this has been changed...
    //$(getBack.sid + '_progress').style.width = getBack.progress + '%';
    $(getBack.sid+'_progress').style.width = (getBack.progress / 10)  + 'em';
    $(getBack.sid+'_progress').style.height = '1em';

    $(getBack.sid + '_fileName').innerHTML = bytes_sec.toFixed(2) + ' ' + bytes_append;
    //$(getBack.sid + '_fileName').innerHTML = fileName;
  }; 
  repeater.failFunc = function (getBack) {
    this.stop();
    $(getBack.iframe).src = 'blank.html';
    alert(getBack.error_msg);
  };
  
  // This MUST be called before the action will start.  When the repeater has served its 
  // purpose (or you get sick of it), you can call repeater.stop() to stop it.
  repeater.start();
}