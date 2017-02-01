<?php
// AShop
// Copyright 2002-2011 - All Rights Reserved Worldwide
// http://www.ashopsoftware.com
// This software is licensed per individual site.
// By installing or using this software, you agree to the licensing terms,
// which are located at http://www.ashopsoftware.com/license.htm
// Unauthorized use or distribution of this software
// is a violation U.S. and international copyright laws.

$announcement = FALSE;
$announcequerystring = "";
$announceheader = "POST /resources/announcement.php HTTP/1.0\r\nHost: www.ashopsoftware.com\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen ($announcequerystring)."\r\n\r\n";
$announcefp = @fsockopen ("www.ashopsoftware.com", 80, $announceerrno, $announceerrstr, 5);
if ($announcefp) {
	fputs ($announcefp, $announceheader . $announcequerystring);
	while (!feof($announcefp)) {
		$announceres = fgets ($announcefp, 1024);
		if (is_numeric(trim($announceres))) $announcement = $announceres;
		continue;
	}
	fclose ($announcefp);
}
?>