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

include "../admin/config.inc.php";
include "../admin/ashopfunc.inc.php";

// Apply selected theme...
$buttonpath = "";
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none" && file_exists("$ashoppath/themes/$ashoptheme/theme.cfg.php")) include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";
if ($usethemebuttons == "true") $buttonpath = "themes/$ashoptheme/";
if ($lang && is_array($themelanguages)) {
	if (!in_array("$lang",$themelanguages)) unset($lang);
}

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_sendpass.inc.php";

if ($affuser != "") {

  // Open database...
  $db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
  @mysql_select_db("$databasename",$db);


  // Get password and email from database...
  $sql="SELECT password,email FROM affiliate WHERE user='$affuser'";
  $result = @mysql_query("$sql",$db);
  if (@mysql_num_rows($result) != 0) {

    // Store in variables...
    $affpassword = @mysql_result($result, 0, "password");
    $email = @mysql_result($result, 0, "email");
  } else {
	  unset($affpassword);
	  unset($email);
  }

  // Close database...

  @mysql_close($db);


  if ($email != "") {

    // Send message with password...

    $subject="$ashopname - ".AFFILIATEPROGRAM;
	$message="<html><head><title>".YOURPASSWORD."</title></head><body><font face=\"$font\"><p>".YOURPASSWORDFOR." $ashopname ".IS.": $affpassword</p></font></body></html>";
	$headers = "From: $ashopname<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
    @ashop_mail("$email","$subject","$message","$headers");
  }

  // Tell affiliate that the password has been sent...

  if ($affpassword) {
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	  echo "<table class=\"ashopmessagetable\">
	  <tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".PASSWORDSENT."</span></p>
	  <p><span class=\"ashopmessage\">".PASSWORDSENTBYEMAIL."</span></p>
	  <p><span class=\"ashopmessage\"><a href=\"login.php\">".AFFILIATELOGIN."</a></span></p></td></tr></table>";
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	  exit;
  } else {
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	  echo "<table class=\"ashopmessagetable\">
	  <tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".NOTREGISTERED."</span></p>
	  <p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	  exit;
  }
}


// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");


echo "<br><table class=\"ashopaffiliateloginframe\"><tr><td>
  <span class=\"ashopaffiliateheader\">".FORGOTPASSWORD."</span>
  <p align=\"left\"><span class=\"ashopaffiliatetext2\">".ENTERUSERNAME."</span></p>
<form method=\"post\" action=\"sendpass.php\">
    <table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr> 
        <td align=\"right\"><span class=\"ashopaffiliatetext2\">".USERNAME.":</span></td>
        <td width=\"160\">&nbsp;<input type=\"text\" name=\"affuser\" size=\"20\"></td>
        <td><input type=\"image\" src=\"../{$buttonpath}images/submit-$lang.png\" class=\"ashopbutton\" border=\"0\" alt=\"".SUBMIT."\" name=\"Submit\"></td>
      </tr>
    </table>
  </form></td></tr></table>";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>