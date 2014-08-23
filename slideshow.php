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
    
    
    <?php
		// this checks weather the mobile or desktop version is required
		// Note: Doing that in PHP instead of JS has the advantage that the code
		// is rendered bevor parsed so that the output file size is smaller. Important
		// for mobile websites where every bit counts ;) 
		function check_mobile() {
		  $agents = array(
			'Windows CE', 'Pocket', 'Mobile',
			'Portable', 'Smartphone', 'SDA',
			'PDA', 'Handheld', 'Symbian',
			'WAP', 'Palm', 'Avantgo',
			'cHTML', 'BlackBerry', 'Opera Mini',
			'Nokia'
		  );
			  for ($i=0; $i<count($agents); $i++) {
			if(isset($_SERVER["HTTP_USER_AGENT"]) && strpos($_SERVER["HTTP_USER_AGENT"], $agents[$i]) !== false)
			  return true;
		  }
		  return false;
		}
	?>

    
    <!-- These scripts are required no matter if mobile or desktop version -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="slideshow.js"></script>
    
    
    <?php
		// use variable to avoid calling check_mobile all the time
		$mobile = check_mobile();
		
		// now add special stylesheets and js files based on mobile or desktop view
		if ($mobile) {
			echo '<script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script>';
			echo '<link type="text/css" rel="stylesheet" href="slideshow_mobile.css" />';
		} else {
			echo '<link type="text/css" rel="stylesheet" href="slideshow.css" />';
		}
	?>
    
    
    
	<script type="text/javascript">
		<!--
		// define global variables:
		var $CurrentImage = 1;
		var fileName = "";
		var count = 0;
		var preload = false;
		var pictures = new Array();
		//-->
	</script>
    
    
    
	<title>Slideshow</title>
</head>


<body>

	<div id="header">
    	<div class="fitHeight" id="title">&nbsp;</div>
        <div class="fitHeight" id="year">&nbsp;</div>
    </div>
	
    
    
    
	<div id="main">
    	<div id="main-container">
            <!-- will be filled with jQuery -->
            <h1>Loading Slideshow...</h1>
        </div>
        
        <?php
			// in the desktop version show left / right arrow next to the images
			// mobile version somewhere in the footer!
			if (!$mobile) {
				echo '<a href="javascript: void(0);" onclick="javascript: PreviousImage(fileName);"><div id="left"><strong>&lsaquo;</strong></div></a>';
        		echo '<a href="javascript: void(0);" onclick="javascript: NextImage(fileName);"><div id="right"><strong>&rsaquo;</strong></div></a>';
			}
		?>
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
			 <?php
			 	if ($mobile) {
					// on mobile devices register touch gestures using jQuery Mobile
					echo '	$("#main").on("swipeleft", function(){
								NextImage(fileName);
							});';
			
					echo '	$("#main").on("swiperight", function(){
								PreviousImage(fileName);
							});';	
				}
			?>
			
			
			// register keypress events on all (!) devices (I'm thinking of something like M$ Surface
			// which has both: touch and keyboard)
			$(document).keydown(function(event) {
				if (event.keyCode == 39) {
					NextImage();
				} else if (event.keyCode == 37) {
					PreviousImage();
				}
				event.preventDefault();
			});
			
			
			
			// Restyle slideshow on each resize event
			$(window).resize(function() {
				// adjust text height and width
				$(".fitHeight").fitToHeight();		
				$(".fitWidth").fitToHeight();	
				
				// adjust image position
				setImagePosition();
				
				// adjust foother position
				setFooterPosition($("#footer-wrapper").width());
			});
			
			
			
			
			// the autostart script:
			$(document).ready(function() {
				
				// extract the hash values: slideshow.php#hash1&hash2
				var args = document.location.hash.substr(1).split('&');
				
				// hash1 should be the name of the slideshows XML file 
				// (without .xml)
				if ((args.length >= 1) && (args[0] != "")) {
					fileName = args[0];
				} else {
					$("#main-container").html("<center><h1>No slideshow specified!</h1></center>");	
					throw "Error: No slideshow submitted.";
				}
				
				// if hash2 is preload the slideshow should load all images on start
				// to avoid pauses between images
				if((args.length >= 2) && (args[1] == "preload")) {
					if (navigator.userAgent.indexOf("MSIE") != -1) {
						// preload does not work in Internet Explorer
						preload = false;	
					} else {
						preload = true;
					}
				}
				
			
				// now initiate the slideshow: 
				// Load the XML file, create the thumb view und load first image into viewport
				if (fileName != "") {
					initSlideshow(fileName);
				}
				
				// adjust text font size
				$(".fitHeight").fitToHeight();		
				$(".fitWidth").fitToHeight();	
            });	
        //-->
	</script>
</body>
</html>