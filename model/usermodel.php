<?php

echo "Initialized";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$type = test_input($_POST["type"]);
	echo $type;
	if($type == '1') {
		insertUser();
	} else if($type == '2'){
		editUser();
	} else if($type == '3'){
		removeUser();
	}
} else {
	//Replace
	die("Error: " . $sql . "<br>" . $conn->error);
}

require("dbinfo.php");

$conn = new mysqli($servername, $username, $pw, $dbname);

initcon($conn);

function insertUser(){
	$name = test_input($_POST["name"]);
	$email = test_input($_POST["email"]);
	$password = test_input($_POST["password"]);
	$mobile = test_input($_POST["mobile"]);
	//Replace
	$stmt = $conn->prepare("INSERT INTO users (email, name, password ) VALUES (?, ?, ?)");
	$stmt->bind_param("sss", $email, $name, $password);
	if ($stmt->execute() === TRUE) {
		//Inserted
		$last_id = $conn->insert_id;
	} else {
		//Replace
		die("Error: " . $sql . "<br>" . $conn->error);
	}
}

function editUser(){
	$name = test_input($_POST["name"]);
	echo "Success: ".$name;
}

function initcon($conn){
	if ($conn->connect_error) {
	//Replace
		die("Connection failed: " . $conn->connect_error);
	} 

	if (!$conn->set_charset("utf8")) {
	//Replace
		die("Error loading character set utf8: ".$mysqli->error);
	} 
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>