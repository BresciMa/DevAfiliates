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

if ($cancel) {
	header("Location: settings.php");
	exit;
}

if ($userid != "1") {
	header("Location: editmember.php");
	exit;
}
include "template.inc.php";
include "ashopconstants.inc.php";
include "ashopfunc.inc.php";
// Get language module...
include "language/$adminlang/configure.inc.php";

// Initiate password hasher for changing the admin panel password...
include "$ashoppath/includes/PasswordHash.php";
$passhasher = new PasswordHash(8, FALSE);

// Handle uploaded logo file...
if (is_uploaded_file($imgfile)) {
	$fileinfo = pathinfo("$imgfile_name");
	$extension = strtolower($fileinfo["extension"]);
	if ($extension == "gif" && is_writeable("$ashoppath/images")) {
		move_uploaded_file($imgfile, "$ashoppath/images/logo.gif");
		@chmod("$ashoppath/images/logo.gif", 0777);
	}
}

// Handle uploaded mobile logo file...
if (is_uploaded_file($mobimgfile)) {
	$fileinfo = pathinfo("$mobimgfile_name");
	$extension = strtolower($fileinfo["extension"]);
	if ($extension == "gif" && is_writeable("$ashoppath/images")) {
		move_uploaded_file($mobimgfile, "$ashoppath/images/logomobile.gif");
		@chmod("$ashoppath/images/logomobile.gif", 0777);
	}
}

// Open database connection...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Change administrators password...
$passworderrorstring = "";
if ($changeconfig && $oldpassword && $newpassword1 && $newpassword2) {
	$sql = "SELECT password FROM user WHERE username = 'ashopadmin'";
	$result = @mysql_query($sql, $db);
	$correctoldpassword = @mysql_result($result,0,"password");
	$passcheck = $passhasher->CheckPassword($oldpassword, $correctoldpassword);
	if (!$passcheck) $passworderrorstring = "?passworderror=old";
	if ($newpassword1 != $newpassword2) $passworderrorstring = "?passworderror=new";
	if (!$passworderrorstring) {
		$passhash = $passhasher->HashPassword($newpassword1);
		$sql = "UPDATE user SET password='$passhash' WHERE username='ashopadmin'";
		$result = @mysql_query($sql, $db);
		$headers = "From: ".un_html($ashopname)."<$ashopemail>\nX-Sender: <$ashopemail>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$ashopemail>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
		@ashop_mail("$ashopemail",un_html($ashopname)." - ".ADMINPASSWORDCHANGED,THEADMINPASSWORDAT." $ashopurl ".HASBEENCHANGED.": {$_SERVER["REMOTE_ADDR"]}!","$headers");
	}
}

if (strpos($header, "body") != 0) {
	$newheader = substr($header,1,strpos($header, "body")+3);
	$newheader .= " onUnload=\"closemessage()\" ".substr($header,strpos($header, "body")+4,strlen($header));
} else {
	$newheader = substr($header,1,strpos($header, "BODY")+3);
	$newheader .= " onUnload=\"closemessage()\" ".substr($header,strpos($header, "BODY")+4,strlen($header));
}

if (!$changeconfig) {
        if ($param == "layout") echo "$newheader<script language=\"JavaScript\">
		function uploadmessage() 
		{
		  if (document.configurationform.imgfile.value != '' || document.configurationform.mobimgfile.value != '') w = window.open('uploadmessage.html','_blank','toolbar=no,location=no,width=350,height=150');
	    }
        function closemessage()
        {
       	  if (typeof w != 'undefined') w.close();
        }
        </script>";
		else echo "$header";
		echo "
<div class=\"heading\">";
		switch ($param) {
			case "shop":
				echo SHOPPARAMETERS;
			    break;
			case "layout":
				echo LAYOUT;
				break;
			case "affiliate":
				echo AFFILIATEPROGRAM;
			    break;
		}
		echo "</div><table align=\"center\" cellpadding=\"10\"><tr><td>
        <form action=\"configure.php?changeconfig=1\" method=\"post\" name=\"configurationform\" enctype=\"multipart/form-data\">
		<table width=\"600\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F0F0F0\">";
}

