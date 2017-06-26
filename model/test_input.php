<?php
function test_input($name) {
	if(!isset($_POST[$name]) || $_POST[$name]==null) {
		//Replace
		die("<br>No input for ".$name);
	}
	$data = $_POST[$name];
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
?>