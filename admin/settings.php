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
include "language/$adminlang/configure.inc.php";

if ($userid != "1") {
	header("Location: editmember.php");
	exit;
}

echo "$header
<div class=\"heading\">".CONFIGURATION."</div>";
if ($passworderror == "old") echo "<CENTER><P><font size=\"3\" color=\"#FF0000\"><b>".OLDPASSWORDINCORRECT."</b></font></P></CENTER>";
else if ($passworderror == "new") echo "<CENTER><P><font size=\"3\" color=\"#FF0000\"><b>".PASSWORDSDIDNOTMATCH."</b></font></P></CENTER>";
else "<CENTER><P><B>".SETTINGSUPDATED."</B></P></CENTER>";
echo $footer;
?>