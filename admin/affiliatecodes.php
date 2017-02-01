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

include "config.inc.php";
include "checklogin.inc.php";
include "template.inc.php";
// Get language module...
include "language/$adminlang/affiliates.inc.php";
// Get context help for this page...
$contexthelppage = "affiliatecodes";
include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);


// Handle new and updated link codes...
if ($submitbutton) {
	if (!$linkid) {
		$imgfile = str_replace("\t","\\t",$imgfile);
		if (is_uploaded_file($imgfile)) {
			$filename = preg_replace("/%28|%29|%2B/","",urlencode(basename($imgfile_name)));
			$filename = preg_replace("/%E5|%E4/","a",$filename);
			$filename = preg_replace("/%F6/","o",$filename);
			$filename = preg_replace("/%C5|%C4/","A",$filename);
			$filename = preg_replace("/%D6/","O",$filename);
			$filename = preg_replace("/\+\+\+|\+\+/","+",$filename);
			$fileinfo = pathinfo("$filename");
			$extension = $fileinfo["extension"];
			if ($extension != "gif" && $extension != "jpg") {
				$error = "extension";
			} else {
				move_uploaded_file($imgfile, "$ashoppath/banners/$filename");
				@chmod("$ashoppath/banners/$filename", 0666);
			}
			$sql="INSERT INTO linkcodes (linkcategoryid,filename,linktext,redirect,alt) VALUES ('$linkcategoryid','$filename','$linktext', '$redirect', '$alt')";
		} else $sql="INSERT INTO linkcodes (linkcategoryid,linktext,redirect) VALUES ('$linkcategoryid','$linktext','$redirect')";
	} else {
		if (is_uploaded_file($imgfile)) {
			$filename = preg_replace("/%28|%29|%2B/","",urlencode(basename($imgfile_name)));
			$filename = preg_replace("/%E5|%E4/","a",$filename);
			$filename = preg_replace("/%F6/","o",$filename);
			$filename = preg_replace("/%C5|%C4/","A",$filename);
			$filename = preg_replace("/%D6/","O",$filename);
			$filename = preg_replace("/\+\+\+|\+\+/","+",$filename);
			$fileinfo = pathinfo("$filename");
			$extension = $fileinfo["extension"];
			if ($extension != "gif" && $extension != "jpg") {
				$error = "extension";
			} else {
				move_uploaded_file($imgfile, "$ashoppath/banners/$filename");
				@chmod("$ashoppath/banners/$filename", 0666);
			}
			$sql = "UPDATE linkcodes SET linkcategoryid='$linkcategoryid', linktext='$linktext', redirect='$redirect', alt='$alt', filename='$filename' WHERE linkid=$linkid";
		} else $sql = "UPDATE linkcodes SET linkcategoryid='$linkcategoryid', linktext='$linktext', redirect='$redirect', alt='$alt' WHERE linkid=$linkid";
	}
	$result = @mysql_query($sql,$db);
}

// Delete link code...
if ($deletebutton) {
	$sql = "DELETE FROM linkcodes WHERE linkid=$linkid";
	$result = @mysql_query($sql,$db);
}

// Create an array of link categories...
$linkcategories = array();
$result = @mysql_query("SELECT * FROM linkcategories ORDER BY linkcategoryid ASC",$db);
while ($row = @mysql_fetch_array($result)) {
	$linkcategoryid = $row["linkcategoryid"];
	$linkcategoryname = $row["linkcategoryname"];
	$linkcategories["$linkcategoryid"] = $linkcategoryname;
}

if (strpos($header, "body") != 0) {
	$newheader = substr($header,1,strpos($header, "body")+3);
	$newheader .= " onUnload=\"closemessage()\" ".substr($header,strpos($header, "body")+4,strlen($header));
} else {
	$newheader = substr($header,1,strpos($header, "BODY")+3);
	$newheader .= " onUnload=\"closemessage()\" ".substr($header,strpos($header, "BODY")+4,strlen($header));
}
echo "$newheader
		<script language=\"JavaScript\">
		function uploadmessagetwotier() 
		{
		  if (document.twotierform.imgfile.value != '') {
			  w = window.open('uploadmessage.html','_blank','toolbar=no,location=no,width=350,height=150');
		  }
	    }
		function uploadmessage() 
		{
		  if (document.addform.imgfile.value != '') {
			  w = window.open('uploadmessage.html','_blank','toolbar=no,location=no,width=350,height=150');
		  }
	    }
        function closemessage()
        {
       	  if (typeof w != 'undefined') w.close();
        }
        </script>
