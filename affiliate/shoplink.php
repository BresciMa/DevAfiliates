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
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_shoplink.inc.php";

if (!isset($promoteshop) || $promoteshop < 2) header("Location: signupform.php");

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

$result = @mysql_query("SELECT shopname FROM user WHERE userid='$promoteshop'",$db);
$shopname = @mysql_result($result,0,"shopname");

if (isset($affid) && $affid != "") {

  // Check if this affiliate exists...
  $sql="SELECT * FROM affiliate WHERE affiliateid='$affid'";
  $result = @mysql_query("$sql",$db);
  if (@mysql_num_rows($result)) {

	  // Generate HTML link code...
	  $affiliatelink = "&lt;a href=&quot;$ashopurl/affiliate.php?id=$affid&redirect=index.php?shop=$promoteshop\" target=\"_top\"&gt;".CLICKHERE."&lt;/a&gt;";

	  // Display HTML link code...
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	  echo "<table class=\"ashopaffiliateloginframe\"><tr><td>
	  <span class=\"ashopaffiliateheader\">".YOURHTMLCODE." <i>$shopname</i></span>
	  <p align=\"left\"><span class=\"ashopaffiliatetext2\">".COPYHTMLCODE."</span></p>
	  <textarea name=\"linkcode\" readonly cols=\"50\" rows=\"5\" align=\"top\">$affiliatelink</textarea>
	  <p><span class=\"ashopaffiliatetext2\"><a href=\"../mall.php\">".BACKTOMALL."</a></span></p>
	  </td></tr></table>";
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	  @mysql_close($db);
	  exit;
  } else {
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
	  echo "<table class=\"ashopmessagetable\">
	  <tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".NOTREGISTERED."</span></p>
	  <p><span class=\"ashopmessage\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
	  if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
	  else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
	  @mysql_close($db);
	  exit;
  }
}


// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");


echo "<table class=\"ashopaffiliateloginframe\"><tr><td>
  <span class=\"ashopaffiliateheader\">".PROMOTESHOP." <i>$shopname</i></span>
  <p align=\"left\"><span class=\"ashopaffiliatetext2\">".ENTERAFFILIATEID."</span></p>
<form method=\"post\" action=\"shoplink.php\">
    <table width=\"250\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr> 
        <td align=\"right\"><span class=\"ashopaffiliatetext2\">".AFFILIATEID.":</span></td>
        <td>&nbsp;<input type=\"text\" name=\"affid\" size=\"10\"></td>
        <td><input type=\"hidden\" name=\"promoteshop\" value=\"$promoteshop\"><input type=\"submit\" value=\"".SUBMIT."\"></td>
      </tr>
    </table>
  </form>
  <p><span class=\"ashopaffiliatetext2\">".NOAFFID." <a href=\"signupform.php\">".NEWAFFILIATE."</a><br><br>
  <a href=\"../mall.php\">".BACKTOMALL."</a></span>
  </p></td></tr></table>";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>