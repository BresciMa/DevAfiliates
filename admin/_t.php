<?php
//phpinfo();

/**
 * PHP GD
* create a simple image with GD library
*
*/
//setting the image header in order to proper display the image
header("Content-Type: image/png");
//try to create an image
$im = @imagecreate(800, 600)
or die("Cannot Initialize new GD image stream");
//set the background color of the image
$background_color = imagecolorallocate($im, 0xFF, 0xCC, 0xDD);
//set the color for the text
$text_color = imagecolorallocate($im, 133, 14, 91);
//adf the string to the image
imagestring($im, 5, 300, 300,  "I'm a pretty picture:))", $text_color);
//outputs the image as png
imagepng($im);
//frees any memory associated with the image
imagedestroy($im);

?>