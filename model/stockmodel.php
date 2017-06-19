<?php

if (!$_SERVER["REQUEST_METHOD"] == "POST") {
	die("Incorrect input");
}

$type = test_input($_POST["type"]);

require("dbinfo.php");

$conn = new mysqli($servername, $username, $pw, $dbname);

if ($conn->connect_error) {
	//Replace
	die("Connection failed: " . $conn->connect_error);
} 

function insertStock($data){
	
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>