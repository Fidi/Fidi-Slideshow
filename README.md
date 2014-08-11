Fidi-Slideshow
====

A simple generic slideshow for websites written in html and javascript. Support for different browser types and resolutions as well as for touch.


Dependencies
-----
The slideshow uses [jQuery](http://jquery.com) for displaying the slideshow and parsing the XML file.

It uses [jQuery Mobile](http://jquerymobile.com) for touch controls too.


How it works
-----
To open a slideshow you have to open `slideshow.php` with a hash value that points to a XML file.

Example: To open the sample slideshow in this repo simply open `slideshow.php#Sample`.

For better performance it is possible to load all slideshow images into cache on start. This however causes a longer start time but you won't have to load every image on click.
To do so open `slideshow.php#Sample&preload`.

But right now it causes an error in Internet Explorer.