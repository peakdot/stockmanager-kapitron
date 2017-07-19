<?php
function initcon() {
	require("dbinfo.php");
	global $conn;

	$conn = new mysqli($servername, $username, $pw, $dbname);

	if ($conn->connect_error) {
		//Replace
		die("Connection failed: " . $conn->connect_error);
	} 

	if (!$conn->set_charset("utf8")) {
		//Replace
		die("Error loading character set utf8: ".$mysqli->error);
	} 
}

function closecon() {
	global $conn;
	if (!$conn -> close()){
		//Replace
		die("Can't close connection");
	}
}
?>