<div class=\"heading\">".LINKCODES." <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center>";

// Show recruitment link...
if ($secondtieractivated) {
	$sql = "SELECT * FROM linkcodes WHERE linkid=1";
	$result = @mysql_query("$sql",$db);
	if (@mysql_num_rows($result)) {
		$thislinktext = @mysql_result($result, 0, "linktext");
		$newlinktext = str_replace("%affiliatelink%","$ashopurl/affiliate.php?id=0",$thislinktext);
		$newlinktext2 = str_replace("&gt;",">",$newlinktext);
		$newlinktext2 = str_replace("&lt;","<",$newlinktext2);
		$thisfilename = @mysql_result($result, 0, "filename");
		$thisalt = @mysql_result($result, 0, "alt");
		echo "<p><form action=\"affiliatecodes.php\" method=\"post\" enctype=\"multipart/form-data\" name=\"twotierform\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" align=\"center\" bgcolor=\"#D0D0D0\"><tr class=\"formtitle\"><td valign=\"top\" colspan=\"3\">".RECRUITMENTLINK.":</td></tr><tr class=\"formlabel\"><td colspan=\"3\" align=\"center\" valign=\"top\">";
		if ($thisfilename) echo "<img src=\"../banners/$thisfilename\" alt=\"$thisalt\"><br>";
		echo "$newlinktext2<br><br></td></tr><tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".BANNERIMAGE.":</td><td valign=\"top\" align=\"left\"><input type=\"file\" name=\"imgfile\"><span class=\"sm\"> [".GIFORJPG."]</td><td></td></tr><tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".IMAGEALTTEXT.":</td><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"alt\" value=\"$thisalt\" size=\"40\" maxlength=\"255\"><br><span class=\"sm\">[".THISISTHETEXT."]</td><td width=\"80\">&nbsp;</td></tr><tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".LINKTEXT.":</td><td valign=\"top\" align=\"left\"><textarea name=\"linktext\" cols=\"40\" rows=\"5\">$thislinktext</textarea><br><span class=\"sm\">[".HTMLCODECANBEUSED."]</td><td width=\"80\" valign=\"bottom\" align=\"left\"><input type=\"hidden\" name=\"linkid\" value=\"1\"><input type=\"hidden\" name=\"redirect\" value=\"$ashopurl/affiliate/signupform.php\"><input type=\"submit\" value=\"".UPDATE."\" onClick=\"uploadmessagetwotier()\" name=\"submitbutton\"></td></tr></table></form></p><hr width=\"600\">";
	}
}

echo "
	  <p><form action=\"affiliatecodes.php\" method=\"post\" enctype=\"multipart/form-data\" name=\"addform\">
      <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#E5E5E5\">
	  <tr class=\"formtitle\"><td valign=\"top\" colspan=\"3\" align=\"left\">".ADDANEWLINK.":</td></tr>
	  <tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".CATEGORY.":</td><td valign=\"top\" align=\"left\"><select name=\"linkcategoryid\">";
	  foreach($linkcategories as $linkcategoryid=>$linkcategoryname) echo "<option value=\"$linkcategoryid\">$linkcategoryname</option>";
	  echo "</td><td></td></tr>
      <tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".IMAGE.":</td><td valign=\"top\" colspan=\"2\" align=\"left\"><input type=\"file\" name=\"imgfile\" size=\"60\"><br><span class=\"sm\"> [".GIFORJPG."]</span></td></tr>
<tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".IMAGEALTTEXT.":</td><td valign=\"top\" colspan=\"2\" align=\"left\"><input type=\"text\"name=\"alt\" size=\"60\" maxlength=\"255\"><br><span class=\"sm\">[".THISISTHETEXT."]</span></td></tr>
	<tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".URL.":</td><td valign=\"top\" colspan=\"2\" align=\"left\"><input type=\"text\" name=\"redirect\" value=\"$thisredirect\" size=\"60\" maxlength=\"255\"><br><span class=\"sm\">[".CLICKTHROUGHSWILL."]</span></td></tr>
	  <tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".LINKTEXT.":</td><td valign=\"top\" align=\"left\"><textarea name=\"linktext\" cols=\"46\" rows=\"5\"></textarea><br><span class=\"sm\">[".HTMLCODECANBEUSED."<br>".ORAFFILIATECLOAK."]</span></td><td width=\"80\" align=\"right\" valign=\"bottom\"><input type=\"submit\" value=\"".ADD."\" onClick=\"uploadmessage()\" name=\"submitbutton\"></td></tr></table></form></p>";

// Order by category...
foreach($linkcategories as $linkcategoryid=>$linkcategoryname) {

// Get link code information from database...
$sql="SELECT * FROM linkcodes WHERE linkid > 1 AND linkcategoryid='$linkcategoryid'";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result)) echo "<p class=\"formtitle\" align=\"center\">$linkcategoryname:</p>";
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
	$thislinkcategoryid = @mysql_result($result, $i, "linkcategoryid");
    $thislinktext = @mysql_result($result, $i, "linktext");
    $thisfilename = @mysql_result($result, $i, "filename");
    $thislinkid = @mysql_result($result, $i, "linkid");
    $thisredirect = @mysql_result($result, $i, "redirect");
	$newlinktext = str_replace("%affiliatelink%","$ashopurl/affiliate.php?id=0&redirect=$thisredirect",$thislinktext);
	$newlinktext = str_replace("%affiliatecloaklink%","&lt;a href=\"$ashopurl\" onClick=\"window.open('$ashopurl/affiliate.php?id=0', 'PGM', 'scrollbars=yes, toolbar=yes, status=yes, menubar=yes location=yes resizable=yes'); return false;\"&gt;",$thislinktext);
	$newlinktext2 = str_replace("&gt;",">",$newlinktext);
	$newlinktext2 = str_replace("&lt;","<",$newlinktext2);
    $thisalt = @mysql_result($result, $i, "alt");
    echo "<p><form action=\"affiliatecodes.php\" method=\"post\">
      <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#D0D0D0\"><tr class=\"formlabel\"><td colspan=\"3\" align=\"center\" valign=\"top\">";
	if ($thisfilename) echo "<img src=\"../banners/$thisfilename\" alt=\"$thisalt\"><br>";
	echo "$newlinktext2</td></tr>
	<tr class=\"formlabel\"><td valign=\"top\" align=\"right\" width=\"88\">".CATEGORY.":</td><td valign=\"top\" align=\"left\"><select name=\"linkcategoryid\">";
	foreach($linkcategories as $linkcategoryid=>$linkcategoryname) {
		echo "<option value=\"$linkcategoryid\"";
		if ($thislinkcategoryid == $linkcategoryid) echo " selected";
		echo ">$linkcategoryname</option>";
	}
	echo "</td><td></td></tr>";
if ($thisfilename) echo "<tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".IMAGEALTTEXT.":</td><td valign=\"top\" align=\"left\"><input type=\"text\"name=\"alt\" size=\"60\" maxlength=\"255\" value=\"$thisalt\"></td><td></td></tr>";
echo "<tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".URL.":</td><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"redirect\" value=\"$thisredirect\" size=\"60\" maxlength=\"255\"></td><td></td></tr><tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".LINKTEXT.":</td><td valign=\"top\" align=\"left\"><textarea name=\"linktext\" cols=\"46\" rows=\"5\">$thislinktext</textarea></td><td width=\"80\" valign=\"bottom\" align=\"left\"><input type=\"hidden\" name=\"linkid\" value=\"$thislinkid\"><input type=\"submit\" value=\"".UPDATE."\" name=\"submitbutton\" style=\"margin-bottom: 3px;\"><br><input type=\"submit\" value=\"".DELETELINK."\" name=\"deletebutton\"></td></tr></table></form>";
}
}
echo "</center>$footer";
?>