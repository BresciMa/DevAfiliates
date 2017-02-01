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

// ----------------------------------------------------------------------- //
//																		   //
//      Edit the following parameters to correspond to your database       //
//		server, name and the username + password used to access your	   //
//		database.														   //
//																		   //
// ----------------------------------------------------------------------- //

//prod
//$databaseserver = "localhost"; // <-- change to your database server
//$databasename = "ecommer2_ashopaffiliate"; // <-- change to your database name
//$databaseuser = "ecommer2_ashop"; // <-- change to the username for your database
//$databasepasswd = "p!CL0!fPeBF6"; // <-- change to the password for your database

//local
$databaseserver = "localhost"; // <-- change to your database server
$databasename = "ashopaffiliate"; // <-- change to your database name
$databaseuser = "root"; // <-- change to the username for your database
$databasepasswd = ""; // <-- change to the password for your database


$noinactivitycheck = "true";
$adminpanelcolor = "7589e7";

// ----------------------------------------------------------------------- //
//																		   //
//						 Do not edit below this!						   //
//																		   //
// ----------------------------------------------------------------------- //

// Get preferences from the database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd") or die("Error! Could not connect to database server!");
@mysql_select_db("$databasename",$db) or die("Error! The database does not exist on this server!");
$result = @mysql_query("SELECT * FROM preferences");
if (@mysql_num_rows($result)) while ($row = @mysql_fetch_array($result)) $$row["prefname"] = $row["prefvalue"];
if (empty($ashoppath) && empty($updating)) {
	header("Location: install.php");
	exit;
}

// Version check...
include "$ashoppath/admin/version.inc.php";
if ($ashopversion != $version && !$updating) die("<b>Error!</b> Version mismatch! Run the update script.");

// Fix incompatible php settings...
if (ini_get("register_globals") != 1 || !get_magic_quotes_gpc()){
	include "$ashoppath/admin/vars.inc.php";
}

error_reporting (E_ALL ^ E_NOTICE);
if (strlen($_COOKIE["basket"]) > 800) exit;
?>