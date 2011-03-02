/****************************************
SendReceive.js - A library to automate remote JSON formatted requests
Copyright (C) <2006>  <Jeremy Nicoll>

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
********************************************/
function SendReceive() {
  this.sendRequest = SGPR.sendRequest;
  this.getRequest = SGPR.getRequest;
}
// --------- Send / Get Post Request ---------
// Sends a Post Request via JSON. (used in variable format for including in other objects).
var SGPR = {
  sendRequest : function (url, request, receiver, quiet) {
    if (receiver) {this.receiver = receiver;}
    if (typeof(this.receiver) != 'function' && this.async !== false) {
      alert('Function not supplied for callback.');
      return false;
    }
    if (quiet === null || quiet === undefined) {this.quiet = false;}
  	var jRequest = JSON.stringify(request);
    this.remote = new getHTTPRequest();
    if (this.async == false) {dont_wait = false;}
    else {dont_wait = true;}
    var ref = this;
    if (dont_wait) {
      this.remote.onreadystatechange = function () {
        ref.getRequest(ref.remote, ref);
      };
    }
    this.remote.open('POST', url, dont_wait);  
  
    this.remote.setRequestHeader("Content-type", "text/plain");
    this.remote.setRequestHeader("Content-length", jRequest.length);
    this.remote.setRequestHeader("Connection", "close");
    this.remote.send(jRequest);
    if (!dont_wait) {
      this.response = eval('(' + this.remote.responseText + ')');
    }
  },
  getRequest : function (remote, obj) {
    if (remote.readyState == 4) {
      if (remote.status == 200) {
        var getBack = eval('(' + remote.responseText + ')');
        obj.receiver(getBack);
  	  } else {
        if (!obj.quiet) {alert('There was a problem with the request. Error code: ' + this.remote.status);}
  		else {obj.receiver({status : 'remote', error_msg : remote.status});}
      }
    }
  }
};

//  --------- RepeatGetAction -----------------
//  Sends repeated requests to a file via JSON

function RepeatGetAction(page, request, interval, successFunc, failFunc, stopOnFail) {
	page = page || '';
	request = request || {};
	interval = interval || 3000;
	succssFunc = successFunc || '';
	failFunc = failFunc || '';
	if (stopOnFail === null || stopOnFail === undefined) {stopOnFail = true;}
    this.init(page, request, interval, successFunc, failFunc, stopOnFail);
}

RepeatGetAction.prototype = {
  init : function(page, request, interval, successFunc, failFunc, stopOnFail) {
	  this.page = page;
		this.request = request;
		this.interval = interval;
		this.successFunc = successFunc;
		this.failFunc = failFunc; 
		this.stopOnFail = stopOnFail;
		if (this.checkVals()) {
		  this.start();
		}
	},
    sendRequest: SGPR.sendRequest, 
    getRequest: SGPR.getRequest, 
  
	checkVals : function() {
    if (this.page !== '' && parseInt(this.interval) > 0 && typeof(this.interval) == 'number' && typeof(this.failFunc) == 'function' && typeof(this.successFunc) == 'function' && (typeof(this.request) == 'object' || typeof(this.request) == 'array')) {
		  return true;
		} else {
		  return false;
		}
	},  
	
	start : function() {
      if (!this.checkVals()) {return false;}
      var ref = this;
      this.timerVar = setInterval(function(){ref.start_ref();}, this.interval); 
	}, 
	
	start_ref : function () {
      this.sendRequest(this.page, this.request, this.receive, true);
	},
	
	receive : function(getBack) {
      if (!getBack) {
		  this.stop();
          alert('No response sent back to receive function.');
		} else if (getBack.status == 'error') {
		  if (this.stopOnFail) {this.stop();}
          this.failFunc(getBack);
		} else if (getBack.status == 'remote') {
		  this.stop();
          alert('There was a problem with the request. Error code: ' + getBack.error_msg);
		} else {
		  this.successFunc(getBack);
		}
	},
	
	stop : function() {
      clearInterval(this.timerVar);
	}
};


// -------- getHTTPRequest --------
//  Gets the current HTTP Request object for the relevant browser.

function getHTTPRequest() {
  http_request = false;
  if (window.XMLHttpRequest) { // Mozilla, Safari,...
     http_request = new XMLHttpRequest();
  } else if (window.ActiveXObject) { // IE
     try {
        http_request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (e) {
        try {
           http_request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {}
     }
  }
 return http_request;
}
// ------------  $ ------------
//  This function makes JavaScripting so much easier...
 
function $(text) {
  return document.getElementById(text);
}


