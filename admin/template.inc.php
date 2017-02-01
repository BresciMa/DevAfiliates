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

include "help.inc.php";
include "version.inc.php";
$thisscriptname = $_SERVER["PHP_SELF"];
$thisscriptname = substr($thisscriptname,strpos($thisscriptname,"admin/")+6);
include "pages.inc.php";
$versionarray = explode(".",$version);
if (is_array($versionarray)) $displayversion = $versionarray[0].".".$versionarray[1].".".$versionarray[2];
$thisyear = date("Y",time()+$timezoneoffset);
$displaydate = date("l, F j, Y",time()+$timezoneoffset);
$adminlang = "en";
include "language/$adminlang/menu.inc.php";
if (is_numeric($cat) && $contexthelppage != "editcatalogue") $querystring = "?cat=$cat";
$header = "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<title>$ashopname - ".ADMINPANEL."</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<meta name=\"Author\" content=\"AShop Software\">
<meta name=\"Keywords\" content=\"AShop, admin panel\">
<meta name=\"description\" content=\"AShop Admin Panel\">
<link rel=\"stylesheet\" href=\"admin.css\" />
<script type=\"text/javascript\" src=\"overlib.js\"></script>
<script language=\"JavaScript\">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf(\"#\")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf(\"?\"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>
<body onLoad=\"MM_preloadImages('images/contexthelpicon_over.gif')\">
<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>
<div class=\"wrapper\">
  <div class=\"headercontainer\">
  <div class=\"header\">
    <a href=\"index.php\"><img src=\"images/adminpanellogo.gif\" alt=\"AShop Affiliate - Version $displayversion\" border=\"0\" width=\"277\" height=\"46\" class=\"logo\" /></a>
    <div class=\"headerright\">
      <p align=\"left\">".LOGGEDINAS." $username<span class=\"separator\">|</span>$displaydate<span class=\"separator\">|</span><a href=\"login.php?logout\" class=\"link-logout\">".LOGOUT."</a><br /><a href=\"$ashopurl/affiliate/login.php\" target=\"_blank\"><img src=\"images/icon_affiliatereports.gif\" alt=\"".VIEWCATALOG."\" style=\"vertical-align: -30%; margin: 0px; padding: 0px; margin-top: 3px; border: 0; \" /></a> <a href=\"$ashopurl/affiliate/login.php\" target=\"_blank\">".VIEWCATALOG."</a></p>
     </div>
</div>
</div>
  <div class=\"menucontainer\">
  <div class=\"menubar\">
    <ul>
      <li><a href=\"javascript:void(0);\""; if (in_array($thisscriptname,$menu_configuration)) $header .= " id=\"selected\""; $header .= ">".CONFIGURATION."</a>
        <ul>
      	<li><a href=\"configure.php?param=shop\">".SHOPPARAMETERS."</a></li>
        <li><a href=\"configure.php?param=layout\">".LAYOUT."</a></li>
        <li><a href=\"configure.php?param=affiliate\">".AFFILIATEPROGRAM."</a></li>
      </ul></li>
    </ul>
    <ul>
      <li class=\"narrowmenuitem\"><a class=\"narrowmenuitem\" href=\"javascript:void(0);\""; if (in_array($thisscriptname,$menu_affiliates)) $header .= " id=\"selected\""; $header .= ">".AFFILIATES."</a>
      <ul><li><a href=\"affiliateadmin.php\">".VIEWAFFILIATES."</a></li>
      <li><a href=\"affiliatestats.php\">".STATISTICS."</a></li></ul></li>
    </ul>
    <ul>
      <li class=\"narrowmenuitem\"><a class=\"narrowmenuitem\" href=\"javascript:void(0);\""; if (in_array($thisscriptname,$menu_linkcodes)) $header .= " id=\"selected\""; $header .= ">".LINKCODES."</a>
      <ul><li><a href=\"affiliatecodes.php\">".LINKCODES."</a></li>
	  <li><a href=\"affiliatecategories.php\">".LINKCATEGORIES."</a></li>
      <li><a href=\"affiliatetags.php\">".CUSTOMTAGS."</a></li></ul></li>
    </ul>
    <ul>
      <li><a href=\"javascript:void(0);\""; if (in_array($thisscriptname,$menu_customers)) $header .= " id=\"selected\""; $header .= ">".CUSTOMERS."</a><ul>
      <li><a href=\"salesadmin.php\">".VIEWCUSTOMERS."</a></li>
	  <li><a href=\"salesreport.php\">".SALESREPORTSMENU."</a></li>
	  <li><a href=\"bannedcustomers.php\">".BLACKLIST."</a></li>
	  </ul></li>
    </ul>";
if (!empty($autoresponderid) && is_numeric($autoresponderid) && !empty($activateautoresponder) && $activateautoresponder == "1") $header .= "
    <ul>
      <li><a href=\"http://www.autoresponder-service.com/index.php\" target=\"_blank\">".AUTORESPONDER."</a></li>
    </ul>";
$header .= "
    <div class=\"menubarright\"><a href=\"http://www.ashopsoftware.com/help/ashopv/\" target=\"_blank\"><img src=\"images/fam_help.gif\" width=\"16\" height=\"16\" alt=\"".ONLINEHELP."\" title=\"".ONLINEHELP."\" /></a> <a href=\"http://www.ashopsoftware.com/help/ashopv/\" target=\"_blank\">".ONLINEHELP."</a></div>
  </div>
 </div>
<div class=\"mainarea\">";

$footer = "
</div>
<div class=\"push\"></div>
</div>
        <div class=\"footer\"><div class=\"footerleft\">AShop Affiliate Version $displayversion</div><div class=\"footerright\">&copy; Copyright AShop Software 2002-$thisyear
        All Rights Reserved Worldwide</div></div>
</body>
</html>";
?>