if ($param == "shop") {
	if (!$changeconfig) {
		// Get list of languages...
		$langlist = "";
		$findfile = opendir("$ashoppath/language");
		while ($foundfile = readdir($findfile)) {
			if(is_dir("$ashoppath/language/$foundfile") && strlen($foundfile) == 2 && $foundfile != ".." && file_exists("$ashoppath/language/$foundfile/lang.cfg.php")) {
				$fp = fopen ("$ashoppath/language/$foundfile/lang.cfg.php","r");
				while (!feof ($fp)) {
					$fileline = fgets($fp, 4096);
					if (strstr($fileline,"\$langname")) $langnamestring = $fileline;
					if (strstr($fileline,"\$langredirect")) $langredirectstring = $fileline;
				}
				fclose($fp);
				eval ($langnamestring);
				if ($language == $foundfile) eval ($langredirectstring);
				$langlist .= "<option value=\"$foundfile\"";
				if ($defaultlanguage == $foundfile) $langlist .= " selected";
				$langlist .= ">$langname</option>";
			}
		}

		// Get context help for this page...
		$contexthelppage = "shopparameters";
		include "help.inc.php";
		echo "<input type=\"hidden\" name=\"param\" value=\"shop\">
		<tr><td width=\"45%\" class=\"formtitle\">".CHANGEPASSWORD." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image1','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image1\" align=\"absmiddle\" onclick=\"return overlib('$tip1');\" onmouseout=\"return nd();\"></a></td><td width=\"55%\"><span class=\"sm\">[".LEAVEBLANKTOKEEP."]</span></td></tr>
		<tr><td align=\"right\" class=\"formlabel\"><span >".OLDPASSWORD.": </td><td><input type=\"password\" name=\"oldpassword\" size=\"25\"></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".NEWPASSWORD.": </td><td><input type=\"password\" name=\"newpassword1\" size=\"25\"></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".CONFIRM.": </td><td><input type=\"password\" name=\"newpassword2\" size=\"25\"></td></tr></table>
<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">
		<tr bgcolor=\"#D0D0D0\"><td colspan=\"2\" class=\"formtitle\">".CONTACTINFO." <a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image2','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image2\" align=\"absmiddle\" onclick=\"return overlib('$tip2');\" onmouseout=\"return nd();\"></a></td></tr>
<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".SHOPNAME.":</td><td><input type=\"text\" name=\"nashopname\" size=\"35\" value=\"$ashopname\"><script language=\"JavaScript\">document.configurationform.nashopname.focus();</script></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".SHOPADDRESS.": </td><td><input type=\"text\" name=\"nashopaddress\" size=\"35\" value=\"$ashopaddress\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".SHOPPHONE.": </td><td><input type=\"text\" name=\"nashopphone\" size=\"35\" value=\"$ashopphone\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".EMAIL.": </td><td><input type=\"text\" name=\"nashopemail\" size=\"35\" value=\"$ashopemail\"></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td colspan=\"2\" class=\"formtitle\">".CATALOGOPTIONS."</td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".DEFAULTLANG.":</td><td><select name=\"ndefaultlanguage\">$langlist</select></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image3','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image3\" align=\"absmiddle\" onclick=\"return overlib('$tip3a');\" onmouseout=\"return nd();\"></a> ".CURRENCY.":</td><td><select name=\"nashopcurrency\"><option value=\"usd\""; if ($ashopcurrency == "usd") echo " selected"; echo ">".USDOLLARS."</option><option value=\"cad\""; if ($ashopcurrency == "cad") echo " selected"; echo ">".CANDOLLARS."</option><option value=\"mxn\""; if ($ashopcurrency == "mxn") echo " selected"; echo ">Mexican Peso</option><option value=\"aud\""; if ($ashopcurrency == "aud") echo " selected"; echo ">".AUSDOLLARS."</option><option value=\"nzd\""; if ($ashopcurrency == "nzd") echo " selected"; echo ">".NEWZDOLLARS."</option><option value=\"gbp\""; if ($ashopcurrency == "gbp") echo " selected"; echo ">Pounds Sterling</option><option value=\"eur\""; if ($ashopcurrency == "eur") echo " selected"; echo ">".EURO."</option><option value=\"nok\""; if ($ashopcurrency == "nok") echo " selected"; echo ">".NORKRONOR."</option><option value=\"sgd\""; if ($ashopcurrency == "sgd") echo " selected"; echo ">".SINGADOLLARS."</option><option value=\"sek\""; if ($ashopcurrency == "sek") echo " selected"; echo ">".SWEDKRONOR."</option><option value=\"zar\""; if ($ashopcurrency == "zar") echo " selected"; echo ">".SARAND."</option><option value=\"twd\""; if ($ashopcurrency == "twd") echo " selected"; echo ">".TAIWANDOLLARS."</option><option value=\"tec\""; if ($ashopcurrency == "tec") echo " selected"; echo ">".TECREDITS."</option></select></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".THOUSANDSEPARATOR.":</td><td><select name=\"nthousandchar\"><option value=\"\""; if ($thousandchar == "") echo " selected"; echo ">".NONE."</option><option value=\",\""; if ($thousandchar == ",") echo " selected"; echo ">".COMMA."</option><option value=\".\""; if ($thousandchar == ".") echo " selected"; echo ">".DOT."</option><option value=\" \""; if ($thousandchar == " ") echo " selected"; echo ">".SPACE."</option></select></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".DECIMALSEPARATOR.":</td><td><select name=\"ndecimalchar\"><option value=\",\""; if ($decimalchar == ",") echo " selected"; echo ">".COMMA."</option><option value=\".\""; if ($decimalchar == ".") echo " selected"; echo ">".DOT."</option></select></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image8','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image8\" align=\"absmiddle\" onclick=\"return overlib('$tip3f');\" onmouseout=\"return nd();\"></a> <a href=\"$help3f\" class=\"helpnav2\" target=\"_blank\">".ASHOPAFFILIATEID.":</a></td><td><input type=\"text\" name=\"nashopaffiliateid\" size=\"2\" value=\"$ashopaffiliateid\"></td></tr>";
		if (!empty($autoresponderid) && is_numeric($autoresponderid)) {
			echo "<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".ACTIVATEAUTORESPONDER.":</a></td><td><input type=\"checkbox\" name=\"nactivateautoresponder\""; if ($activateautoresponder == "1") echo "checked"; echo "></td></tr>";
			$sql = "SELECT * FROM autoresponders ORDER BY name";
			$responderresult = @mysql_query($sql);
			if (@mysql_num_rows($responderresult)) {
				echo "<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".NEWSLETTERAUTORESPONDER.":</td><td class=\"formlabel\"><select name=\"nnewsresponderid\"><option value=\"0\">none</option>";
				for ($i = 0; $i < @mysql_num_rows($responderresult); $i++) {
					$responderid = @mysql_result($responderresult, $i, "responderid");
					$respondername = @mysql_result($responderresult, $i, "name");
					echo "<option value=\"$responderid\"";
					if ($newsresponderid == $responderid) echo " selected";
					echo ">$respondername</option>";
				}
				echo "</select></td></tr>";
			}
		}
		echo "
		<tr bgcolor=\"#F0F0F0\"><td colspan=\"2\" class=\"formtitle\">".SEOSETTINGS." <a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image18','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image18\" align=\"absmiddle\" onclick=\"return overlib('$tip14');\" onmouseout=\"return nd();\"></a></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".METAKEYWORDS.":</td><td class=\"formlabel\"><textarea name=\"nashopmetakeywords\" cols=\"30\" rows=\"5\">$ashopmetakeywords</textarea></td></tr>
		<tr bgcolor=\"#F0F0F0\"><td align=\"right\" class=\"formlabel\">".METADESCRIPTION.":</td><td class=\"formlabel\"><textarea name=\"nashopmetadescription\" cols=\"30\" rows=\"5\">$ashopmetadescription</textarea></td></tr>
		";
		} else {
		$nashopname = htmlentities(stripslashes($nashopname), ENT_QUOTES);
		$nashopphone = htmlentities(stripslashes($nashopphone), ENT_QUOTES);
		$nashopaddress = htmlentities(stripslashes($nashopaddress), ENT_QUOTES);
		if ($nactivateautoresponder == "on") $nactivateautoresponder = "1";
		else $nactivateautoresponder = "0";
		@mysql_query("UPDATE preferences SET prefvalue='$nactivateautoresponder' WHERE prefname='activateautoresponder'");
		@mysql_query("UPDATE preferences SET prefvalue='$nnewsresponderid' WHERE prefname='newsresponderid'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopname' WHERE prefname='ashopname'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopphone' WHERE prefname='ashopphone'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopmetakeywords' WHERE prefname='ashopmetakeywords'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopmetadescription' WHERE prefname='ashopmetadescription'");
		@mysql_query("UPDATE preferences SET prefvalue='$ndecimalchar' WHERE prefname='decimalchar'");
		@mysql_query("UPDATE preferences SET prefvalue='$nthousandchar' WHERE prefname='thousandchar'");
		if ($ashopemail != $nashopemail) {
			$headers = "From: ".un_html($ashopname)."<$ashopemail>\nX-Sender: <$ashopemail>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$ashopemail>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
			@ashop_mail("$ashopemail",un_html($ashopname)." - main shopping cart email changed","The main email address of your shopping cart at $ashopurl has been changed. If you have changed it yourself you can discard this message. In other case you may have had an unauthorized login to your administration panel by IP: {$_SERVER["REMOTE_ADDR"]}! The email is now set to: $nashopemail.","$headers");
		}
		@mysql_query("UPDATE preferences SET prefvalue='$nashopemail' WHERE prefname='ashopemail'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopaddress' WHERE prefname='ashopaddress'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopcurrency' WHERE prefname='ashopcurrency'");
		@mysql_query("UPDATE preferences SET prefvalue='$ndefaultlanguage' WHERE prefname='defaultlanguage'");
		@mysql_query("UPDATE preferences SET prefvalue='$timezoneoffset' WHERE prefname='timezoneoffset'");
		@mysql_query("UPDATE preferences SET prefvalue='$nashopaffiliateid' WHERE prefname='ashopaffiliateid'");
	}
}

