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

// Validate confirmation code...
if (!empty($aid) && !preg_match("/^[2-9a-z]{7}$/", $aid)) $aid = "";

// If GD is available generate random code for security check...
if (!$_GET["aid"] && function_exists('imagecreatefromjpeg') && function_exists('imagecreatefromgif') && function_exists('imagecreatetruecolor') && $gdversion == 2) {
	$activatesecuritycheck = TRUE;
	if ($action == "generatecode") {
		$checkcode = generatecode($random);
		$image = ImageCreateFromJPEG("$ashoppath/admin/images/codebg.jpg");
		$text_color = ImageColorAllocate($image, 80, 80, 80);
		Header("Content-type: image/jpeg");
		ImageString ($image, 5, 12, 2, $checkcode, $text_color);
		ImageJPEG($image, '', 75);
		ImageDestroy($image);
		exit;
	}
} else $activatesecuritycheck = FALSE;

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none" && file_exists("$ashoppath/themes/$ashoptheme/theme.cfg.php")) include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_signup.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get pending affiliate information to confirm email...
$confirmed = FALSE;
if ($_GET["aid"]) {
	$pendingaffresult = @mysql_query("SELECT * FROM pendingaffiliate WHERE password='{$_GET["aid"]}'",$db);
	if (@mysql_num_rows($pendingaffresult)) {
		$pendingaffrow = @mysql_fetch_array($pendingaffresult);
		$affuser = $pendingaffrow["user"];
		$business = $pendingaffrow["business"];
		$firstname = $pendingaffrow["firstname"];
		$lastname = $pendingaffrow["lastname"];
		$email = $pendingaffrow["email"];
		$paypalid = $pendingaffrow["paypalid"];
		$address = $pendingaffrow["address"];
		$state = $pendingaffrow["state"];
		$zip = $pendingaffrow["zip"];
		$city = $pendingaffrow["city"];
		$country = $pendingaffrow["country"];
		$url = $pendingaffrow["url"];
		$phone = $pendingaffrow["phone"];
		$affiliate = $pendingaffrow["referedby"];
		$extrainfo = $pendingaffrow["extrainfo"];
		@mysql_query("DELETE FROM pendingaffiliate WHERE password='{$_GET["aid"]}'",$db);
		$confirmed = TRUE;
	} else {
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
		echo "<table class=\"ashopmessagetable\">
		<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".ERROR."</span></p>
		<p><span class=\"ashopmessage\">".AIDDOESNOTEXIST."</span></p></td></tr></table>";
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
		exit;
	}
}

// Check for spam injection...
$affuser = ashop_mailsafe($affuser);
$business = ashop_mailsafe($business);
$firstname = ashop_mailsafe($firstname);
$lastname = ashop_mailsafe($lastname);
$email = ashop_mailsafe($email);
$address = ashop_mailsafe($address);
$state = ashop_mailsafe($state);
$province = ashop_mailsafe($province);
if (empty($state) && !empty($province)) $state = $province;
$zip = ashop_mailsafe($zip);
$city = ashop_mailsafe($city);
$country = ashop_mailsafe($country);
$phone = ashop_mailsafe($phone);
$paypalid = ashop_mailsafe($paypalid);
if (substr($url,0,7) != "http://" && substr($url,0,8) != "https://") $url = "http://".$url;

// Check if all fields were filled in...
if (($affuser=="") || ($firstname=="") || ($lastname=="")
|| ($email=="") || ($address=="") || ($zip=="") || ($city=="") || ($country=="") || ($paypalid=="" && $requirepaypalid)) {
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	echo "<table class=\"ashopmessagetable\">
	<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".ERROR."</span></p>
	<p><span class=\"ashopmessage\">".YOUFORGOT."</span></p>
	<p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	exit;
}


// Check if the username contains forbidden characters...
if (strstr($affuser, chr(32)) || strstr($affuser, chr(33)) || strstr($affuser, chr(44)) || strstr($affuser, chr(46)) || strstr($affuser, chr(63)) || (strlen($affuser) > 10)) {
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	echo "<table class=\"ashopmessagetable\">
	<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".ERROR."</span></p>
	<p><span class=\"ashopmessage\">".THEUSERNAME."</span></p>
	<p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	exit;
}

