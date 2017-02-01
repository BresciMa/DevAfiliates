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

@set_time_limit(600);

include "config.inc.php";
include "checklogin.inc.php";
header("Content-disposition: filename=ashop.sql");
header("Content-type: application/octetstream");
header("Pragma: no-cache");
header("Expires: 0");

// Open database connection...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);
   
// Use the right type of linebreak...
$crlf="\n";
if(preg_match('/[^(]*\((.*)\)[^)]*/',getenv("HTTP_USER_AGENT"),$regs)) {
	$os = $regs[1];
	if (preg_match("/Win/i",$os)) $crlf="\r\n";
}

$tables = @mysql_query("SHOW TABLES FROM $databasename", $db);

$num_tables = @mysql_num_rows($tables);
if($num_tables == 0)
{
    echo "No tables found in database.";
} else {
    $i = 0;
    print "# AShop MySQL-Dump for $ashopname$crlf";

    while($i < $num_tables) { 
        $table = @mysql_tablename($tables, $i);

		// Dump table structure...
        print $crlf;
        print "# --------------------------------------------------------$crlf";
        print "#$crlf";
        print "# Table structure for table '$table'$crlf";
        print "#$crlf";
        print $crlf;

		$createquery = "DROP TABLE IF EXISTS $table;$crlf";
		$createquery .= "CREATE TABLE $table ($crlf";

		// Get table fields...
		$result = @mysql_query("SHOW FIELDS FROM $table", $db);
		if (!$result) {
			echo "Error$crlf";
			echo "SQL-query: SHOW FIELDS FROM $table$crlf"."MySQL said: ".mysql_error()."$crlf";
			exit;
		}
		while($row = @mysql_fetch_array($result)) {
			$createquery .= "   $row[Field] $row[Type]";
			if(isset($row["Default"]) && (!empty($row["Default"]) || $row["Default"] == "0")) $createquery .= " DEFAULT '$row[Default]'";
			if($row["Null"] != "YES") $createquery .= " NOT NULL";
			if($row["Extra"] != "") $createquery .= " $row[Extra]";
			$createquery .= ",$crlf";
		}
		$createquery = preg_replace("/,".$crlf."$/", "", $createquery);

		// Get keys...
		$result = @mysql_query("SHOW KEYS FROM $table", $db);
		if (!$result) {
			echo "Error$crlf";
			echo "SQL-query: SHOW KEYS FROM $table$crlf"."MySQL said: ".mysql_error()."$crlf";
			exit;
		}

		unset($index);
		unset($x);
		unset($columns);
		while($row = @mysql_fetch_array($result)) {
			$kname=$row['Key_name'];
			if(($kname != "PRIMARY") && ($row['Non_unique'] == 0)) $kname="UNIQUE|$kname";
			if(!isset($index[$kname])) $index[$kname] = array();
			$index[$kname][] = $row['Column_name'];
		}

		while(list($x, $columns) = @each($index)) {
			$createquery .= ",$crlf";
			if($x == "PRIMARY") $createquery .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
			elseif (substr($x,0,6) == "UNIQUE") $createquery .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
			else $createquery .= "   KEY $x (" . implode($columns, ", ") . ")";
		}
		$createquery .= "$crlf)";
		echo stripslashes($createquery).";$crlf$crlf";

        print "#$crlf";
        print "# Dumping data for table '$table'$crlf";
        print "#$crlf";
        print $crlf;

		// Dump table contents...
		$result = @mysql_query("SELECT * FROM $table", $db);
		if (!$result) {
			echo "Error$crlf";
			echo "SQL-query: SELECT * FROM $table$crlf"."MySQL said: ".@mysql_error()."$crlf";
			exit;
		}
		while($row = @mysql_fetch_row($result)) {
			$tablelist = "(";
			for($j=0; $j<@mysql_num_fields($result);$j++) $tablelist .= @mysql_field_name($result,$j).", ";
			$tablelist = substr($tablelist,0,-2);
			$tablelist .= ")";
			$insertquery = "INSERT INTO $table $tablelist VALUES (";

			for($j=0; $j<mysql_num_fields($result);$j++) {
				if(!isset($row[$j])) $insertquery .= " NULL,";
				elseif($row[$j] != "") $insertquery .= " '".addslashes($row[$j])."',";
				else $insertquery .= " '',";
			}
			$insertquery = preg_replace("/,$/", "", $insertquery);
			$insertquery .= ")";
			echo trim($insertquery).";$crlf";
		}

		$i++;
	}
}
?>