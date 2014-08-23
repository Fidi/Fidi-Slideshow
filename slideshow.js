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