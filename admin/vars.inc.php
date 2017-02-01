<?php

if (ini_get("register_globals") != 1) {
	foreach ($_FILES as $key => $varvalue) {
		if (isset($key) && isset($varvalue)) {
			eval("\$$key = \"{$varvalue['tmp_name']}\";");
			eval("\${$key}_name = \"{$varvalue['name']}\";");
			eval("\${$key}_type = \"{$varvalue['type']}\";");
			eval("\${$key}_size = \"{$varvalue['size']}\";");
		}
	}
}

if (ini_get("register_globals") != 1 || !get_magic_quotes_gpc()) {
	
	foreach ($_GET as $key => $varvalue) {
		$key = str_replace("-","",$key);
		if (isset($key) && isset($varvalue)) {
			if (is_array($varvalue)) {
				foreach ($varvalue as $arraykey=>$arrayvalue) {
					if (!get_magic_quotes_gpc()) $arrayvalue = addslashes($arrayvalue);
					${$key}["$arraykey"] = $arrayvalue;
				}
			} else {
				if (!get_magic_quotes_gpc()) $varvalue = addslashes($varvalue);
				${$key} = $varvalue;
			}
		}
	}
	
	foreach ($_POST as $key => $varvalue) {
		$key = str_replace("-","",$key);
		if (isset($key) && isset($varvalue)) {
			if (is_array($varvalue)) {
				foreach ($varvalue as $arraykey=>$arrayvalue) {
					if (!get_magic_quotes_gpc()) $arrayvalue = addslashes($arrayvalue);
					${$key}["$arraykey"] = $arrayvalue;
				}
			} else {
				if (!get_magic_quotes_gpc()) $varvalue = addslashes($varvalue);
				${$key} = $varvalue;
			}
		}
	}
	
	foreach ($_COOKIE as $key => $varvalue) {
		$key = str_replace("-","",$key);
		
		if (isset($key) && isset($varvalue)) {
			if (is_array($varvalue)) {
				foreach ($varvalue as $arraykey=>$arrayvalue) {
					echo ".";
					if (!get_magic_quotes_gpc()) $arrayvalue = addslashes($arrayvalue);
					${$key}["$arraykey"] = $arrayvalue;
				}
			} else {
				if (!get_magic_quotes_gpc()) $varvalue = addslashes($varvalue);
				${$key} = $varvalue;
			}
		}
	}
	
	foreach ($_SERVER as $key => $varvalue) {
		$key = str_replace("-","",$key);
		if (isset($key) && isset($varvalue)) {
			if (is_array($varvalue)) {
				foreach ($varvalue as $arraykey=>$arrayvalue) {
					if (!get_magic_quotes_gpc()) $arrayvalue = addslashes($arrayvalue);
					${$key}["$arraykey"] = $arrayvalue;
				}
			} else {
				if (!get_magic_quotes_gpc()) $varvalue = addslashes($varvalue);
				${$key} = $varvalue;
			}
		}
	}
	
}

?>