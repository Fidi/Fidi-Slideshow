<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Kevin Fiedler
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script>
	<script src="slideshow.js"></script>
	<link type="text/css" rel="stylesheet" href="slideshow.css" />
    
    
	<script type="text/javascript">
		<!--
		// define all variables:
		var $CurrentImage = 1;
		var fileName = "";
		var count = 0;
		var preload = false;
		var pictures = new Array();
		//-->
	</script>
    
    
    
	<title>Slideshow</title>
</head>

<body onkeydown="javascript: handleKeypress(fileName);">
	<div id="header">
    	<div class="fitHeight" id="title">&nbsp;</div>
        <div class="fitHeight" id="year">&nbsp;</div>
    </div>
	
    
    
    
	<div id="main">
    	<div id="main-container">
            <!-- will be filled with jQuery -->
            <h1>Loading Slideshow...</h1>
        </div>
        
        <a href="javascript: void(0);" onclick="javascript: PreviousImage(fileName);"><div id="left"><strong>&lsaquo;</strong></div></a>
        <a href="javascript: void(0);" onclick="javascript: NextImage(fileName);"><div id="right"><strong>&rsaquo;</strong></div></a>
   	</div>
    
    
   	<div id="description">
    	<div class="fitHeight" id="description-text">&nbsp;</div>
       	<div class="fitHeight" id="description-counter"><span id="current-img">1</span>/<span id="count-img">42</span></div>
    </div>
    
    
    <div id="footer">
    	<div id="footer-wrapper">
        	
    	</div>
    </div>
    
    
    
    <script type="text/javascript">
		<!--	
			// extract the hash values:
			var args = document.location.hash.substr(1).split('&');
			// name of the xml file
			if ((args.length >= 1) && (args[0] != "")) {
				fileName = args[0];
			} else {
				$("#main-container").html("<center><h1>No slideshow specified!</h1></center>");	
				throw "Error: No slideshow submitted.";
			}
			//preload yes/no ?
			if((args.length >= 2) && (args[1] == "preload")) {
				if (navigator.userAgent.indexOf("MSIE") != -1) {
					// preload does not work in Internet Explorer
					preload = false;	
				} else {
					preload = true;
				}
			}
			
			
			// adjust text font size
			$(".fitHeight").fitToHeight();		
			$(".fitWidth").fitToHeight();	
			
			 
			 
			// register mobile touch gestures
			$("#main").on("swipeleft", function(){
				NextImage(fileName);
			});
			
			$("#main").on("swiperight", function(){
				PreviousImage(fileName);
			});	
			
			
			// adjust font size and image position on resize
			$(window).resize(function() {
				$(".fitHeight").fitToHeight();		
				$(".fitWidth").fitToHeight();	
				
				setImagePosition();
				setFooterPosition($("#footer-wrapper").width());
			});
			
			
			//startup script: Load the XML file, create the thumb view und load first image into viewport
			if (fileName != "") {
				initSlideshow(fileName);
			}
			
			//TODO: set footer position after slideshow start to center it the thumb width is smaller than the window width			
        //-->
	</script>
</body>
</html>