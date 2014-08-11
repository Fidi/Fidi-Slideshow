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



// init slideshow: loading thumbs and 
function initSlideshow(fFileName) {
	$.ajax({
		type: "GET",
		url: fFileName + ".xml",
		dataType: "xml",
		success: function(xml) {
			
			// Print title and year on top
			var $title = $(xml).find("title").text();
			
			$("#title").html($title);
			$("#year").html($(xml).find("year").text());
			
			// save path for later use
			path = $(xml).find("path").text()
			
			// print the description line
			$("#description-text").html("&nbsp;");
			$("#current-img").html("1");
			count = $(xml).find("count").text();
			$("#count-img").html(count);
			
			// if active preload then create empty array
			if(preload) {
				pictures = new Array(count - 1);	
			}
			
						
			$(xml).find("image").each(function(){
				
				var $id = $(this).attr("id")-1;
				var $imgDes = $(this).find("description").text(); 
				
				// cache all images if preload is active
				if(preload) {
					var $imgSrc = path + "images/" + $(this).find("name").text();
					
					pictures[$id] = new Image;
					pictures[$id].src = $imgSrc;
					pictures[$id].alt = "main-image"
					pictures[$id].id = "main-image";							
					// adjust image position onLoad
					pictures[$id].onload = function() { setImagePosition(); }

				}
				
				// load all images in footer
				var text = '<a href="javascript: void(0);" title="' + $imgDes + '" onclick="javascript: LoadImage(\'' + fFileName + '\', \'' + $id + '\');"><div class="thumb"><img id="img' + $id + '" src="' + path + "thumbs/" + $(this).find("name").text() + '" /></div></a>';
				$("#footer-wrapper").append(text);
			});
			
			// load the first image
			LoadImage(fFileName, 0);
			var thumbWidth = count * ((document.height * 9)/100);
			setFooterPosition(thumbWidth)
			$("#footer-wrapper").css("width", count * 9 + "vh");
		},
		
	error: function() {
			$("#main-container").html("<center><h1>Slideshow not found!</h1></center>");	
			throw "Error: XML file not found.";
		}
	});	
}




// load the image with number fID
function LoadImage(fFileName, fID) {
	id = fID;
	
	$.ajax({
		type: "GET",
		url: fFileName + ".xml",
		dataType: "xml",
		success: function(xml) {
			// find matching image:
			$(xml).find("image[id='" + (++fID) + "']").each(function(){
				
				$CurrentImage = fID-1;
				
				// load the image and description
				var imgDes = $(this).find("description").text(); 
				
				
				if (!preload) {
					// load and show image:				 
					// create new Image (necessary to adjust position in webkit browsers)
					var cachedImg = new Image;
					cachedImg.src = path + "images/" + $(this).find("name").text();
					cachedImg.alt = "main-image"
					cachedImg.id = "main-image";							
					// adjust image position onLoad
					cachedImg.onload = function() { setImagePosition(); }								
					// finally print image
					$("#main-container").html(cachedImg);
				} else {
					// just show the preloaded image	
					$("#main-container").html(pictures[fID-1]);
					setImagePosition();	
				}

				// display description line
				$("#description-text").html(imgDes);
				$("#current-img").html(fID);

				
				// if necessary scroll the footer to the current thumb
				if (!isInView("img" + fID)) {
					$("#footer").scrollLeft(document.getElementById("img" + (fID-1)).offsetLeft);
				}
			});
		},
		error: function() {
			throw "Error: Loading image or XML file failed.";
		}
		
	});
	
	// show or hide left button
	if(fID == 0) {
		$("#left").css("display", "none");
	} else {
		$("#left").css("display", "block");	
	}
		
	// show or hide right button
	if(fID == count-1) {
		$("#right").css("display", "none");
	} else {
		$("#right").css("display", "block");	
	}
	
}


function PreviousImage(fFileName) {
	if ($CurrentImage >= 1) {
		LoadImage(fFileName, --$CurrentImage);
	}
}

function NextImage(fFileName) {
	if ($CurrentImage < count-1) {
		LoadImage(fFileName, ++$CurrentImage);	
	}
}

// use left and right key to show previous/next image
function handleKeypress(fFileName, key) {
	if (!key) {
		key = window.event;
	}
	
	if (key.keyCode == 39) {
		NextImage(fFileName);
	} else if (key.keyCode == 37) {
		PreviousImage(fFileName);
	}
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

function setFooterPosition(thumbWidth) {
	var $footerDiff = thumbWidth - $(document).width();
	if ($footerDiff < 0) {
		$("#footer-wrapper").css("margin-left", (-1*$footerDiff)/2);	
	} else {
		$("#footer-wrapper").css("margin-left", 0);	
	}
}



// returns true if requested element ist currently in viewport
function isInView(elementID) {
	var element = document.getElementById(elementID);
	var parent = document.getElementById("footer");
	var twopercent = ((document.height * 2) / 100) + 10;
	
	if (element.offsetLeft - element.width <= parent.scrollLeft) return false;
	if (element.offsetLeft >= parent.scrollLeft + document.width - twopercent) return false;
	
	return true;
}


// sets the size to maximum available size of the div height without word wrapping
$.fn.fitToHeight = function() {
	$(this).wrapInner("<span style='display:inline;font:inherit;white-space:inherit;'></span>").each(function(){
		var $t=$(this);
		var a=$t.outerHeight(),
			$s=$t.children("span"),
			f=300;
		while($t.children("span").outerHeight() > a) $t.css("font-size",--f);
		$t.html($s.html());
	});
}

// sets the size to maximum available size of the div width without word wrapping
$.fn.fitToWidth = function() {
	$(this).wrapInner("<span style='display:inline;font:inherit;white-space:inherit;'></span>").each(function(){
		var $t=$(this);
		var a=$t.outerWidth(),
			$s=$t.children("span"),
			f=parseFloat($t.css("font-size"));
		while($t.children("span").outerWidth() > a) $t.css("font-size",--f);
		$t.html($s.html());
	});
}