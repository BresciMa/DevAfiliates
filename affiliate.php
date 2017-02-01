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

include "admin/config.inc.php";
include "admin/ashopfunc.inc.php";

// Parse RSS encoded URL...
if (strpos($id,"|")) {
	$redirect = strtolower(substr($id,strpos($id,"|")+1));
	$redirect = str_replace("redirect=","",$redirect);
	$id = substr($id,0,strpos($id,"|"));
}

// Apply selected theme...
$buttonpath = "";
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none" && file_exists("$ashoppath/themes/$ashoptheme/theme.cfg.php")) include "themes/$ashoptheme/theme.cfg.php";
if ($usethemebuttons == "true") $buttonpath = "themes/$ashoptheme/";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";
if ($lang && is_array($themelanguages)) {
	if (!in_array("$lang",$themelanguages)) unset($lang);
}

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "language/$lang/affiliate.inc.php";

// Open database...
   $db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
   if (!$db) $error = 1;
   $errorcheck = @mysql_select_db("$databasename",$db);
   if (!$error) if (!$errorcheck) $error = 2;

   $redirect = str_replace("|","&",$redirect);

    // Set current date and time...
	$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

	// Check if the visitor is black listed...
	$ipnumber = $_SERVER["REMOTE_ADDR"];
	$result = @mysql_query("SELECT * FROM customerblacklist WHERE blacklistitem='$ipnumber'",$db);
	if (@mysql_num_rows($result)) exit;

	// Validate variables...
	if (isset($id) && !is_numeric($id)) unset($id);
	if (isset($referrer) && !is_numeric($referrer)) {
		$referrer = stripslashes($referrer);
		$referrer = @mysql_real_escape_string($referrer,$db);
		$referrer = strtolower($referrer);
		$referrer = str_replace("\'","",$referrer);
		$referrer = str_replace("\"","",$referrer);
		$referrer = str_replace("/","",$referrer);
		$referrer = str_replace("\n","",$referrer);
		$referrer = str_replace(";","",$referrer);
		$referrer = str_replace("select","",$referrer);
		$referrer = str_replace("insert","",$referrer);
		$referrer = str_replace("update","",$referrer);
		$referrer = str_replace("delete","",$referrer);
		$referrer = str_replace("create","",$referrer);
		$referrer = str_replace("modify","",$referrer);
		$referrer = str_replace("password","",$referrer);
		$referrer = str_replace("user","",$referrer);
		$referrer = str_replace("concat","",$referrer);
		$referrer = str_replace("from","",$referrer);
		$referrer = str_replace("username","",$referrer);
	}

   // Check if affiliateID is in the database...
   if($referrer) {
	   $result = @mysql_query("SELECT affiliateid FROM affiliate WHERE referralcode='$referrer'",$db);
	   if (!@mysql_num_rows($result)) {
		   if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/customer-$lang.html");
		   else ashop_showtemplateheader("$ashoppath$templatepath/customer.html");
		   echo "<table class=\"ashopmessagetable\">
		   <tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".INCORRECT."</span></p>
		   <p><span class=\"ashopmessage\">".TRYAGAIN."</span></p>
		   <p><form action=\"affiliate.php\" method=\"post\"><input type=\"text\" name=\"referrer\" size=\"15\"><input type=\"submit\" 
		   value=\"Submit\"></form></p>
		   <p><span class=\"ashopmessage\">".STRAIGHTTOTHE."
		   <a href=\"";
		   if ($redirect) echo $redirect;
		   else echo $affiliateredirect;
		   echo "\">".PRODUCTCATALOG."</a></span></td></tr></table>";
		   if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/customer-$lang.html");
		   else ashop_showtemplatefooter("$ashoppath$templatepath/customer.html");
		   exit;
	   }
   } else $result = @mysql_query("SELECT affiliateid FROM affiliate WHERE affiliateid='$id'",$db);

   if (@mysql_num_rows($result) == 0) {
	   
	   // USUARIO DEFAULT;
	   $id = "1";
	   
	   $result = @mysql_query("SELECT affiliateid FROM affiliate WHERE affiliateid='$id'",$db);
   } 
   
   if (@mysql_num_rows($result) == 0) {
	   if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/customer-$lang.html");
	   else ashop_showtemplateheader("$ashoppath$templatepath/customer.html");
	   echo "<table class=\"ashopmessagetable\">
	     <tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".WEREYOUREFERRED."</span></p>
	     <p><span class=\"ashopmessage\">".ENTERREFERRAL."</span></p>
		 <p><form action=\"affiliate.php\" method=\"post\"><input type=\"text\" name=\"referrer\" size=\"15\"><input type=\"hidden\" name=\"redirect\" value=\"$redirect\"><input type=\"submit\" value=\"Submit\"></form></p>
		 <p><span class=\"ashopmessage\">".STRAIGHTTO."  
		 <a href=\"";
	   if ($redirect) echo $redirect;
	   else echo $affiliateredirect;
	   echo "\">$ashopname</a></span></td></tr></table>";
		 if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/customer-$lang.html");
		 else ashop_showtemplatefooter("$ashoppath$templatepath/customer.html");
	   exit;
   } else {
	   if(!$id) $id = @mysql_result($result, 0, "affiliateid");
	   $sql="SELECT clicks FROM affiliate WHERE affiliateid='$id'";
	   $result = @mysql_query("$sql",$db);
	   $clicks = @mysql_result($result, 0, "clicks");
	   $clicks++;
	   $sql = "UPDATE affiliate SET clicks='$clicks', lastdate='$date' WHERE affiliateid='$id'";
	   $result = @mysql_query("$sql",$db);
	   $httpreferer = $_SERVER["HTTP_REFERER"];
	   if(substr($httpreferer,0,strlen($ashopurl)) == $ashopurl) $httpreferer = "";
	   if(substr($httpreferer,0,strlen($ashopsurl)) == $ashopsurl) $referer = "";
	   $httpreferer = @mysql_real_escape_string($httpreferer,$db);
	   if(!empty($httpreferer)) {
		   $result = @mysql_query("SELECT clicks FROM affiliatereferer WHERE affiliateid='$id' AND referer='$httpreferer'",$db);
		   if (@mysql_num_rows($result)) {
			   $refererclicks = @mysql_result($result,0,"clicks");
			   $refererclicks++;
			   @mysql_query("UPDATE affiliatereferer SET clicks='$refererclicks' WHERE affiliateid='$id' AND referer='$httpreferer'",$db);
		   } else @mysql_query("INSERT INTO affiliatereferer (affiliateid,referer,clicks) VALUES ('$id','$httpreferer','1')",$db);
	   }

	 // Set tracking cookie...
	 setcookie("affiliate","$id", mktime(0,0,0,12,1,2020), "/");
	 if (!$error) {
		 if ($redirect) {
			 if ($referrer) {
				 if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/customer-$lang.html");
				 else ashop_showtemplateheader("$ashoppath$templatepath/customer.html");
				 echo "<table class=\"ashopmessagetable\"><tr align=\"center\"><td><br><br><span class=\"ashopmessageheader\">".THANKYOU."</span><br><br><span class=\"ashopmessage\">".REDIRECTED."
$ashopname.</span><br><meta http-equiv=\"Refresh\" content=\"3; URL=$redirect\"><br><br><span class=\"ashopmessage\">".IFNOREDIRECT."<a href=\"$redirect\">".HERE."</a>.</span></td></tr></table>";
				 if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/customer-$lang.html");
				 else ashop_showtemplatefooter("$ashoppath$templatepath/customer.html");
				 exit;
			 } else if (strstr($SERVER_SOFTWARE, "IIS")) {
				 echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=$redirect\"></head></html>";
				 exit;
			 } else header("Location: $redirect");
		 } else {
			 if ($referrer) {
				 if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/customer-$lang.html");
				 else ashop_showtemplateheader("$ashoppath$templatepath/customer.html");
				 echo "<table class=\"ashopmessagetable\"><tr align=\"center\"><td><br><br><span class=\"ashopmessageheader\">".THANKYOU."</span><br><br><span class=\"ashopmessage\">".REDIRECTED." 
$ashopname.</span><br></center><meta http-equiv=\"Refresh\" content=\"3; URL=$affiliateredirect\"><br><br><span class=\"ashopmessage\">".IFNOREDIRECT."<a href=\"$affiliateredirect\">".HERE."</a>.</span></td></tr></table>";
				 if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/customer-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/customer-$lang.html");
				 else ashop_showtemplatefooter("$ashoppath$templatepath/customer.html");
				 exit;
			 } else if (strstr($SERVER_SOFTWARE, "IIS")) {
				 echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=$affiliateredirect\"></head></html>";
				 exit;
			 } else header("Location: $affiliateredirect");
		 }
	 } else {
		 if (strstr($SERVER_SOFTWARE, "IIS")) {
			 echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=index.php?error=$error\"></head></html>";
			 exit;
		 } else header("Location: index.php?error=$error");
	 }
   }
?>