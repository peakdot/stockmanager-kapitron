<?php
function &test_input($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function &test_input_bin($name) {
	if(!isset($_POST[$name]) || $_POST[$name]==null) {
		//Replace
		return 0;
	} else {
		return 1;
	}
}

function &test_input_ex($name, $type, $null_allowed) {
	if(!isset($_GET[$name]) || $_GET[$name]==null || $_GET[$name]=="") {
		if($null_allowed)
			return null;

		//Replace
		die("<br>No input for ".$name.$_GET[$name]);
	}
	
	$data = $_GET[$name];

	if($type == 0 && (string)(int)$data != $data) {
		//Replace
		die("<br>Invalid input (expected integer) for ".$name.$_GET[$name]);
	}

	return test_input($data);	
}

function &test_input_ex_post($name, $type, $null_allowed) {
	if(!isset($_POST[$name]) || $_POST[$name]==null || $_POST[$name]=="") {
		if($null_allowed)
			return null;

		//Replace
		die("<br>No input for ".$name.$_POST[$name]);
	}
	
	$data = $_POST[$name];

	if($type == 0 && (string)(int)$data != $data) {
		//Replace
		die("<br>Invalid input (expected integer) for ".$name.$_POST[$name]);
	}

	return test_input($data);
}
?>