// Check if the right security check code has been provided...
if ($activatesecuritycheck && (!$securitycheck || $securitycheck != generatecode($random))) {
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	echo "<table class=\"ashopmessagetable\">
	<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".ERROR."</span></p>
	<p><span class=\"ashopmessage\">".INCORRECTSECURITYCODE."</span></p>
	<p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	exit;
}

// Check affiliate data...
$sql="SELECT user FROM affiliate WHERE user='$affuser'";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result) != 0) {
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	echo "<table class=\"ashopmessagetable\">
	<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".SORRY."</span></p>
	<p><span class=\"ashopmessage\">".ALREADYINUSE."</span></p>
	<p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	exit;
}


// Generate a unique password...
function makePassword() {
   $alphaNum = array(2, 3, 4, 5, 6, 7, 8, 9, a, b, c, d, e, f, g, h, i, j, k, m, n, p, q, r, s, t, u, v, w, x, y, z);
   srand ((double) microtime() * 1000000);
   $pwLength = "7"; // this sets the limit on how long the password is.
   for($i = 1; $i <=$pwLength; $i++) {
      $newPass .= $alphaNum[(rand(0,31))];
   }
   return ($newPass);
}
$affpassword = makePassword();
$unique = 0;
while (!$unique) {
   if ($affiliateconfirm && !$confirmed) $sql="SELECT password FROM pendingaffiliate WHERE password='$affpassword'";
   else $sql="SELECT password FROM affiliate WHERE password='$affpassword'";
   $result = @mysql_query("$sql",$db);
   if (@mysql_num_rows($result) == 0) $unique = 1;
   else $affpassword = makePassword();
}

// Generate a unique referral code for manual referral...
$referralcode = substr(strtolower($firstname),0,2).substr(strtolower($lastname),0,3);
$referralcode .= str_repeat("0",5-strlen($referralcode));
$refnumber = 1;
$newreferralcode = $referralcode;
$referralcodenumber = $referralcode.sprintf("%03d",$refnumber);
$unique = 0;
$n = 0;
$m = ord("a");
while(!$unique) {
	while(!$unique && $refnumber < 1000) {
		$result = @mysql_query("SELECT referralcode FROM affiliate WHERE referralcode='$referralcodenumber'",$db);
		if(@mysql_num_rows($result)) {
			$refnumber++;
			$referralcodenumber = $newreferralcode.sprintf("%03d",$refnumber);
		} else $unique = 1;
	} if(!$unique) {
		$refnumber = 1;
		$newreferralcode = substr_replace($referralcode, chr($m), $n, 1);
		$referralcodenumber = $newreferralcode.sprintf("%03d",$refnumber);
		if($m == ord("z")) {
			$n++;
			$m = ord("a");
		} else $m++;
	}
}

// Set current date and time...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

// Set confirmation code if email confirmation is on...
if ($affiliateconfirm && !$confirmed) {
	$sql = "INSERT INTO pendingaffiliate (user, password, business, firstname, lastname,
	email, address, state, zip, city, country, phone, url, paypalid, extrainfo, referedby) VALUES ('$affuser', '$affpassword', '$business', '$firstname', '$lastname', '$email', '$address', '$state', '$zip', '$city', '$country', '$phone', '$url', '$paypalid', '$extrainfo', '$affiliate')";
	$result = @mysql_query("$sql",$db);

	$message = "<html><head><title>".THANKYOUFORJOINING." $ashopname ".AFFILIATEPROGRAM."</title></head><body><font face=\"$font\"><p>".YOUARERECEIVING." $ashopname.</p>
	<p>".PLEASEVERIFY." <a href=\"$ashopurl/affiliate/signup.php?aid=$affpassword\">$ashopurl/affiliate/signup.php?aid=$affpassword</a></p></font></body></html>";

	$headers = "From: ".un_html($ashopname)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
	@ashop_mail("$email",un_html($ashopname)." ".AFFILIATEPROGRAM,"$message","$headers");
	@mysql_close($db);

	// Allow external programming...
	if (file_exists("$ashoppath/api/affiliatesignup.inc.php")) include "../api/affiliatesignup.inc.php";

	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	echo "<table class=\"ashopmessagetable\">
	<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".THANKYOUFORREGISTERING." $ashopname ".AFFILIATEPROGRAM."</span></p>
	<p><span class=\"ashopmessage\">".CHECKMAIL."</span></p>
	</td></tr></table>";
	if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");

	exit;
}

