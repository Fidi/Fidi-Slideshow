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
		  	$agents = array('Windows CE', 'Pocket', 'Mobile',
							'Portable', 'Smartphone', 'SDA',
							'PDA', 'Handheld', 'Symbian',
							'WAP', 'Palm', 'Avantgo',
							'cHTML', 'BlackBerry', 'Opera Mini',
							'Nokia');
			for ($i=0; $i<count($agents); $i++) {
				if(isset($_SERVER["HTTP_USER_AGENT"]) && strpos($_SERVER["HTTP_USER_AGENT"], $agents[$i]) !== false)
					return true;
		  	}
		  	return false;
		}
	?>

    
    <!-- jQuery is required no matter if mobile or desktop version -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

	<!-- All functions that have to do with the slideshow -->
	<script type="text/javascript">
		<!--
		
		// Parse the XML file submitted in fFileName:
		// - Load name, year and thumbs
		// - Show first image
		function initSlideshow(fFileName) {
			$.ajax({
				type: "GET",
				url: fFileName + ".xml",
				dataType: "xml",
				success: function(xml) {
					
					// Starting with the title: print it in the header
					// and change document title tag:
					var $title = $(xml).find("title").text();		
					$("#title").html($title);
					document.title = "Slideshow: " + $title;
					
					// Print year in header
					$("#year").html($(xml).find("year").text());
					
					
					// The path will be used a lot later when loading the thumbs:
					path = $(xml).find("path").text()
					
					
					// Init an "empty" description line: this will be filled
					// when calling LoadImage
					$("#description-text").html("&nbsp;");
					$("#current-img").html("-");
					count = $(xml).find("count").text();
					$("#count-img").html(count);
					
					
					// if preload is active then create an empty array
					// that will store all images
					if(preload) {
						pictures = new Array(count - 1);	
					}
					
					
					// This loop runs over every <Image>-Tag in the XML file		
					$(xml).find("image").each(function(){
						
						// the id needs to be decremented since the array starts at 0
						var $id = $(this).attr("id")-1;
						var $imgDes = $(this).find("description").text(); 
						
						// if preload is active then store the image in the array
						if(preload) {
							var $imgSrc = path + "images/" + $(this).find("name").text();
							
							// alloc image and store it in the array
							pictures[$id] = new Image;
							pictures[$id].src = $imgSrc;
							pictures[$id].alt = "main-image"
							pictures[$id].id = "main-image";
						}
						
						// load the thumb file into the footer:
						// since this is a little cryptic: important is the onclick event with the correct id (that is the original id-1) and the correct file path (in my case I added a folder "thumbs/"
						var text = '<a href="javascript: void(0);" title="' + $imgDes + '" onclick="javascript: LoadImage(' + $id + ');"><div class="thumb"><img id="img' + $id + '" src="' + path + "thumbs/" + $(this).find("name").text() + '" /></div></a>';
						$("#footer-wrapper").append(text);
					});
					
					// all thumbs loaded:
					// now set the thumb wrapper width to the correct size:
					var thumbWidth = count * ((window.innerHeight * 9)/100);
					$("#footer-wrapper").css("width", ((count * 9) + 1) + "vh");
					// if necessary center the thumb-wrapper
					setFooterPosition(thumbWidth);
					
					
					// finally load the first image (starting @ 0)
					LoadImage(0);
					$("#left").css("visibility", "visible");
				},
				
			error: function() {
					$("#main-container").html("<center><h1>Slideshow not found!</h1></center>");	
					throw "Error: XML file not found.";
				}
			});	
		}
		
		
		
		
		// Load the requested image into viewport:
		// - fID is the number image (starting at 0)
		function LoadImage(fID) {
			$.ajax({
				type: "GET",
				url: fileName + ".xml",
				dataType: "xml",
				success: function(xml) {
					
					// check if fID is in range
					if ((fID >= 0) && (fID < count)) {
						
						// set global varialbe to new requested ID
						$CurrentImage = fID;
						
						// if we show the first image then hide the previousImage Button
						if(fID == 0) {
							$("#left").css("display", "none");
						} else {
							$("#left").css("display", "block");	
						}
						// if we show the last image then hide the nextImage Button
						if(fID == count-1) {
							$("#right").css("display", "none");
						} else {
							$("#right").css("display", "block");	
						}
				
				
						// Search the XML file for the matching ID
						$(xml).find("image[id='" + (++fID) + "']").each(function(){
							
							if (!preload) {
								// Preload is not active:	 
								// create new Image (necessary to adjust position in webkit browsers)
								var cachedImg = new Image;
								cachedImg.src = path + "images/" + $(this).find("name").text();
								cachedImg.alt = "main-image"
								cachedImg.id = "main-image";							
								// adjust image position onLoad
								cachedImg.onload = function() { setImagePosition(); }								
								// finally show image
								$("#main-container").html(cachedImg);
							} else {
								// Preload is active:
								// just show the preloaded image	
								$("#main-container").html(pictures[fID-1]);
							}
							
							setImagePosition();	
			
							// display description line
							$("#description-text").html($(this).find("description").text());
							$("#current-img").html(fID);
			
							
							// if necessary scroll the footer to the current thumb
							if (!thumbIsVisible("img" + (fID-1))) {
								$("#footer").scrollLeft(document.getElementById("img" + (fID-1)).offsetLeft);
							}
						});
					} else {
						throw "Error: Invalid image id submitted!";
					}
				},
				error: function() {
					throw "Error: Loading image or XML file failed.";
				}
				
			});
		}
		
		
		function PreviousImage() {
			if ($CurrentImage >= 1) {
				LoadImage(--$CurrentImage);
			}
		}
		
		function NextImage() {
			if ($CurrentImage < count-1) {
				LoadImage(++$CurrentImage);	
			}
		}
		
		
		
		// returns true if requested element ist currently in viewport
		function thumbIsVisible(elementID) {
			var element = document.getElementById(elementID);
			var parent = document.getElementById("footer");
			var twopercent = ((document.height * 2) / 100) + 10;
			
			if (element.offsetLeft <= parent.scrollLeft) return false;
			if (element.offsetLeft + element.width >= parent.scrollLeft + document.width) return false;
			
			return true;
		}
		
		
		// adjust image margin top to center it in the viewport
		function setImagePosition() {
			var imgHeight = $("#main-image").height();
			var containerHeight = $("#main-container").height();
					
			if (imgHeight != 0) {
				var margTop = (containerHeight - imgHeight) / 2;
				$("#main-image").css("margin-top", margTop + "px");
			}
		}
		
		// if footer-wrapper is smaller then window width center it
		function setFooterPosition(thumbsWidth) {
			var $footerDiff = thumbsWidth - window.innerWidth;
			if ($footerDiff < 0) {
				$("#footer-wrapper").css("margin-left", ((-1*$footerDiff)/2));	
			} else {
				$("#footer-wrapper").css("margin-left", 0);	
			}
		}
		
		
		
		// sets the size to maximum available size of the div height without word wrapping
		$.fn.fitToHeight = function() {
			$(this).wrapInner("<span style='display:inline;font:inherit;white-space:inherit;'></span>").each(function(){
				var $t=$(this);
				var a=$t.outerHeight(),
					$s=$t.children("span"),
					f=60;
				$t.css("font-size", f);
				while($t.children("span").outerHeight() > a) $t.css("font-size",--f);
				$t.html($s.html());
			});
		}
		//-->
	</script>
    
    
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
				echo '<a href="javascript: void(0);" onclick="javascript: PreviousImage();"><div id="left" class="fitHeight">&lsaquo;</div></a>';
        		echo '<a href="javascript: void(0);" onclick="javascript: NextImage();"><div id="right" class="fitHeight">&rsaquo;</div></a>';
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