if ($param == "affiliate") {
	if (!$changeconfig) {
		// Get context help for this page...
		$contexthelppage = "affiliateconfiguration";
		include "help.inc.php";
		echo "<input type=\"hidden\" name=\"param\" value=\"affiliate\">
		<tr><td valign=\"top\" align=\"right\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image1','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image1\" align=\"absmiddle\" onclick=\"return overlib('$tip1');\" onmouseout=\"return nd();\"></a> <a href=\"$help1\" class=\"helpnav2\" target=\"_blank\">".AFFILIATETEXT.":</a></td><td class=\"formlabel\"><textarea name=\"naffiliateinfo\" cols=\"30\" rows=\"5\">$affiliateinfo</textarea><script language=\"JavaScript\">document.configurationform.naffiliateinfo.focus();</script></td></tr>
        <tr><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image2','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image2\" align=\"absmiddle\" onclick=\"return overlib('$tip2');\" onmouseout=\"return nd();\"></a> ".AFFILIATEEMAIL.":</td><td><input type=\"text\" name=\"naffiliaterecipient\" size=\"35\" value=\"$affiliaterecipient\"></td></tr>
		<tr><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image8','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image8\" align=\"absmiddle\" onclick=\"return overlib('$tip8');\" onmouseout=\"return nd();\"></a> ".VERIFYORDERS.":</td><td><input type=\"checkbox\" name=\"npending\""; if ($pending == "1") echo " checked"; echo "></td></tr>
		<tr><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image4','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image4\" align=\"absmiddle\" onclick=\"return overlib('$tip4');\" onmouseout=\"return nd();\"></a> <a href=\"$help4\" class=\"helpnav2\" target=\"_blank\">".MULTITIER.":</td><td><input type=\"checkbox\" name=\"nsecondtieractivated\""; if ($secondtieractivated == "1") echo " checked"; echo "></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".MAXTIERS.":</td><td><input type=\"text\" name=\"nmaxaffiliatetiers\" size=\"3\" value=\"$maxaffiliatetiers\"></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".UPGRADEAFFILIATESAFTER.":</td><td class=\"formlabel\"><input type=\"text\" name=\"nupgradeaffiliate\" size=\"3\" value=\"$upgradeaffiliate\"> ".ORDERS." <span class=\"sm\">[ 0 = ".DISABLE." ]</span></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".SHARELEADS.":</td><td><input type=\"checkbox\" name=\"nactivateleads\""; if ($activateleads == "1") echo " checked"; echo "></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td colspan=\"2\" class=\"formtitle\">".NORMAL."</td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image3','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image3\" align=\"absmiddle\" onclick=\"return overlib('$tip3');\" onmouseout=\"return nd();\"></a> ".DEFAULTAFFILIATECOMMISSION.":</td><td><input type=\"text\" name=\"naffiliatepercent\" size=\"3\" value=\"$affiliatepercent\"> %</td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".DEFAULTSECONDTIERCOMMISSION.":</td><td><input type=\"text\" name=\"nsecondtierpercent\" size=\"3\" value=\"$secondtierpercent\"> %</td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".DEFAULTWHOLESALECOMMISSION.":</td><td><input type=\"text\" name=\"nwholesalepercent\" size=\"3\" value=\"$wholesalepercent\"> %</td></tr>
		<tr><td colspan=\"2\" class=\"formtitle\">".UPGRADED."</td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".DEFAULTAFFILIATECOMMISSION.":</td><td><input type=\"text\" name=\"naffiliatepercent2\" size=\"3\" value=\"$affiliatepercent2\"> %</td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".DEFAULTSECONDTIERCOMMISSION.":</td><td><input type=\"text\" name=\"nsecondtierpercent2\" size=\"3\" value=\"$secondtierpercent2\"> %</td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".COMMISSIONONWHOLESALE.":</td><td><input type=\"checkbox\" name=\"nwholesaleaffiliate\""; if ($wholesaleaffiliate == "1") echo " checked"; echo "></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image7','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image7\" align=\"absmiddle\" onclick=\"return overlib('$tip7');\" onmouseout=\"return nd();\"></a> ".EMAILCONFIRMATION.":</td><td><input type=\"checkbox\" name=\"naffiliateconfirm\""; if ($affiliateconfirm == "1") echo " checked"; echo "></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image5','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image5\" align=\"absmiddle\" onclick=\"return overlib('$tip5');\" onmouseout=\"return nd();\"></a> ".REQUIREPAYPALID.":</td><td><input type=\"checkbox\" name=\"nrequirepaypalid\""; if ($requirepaypalid == "1") echo " checked"; echo "></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image6','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image6\" align=\"absmiddle\" onclick=\"return overlib('$tip6');\" onmouseout=\"return nd();\"></a> ".DEFAULTREDIRECTURL.":</td><td><input type=\"text\" name=\"naffiliateredirect\" size=\"35\" value=\"$affiliateredirect\"></td></tr>";
	} else {
		$naffiliateinfo = htmlentities(stripslashes($naffiliateinfo), ENT_QUOTES);
		if ($nsecondtieractivated == "on") $nsecondtieractivated = "1";
		else $nsecondtieractivated = "0";
		if ($nrequirepaypalid == "on") $nrequirepaypalid = "1";
		else $nrequirepaypalid = "0";
		if ($naffiliateconfirm == "on") $naffiliateconfirm = "1";
		else $naffiliateconfirm = "0";
		if ($nactivateleads == "on") $nactivateleads = "1";
		else $nactivateleads = "0";
		if ($nwholesaleaffiliate == "on") $nwholesaleaffiliate = "1";
		else $nwholesaleaffiliate = "0";
		if ($npending == "on") $npending = "1";
		else $npending = "0";
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliateinfo' WHERE prefname='affiliateinfo'");
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliaterecipient' WHERE prefname='affiliaterecipient'");
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliatepercent' WHERE prefname='affiliatepercent'");
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliatepercent2' WHERE prefname='affiliatepercent2'");
		@mysql_query("UPDATE preferences SET prefvalue='$nsecondtieractivated' WHERE prefname='secondtieractivated'");
		@mysql_query("UPDATE preferences SET prefvalue='$nsecondtierpercent' WHERE prefname='secondtierpercent'");
		@mysql_query("UPDATE preferences SET prefvalue='$nsecondtierpercent2' WHERE prefname='secondtierpercent2'");
		@mysql_query("UPDATE preferences SET prefvalue='$nwholesalepercent' WHERE prefname='wholesalepercent'");
		@mysql_query("UPDATE preferences SET prefvalue='$nrequirepaypalid' WHERE prefname='requirepaypalid'");
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliateredirect' WHERE prefname='affiliateredirect'");
		@mysql_query("UPDATE preferences SET prefvalue='$naffiliateconfirm' WHERE prefname='affiliateconfirm'");
		@mysql_query("UPDATE preferences SET prefvalue='$nmaxaffiliatetiers' WHERE prefname='maxaffiliatetiers'");
		@mysql_query("UPDATE preferences SET prefvalue='$nupgradeaffiliate' WHERE prefname='upgradeaffiliate'");
		@mysql_query("UPDATE preferences SET prefvalue='$nactivateleads' WHERE prefname='activateleads'");
		@mysql_query("UPDATE preferences SET prefvalue='$nwholesaleaffiliate' WHERE prefname='wholesaleaffiliate'");
		@mysql_query("UPDATE preferences SET prefvalue='$npending' WHERE prefname='pending'");
	}
}