// Store affiliate data...
if ($paypalid) $sql = "INSERT INTO affiliate (user, password, business, firstname, lastname,
email, address, state, zip, city, country, phone, url, paypalid, signedup, updated, referralcode, commissionlevel, extrainfo) VALUES ('$affuser', '$affpassword', '$business', '$firstname', '$lastname', '$email', '$address', '$state', '$zip', '$city', '$country', '$phone', '$url', '$paypalid', '$date', '$date', '$referralcodenumber', 1, '$extrainfo')";
else $sql = "INSERT INTO affiliate (user, password, business, firstname, lastname,
email, address, state, zip, city, country, phone, url, signedup, updated, referralcode, commissionlevel, extrainfo) VALUES ('$affuser', '$affpassword', '$business', '$firstname', '$lastname', '$email', '$address', '$state', '$zip', '$city', '$country', '$phone', '$url', '$date', '$date', '$referralcodenumber', 1, '$extrainfo')";
$result = @mysql_query("$sql",$db);
$affiliateid = @mysql_insert_id();
if ($affiliate && $secondtieractivated) {
	$sql = "UPDATE affiliate SET referedby=$affiliate WHERE affiliateid=$affiliateid";
	$result = @mysql_query("$sql",$db);
}

// Send message to inform webmaster about the new affiliate...

$message="<html><head><title>New Affiliate</title></head><body><font face=\"$font\"><b>$affuser</b> has signed up with the affiliate program, check his/her website: <a href=\"$url\">$url</a></font></body></html>";
$headers = "From: $affuser<$email>\nX-Sender: <$email>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$email>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
@ashop_mail("$affiliaterecipient","New Affiliate Signed Up","$message","$headers");

// Close database...

@mysql_close($db);

// Send message with password to affiliate...

if (file_exists("$ashoppath/templates/messages/affiliatesignupmessage-$lang.html")) $messagefile = "$ashoppath/templates/messages/affiliatesignupmessage-$lang.html";
else $messagefile = "$ashoppath/templates/messages/affiliatesignupmessage.html";
$fp = @fopen("$messagefile","r");
if ($fp) {
	while (!feof ($fp)) $messagetemplate .= fgets($fp, 4096);
	fclose($fp);
} else {
	$messagetemplate="<html><head><title>".THANKYOUFORJOINING." $ashopname ".AFFILIATEPROGRAM."</title></head><body><font face=\"$font\"><p>".THANKYOUFORJOINING." $ashopname ".AFFILIATEPROGRAM."</p><p>".YOURUSERNAMEIS." <b>$affuser</b>".ANDYOURPASSWORD." <b>$affpassword</b></p><p>".TOMANUALLYREFER." $referralcodenumber</p><p>".LOGIN." <b><a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></b></p></font></body></html>";
}
$message = str_replace("%ashopname%",$ashopname,$messagetemplate);
$message = str_replace("%username%",$affuser,$message);
$message = str_replace("%password%",$affpassword,$message);
$message = str_replace("%referralcode%",$referralcodenumber,$message);
$message = str_replace("%affiliateid%",$affiliateid,$message);
$message = str_replace("%affiliateurl%","$ashopurl/affiliate.php?id=$affiliateid",$message);
$message = str_replace("%business%",$business,$message);
$message = str_replace("%firstname%",$firstname,$message);
$message = str_replace("%lastname%",$lastname,$message);
$message = str_replace("%email%",$email,$message);
$message = str_replace("%address%",$address,$message);
$message = str_replace("%state%",$state,$message);
$message = str_replace("%zip%",$zip,$message);
$message = str_replace("%city%",$city,$message);
$message = str_replace("%country%",$country,$message);
$message = str_replace("%phone%",$phone,$message);
$message = str_replace("%url%",$url,$message);
$message = str_replace("%date%",$date,$message);
$message = str_replace("%loginlink%","<a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a>",$message);

$headers = "From: ".un_html($ashopname)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
@ashop_mail("$email",un_html($ashopname)." ".AFFILIATEPROGRAM,"$message","$headers");

// Allow external programming...
if (file_exists("$ashoppath/api/affiliatesignup.inc.php")) include "../api/affiliatesignup.inc.php";

// Show login form...
header("Location:login.php?newregistered=true");
?>