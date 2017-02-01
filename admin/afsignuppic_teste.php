<?php
// AShop Affiliate
// Copyright 2012 - AShop Software - http://www.ashopsoftware.com
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, see: http://www.gnu.org/licenses/.

// Check for GD...
ob_start(); 
phpinfo(8); 
$phpinfo=ob_get_contents(); 
ob_end_clean(); 
$phpinfo=strip_tags($phpinfo); 
$phpinfo=stristr($phpinfo,"gd version"); 
$phpinfo=stristr($phpinfo,"version"); 
$end=strpos($phpinfo,"\n"); 
$phpinfo=substr($phpinfo,0,$end);
preg_match ("/[0-9]/", $phpinfo, $version);
if(isset($version[0]) && $version[0]>1) $gdversion = 2;
else $gdversion = 0;

include "../admin/config.inc.php";
include "../admin/ashopfunc.inc.php";

// If GD is available generate random code for security check...
if (function_exists('imagecreatefromjpeg') && function_exists('imagecreatefromgif') && function_exists('imagecreatetruecolor') && $gdversion == 2) {
	$activatesecuritycheck = TRUE;
	if ($action == "generatecode") {
		$checkcode = generatecode($random);
		
		
		$image = ImageCreateFromJPEG("$ashoppath/admin/images/codebg.jpg");
		$text_color = ImageColorAllocate($image, 80, 80, 80);
		Header("Content-type: image/jpeg");
		ImageString ($image, 5, 12, 2, $checkcode, $text_color);
		ImageJPEG($image);
		
		//ImageJPEG($image, $checkcode."jpg", 75);
		ImageDestroy($image);
		
		
		/*
		header("Content-Type: image/png");
		//try to create an image
		$im = @imagecreate(800, 600)
		or die("Cannot Initialize new GD image stream");
		//set the background color of the image
		$background_color = imagecolorallocate($im, 0xFF, 0xCC, 0xDD);
		//set the color for the text
		$text_color = imagecolorallocate($im, 133, 14, 91);
		//adf the string to the image
		imagestring($im, 5, 300, 300,  $checkcode, $text_color);
		//outputs the image as png
		imagepng($im);
		//frees any memory associated with the image
		imagedestroy($im);		
		*/
		
	}
} 
?>