if ($param == "layout") {
	if (!$changeconfig) {
		// Get context help for this page...
		$contexthelppage = "layout";
		include "help.inc.php"; 
		echo "<script language=\"JavaScript\">
		function colorpicker(formname,fieldname) 
		{
		  w = window.open('colors.php?form='+formname+'&field='+fieldname,'_blank','toolbar=no,location=no,width=450,height=100');
	    }
		function fontselect(formname,fieldname) 
		{
		  w = window.open('fonts.php?form='+formname+'&field='+fieldname,'_blank','toolbar=no,location=no,width=350,height=200');
	    }
		</script>
		<input type=\"hidden\" name=\"param\" value=\"layout\">
	<tr><td colspan=\"2\" class=\"formtitle\">".DEFAULTLOGOIMAGE." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image1','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image1\" align=\"absmiddle\" onclick=\"return overlib('$tip1');\" onmouseout=\"return nd();\"></a></td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".UPLOADLOGOIMAGE.":</td><td><input type=\"file\" name=\"imgfile\" size=\"20\"></td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".UPLOADMOBILELOGO.":</td><td><input type=\"file\" name=\"mobimgfile\" size=\"20\"></td></tr>
	<tr bgcolor=\"#D0D0D0\"><td class=\"formtitle\" colspan=\"2\">".THEMESELECTION." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image2','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image2\" align=\"absmiddle\" onclick=\"return overlib('$tip2');\" onmouseout=\"return nd();\"></a></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".THEME.":</td><td><select name=\"nashoptheme\"><option value=\"none\"";
		if ($ashoptheme == "none") echo " selected";
		echo ">".NONE."</option>";
		$findfile = opendir("$ashoppath/themes");
		$starttime = time();
		while ($founddir = readdir($findfile)) {
			if  (time()-$starttime > 180) exit;
			if (is_dir("$ashoppath/themes/$founddir") && $founddir != "." && $founddir != ".." && $founddir != ".htaccess" && !strstr($founddir, "CVS") && substr($founddir, 0, 1) != "_") {
				echo "<option value=\"$founddir\"";
				$fp = fopen ("$ashoppath/themes/$founddir/theme.cfg.php","r");
				if ($fp) {
					while (!feof ($fp)) {
						$fileline = fgets($fp, 4096);
						if (strstr($fileline,"\$themename")) $themenamestring = $fileline;
					}
					fclose($fp);
					eval ($themenamestring);
				}
				if ($ashoptheme == $founddir) echo " selected";
				echo ">$themename</option>";
			}
		}
		echo "</select></td></tr>
	<tr><td class=\"formtitle\">".PAGEBODYCOLORS." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image3','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image3\" align=\"absmiddle\" onclick=\"return overlib('$tip3');\" onmouseout=\"return nd();\"></a></td></tr>
<tr><td align=\"right\" class=\"formlabel\">".BACKGROUNDCOLOR.":</td><td><input type=\"text\" name=\"nbgcolor\" size=\"15\" value=\"$bgcolor\"><a href=\"javascript:colorpicker('configurationform','nbgcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a><script language=\"JavaScript\">document.configurationform.nbgcolor.focus();</script></td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".TEXTCOLOR.":</td><td><input type=\"text\" name=\"ntextcolor\" size=\"15\" value=\"$textcolor\"><a href=\"javascript:colorpicker('configurationform','ntextcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".ALERTCOLOR.":</td><td><input type=\"text\" name=\"nalertcolor\" size=\"15\" value=\"$alertcolor\"><a href=\"javascript:colorpicker('configurationform','nalertcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".HEADERBACKGROUNDCOLOR.":</td><td><input type=\"text\" name=\"ncatalogheader\" size=\"15\" value=\"$catalogheader\"><a href=\"javascript:colorpicker('configurationform','ncatalogheader')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
		<tr><td align=\"right\" class=\"formlabel\">".HEADERTEXTCOLOR.":</td><td><input type=\"text\" name=\"ncatalogheadertext\" size=\"15\" value=\"$catalogheadertext\"><a href=\"javascript:colorpicker('configurationform','ncatalogheadertext')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
	<tr bgcolor=\"#D0D0D0\"><td width=\"44%\" class=\"formtitle\" colspan=\"2\">".FORMSCOLORS." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image4','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image4\" align=\"absmiddle\" onclick=\"return overlib('$tip4');\" onmouseout=\"return nd();\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".FORMSBACKGROUNDCOLOR.":</td><td><input type=\"text\" name=\"nformsbgcolor\" size=\"15\" value=\"$formsbgcolor\"><a href=\"javascript:colorpicker('configurationform','nformsbgcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".FORMSTEXTCOLOR.":</td><td><input type=\"text\" name=\"nformstextcolor\" size=\"15\" value=\"$formstextcolor\"><a href=\"javascript:colorpicker('configurationform','nformstextcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".FORMSBORDERCOLOR.":</td><td><input type=\"text\" name=\"nformsbordercolor\" size=\"15\" value=\"$formsbordercolor\"><a href=\"javascript:colorpicker('configurationform','nformsbordercolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
	<tr bgcolor=\"#D0D0D0\"><td class=\"formtitle\" colspan=\"2\">".CATEGORYCOLORS." 
<a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image6','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image6\" align=\"absmiddle\" onclick=\"return overlib('$tip6');\" onmouseout=\"return nd();\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".CATEGORYCOLOR.":</td><td><input type=\"text\" name=\"ncategorycolor\" size=\"15\" value=\"$categorycolor\"><a href=\"javascript:colorpicker('configurationform','ncategorycolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".CATEGORYTEXTCOLOR.":</td><td><input type=\"text\" name=\"ncategorytextcolor\" size=\"15\" value=\"$categorytextcolor\"><a href=\"javascript:colorpicker('configurationform','ncategorytextcolor')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".SELECTEDCATEGORYCOLOR.":</td><td><input type=\"text\" name=\"nselectedcategory\" size=\"15\" value=\"$selectedcategory\"><a href=\"javascript:colorpicker('configurationform','nselectedcategory')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".SELECTEDCATEGORYTEXTCOLOR.":</td><td><input type=\"text\" name=\"nselectedcategorytext\" size=\"15\" value=\"$selectedcategorytext\"><a href=\"javascript:colorpicker('configurationform','nselectedcategorytext')\"><img src=\"images/colorpicker.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
	<tr><td class=\"formtitle\">".OTHERSETTINGS." 
</td></tr>
        <tr><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image7','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image7\" align=\"absmiddle\" onclick=\"return overlib('$tip7');\" onmouseout=\"return nd();\"></a> ".FONT.":</td><td><input type=\"text\" name=\"nfont\" size=\"25\" value=\"$font\"><a href=\"javascript:fontselect('configurationform','nfont')\"><img src=\"images/fontselect.gif\" border=\"0\" align=\"absmiddle\" width=\"20\" height=\"20\"></a></td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".SMALLTEXTSIZE.":</td><td class=\"formlabel\"><input type=\"text\" name=\"nfontsize1\" size=\"5\" value=\"$fontsize1\"> pixels</td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".REGULARTEXTSIZE.":</td><td class=\"formlabel\"><input type=\"text\" name=\"nfontsize2\" size=\"5\" value=\"$fontsize2\"> pixels</td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".LARGETEXTSIZE.":</td><td class=\"formlabel\"><input type=\"text\" name=\"nfontsize3\" size=\"5\" value=\"$fontsize3\"> pixels</td></tr>
        <tr><td align=\"right\" class=\"formlabel\">".REGULARTABLESIZE.":</td><td class=\"formlabel\"><input type=\"text\" name=\"ntablesize2\" size=\"5\" value=\"$tablesize2\"> pixels</td></tr>
		";
	} else {
		@mysql_query("UPDATE preferences SET prefvalue='$nashoptheme' WHERE prefname='ashoptheme'");
		@mysql_query("UPDATE preferences SET prefvalue='$nbgcolor' WHERE prefname='bgcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$ntextcolor' WHERE prefname='textcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$nalertcolor' WHERE prefname='alertcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$ncatalogheader' WHERE prefname='catalogheader'");
		@mysql_query("UPDATE preferences SET prefvalue='$ncatalogheadertext' WHERE prefname='catalogheadertext'");
		@mysql_query("UPDATE preferences SET prefvalue='$nformsbgcolor' WHERE prefname='formsbgcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$nformstextcolor' WHERE prefname='formstextcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$nformsbordercolor' WHERE prefname='formsbordercolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$ncategorycolor' WHERE prefname='categorycolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$ncategorytextcolor' WHERE prefname='categorytextcolor'");
		@mysql_query("UPDATE preferences SET prefvalue='$nselectedcategory' WHERE prefname='selectedcategory'");
		@mysql_query("UPDATE preferences SET prefvalue='$nselectedcategorytext' WHERE prefname='selectedcategorytext'");
		@mysql_query("UPDATE preferences SET prefvalue='$nfont' WHERE prefname='font'");
		@mysql_query("UPDATE preferences SET prefvalue='$nfontsize1' WHERE prefname='fontsize1'");
		@mysql_query("UPDATE preferences SET prefvalue='$nfontsize2' WHERE prefname='fontsize2'");
		@mysql_query("UPDATE preferences SET prefvalue='$nfontsize3' WHERE prefname='fontsize3'");
		@mysql_query("UPDATE preferences SET prefvalue='$ntablesize2' WHERE prefname='tablesize2'");
	}
}

if (!$changeconfig) {
	if ($param == "shop") {
		echo "<tr bgcolor=\"#F0F0F0\"><td colspan=\"2\"><table bgcolor=\"#3f71a2\" align=\"center\" width=\"100%\"><tr align=\"center\">";
		if ($digitalmall == "ON") echo "<td class=\"nav\" nowrap><a href=\"shopcategories.php\" class=\"nav\">".EDITSHOPCATEGORIES."</a></td>";
		echo "<td class=\"nav\"><a href=\"advancedoptions.php\" class=\"nav\">".ADVANCEDOPTIONS."</a> <a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image12','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image12\" align=\"absmiddle\" onclick=\"return overlib('$tip7');\" onmouseout=\"return nd();\"></a></td></tr></table></td></tr>";
	}
	echo "<tr bgcolor=\"#F0F0F0\"><td>&nbsp;</td><td align=\"right\"><input type=\"hidden\" name=\"cancel\" value=\"\"><input type=\"button\" value=\"".CANCEL."\" onClick=\"document.configurationform.cancel.value='true';document.configurationform.submit();\"> <input type=\"submit\" value=\"".SUBMIT."\"";
	if ($param == "layout") echo "onClick=\"uploadmessage()\"";
	echo "></td></tr></table></form></table>$footer";
} else {
	@mysql_close($db);
	if ($update) header("Location: configure.php?param=payment");
	else if ($updatetaxes) header("Location: configure.php?param=taxes");
	else header("Location: settings.php$passworderrorstring");
}
?>