<?php

require("test_input.php");

$conn = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$type = test_input("type");
	if($type == '1') {
		initcon();
		insertStockType();
		closecon();
	} else if($type == '2'){
		initcon();
		editStock();
		closecon();
	} else if($type == '3'){
		initcon();
		removeStock();
		closecon();
	}
} else {
	//Replace
	die("Error: Wrong method. Expected POST");
}

function insertStockType(){
	global $conn;

	$tablename = "";
	$stock_id = -1;
	if($result = $conn->query("SELECT MAX(id) FROM stocknames")){
		$large = $result->fetch_row();
		if($large[0] != null && (int) $large[0] >= 0){
			$stock_id = (int) $large[0] + 1;
			$tablename = "stocki".$stock_id;
		}
	} else {
		//Replace
		die("Failed to set tablename: ".$conn->error);
	}

	//Query to be executed
	$query = "CREATE TABLE ".$tablename." (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, model VARCHAR(200) NOT NULL, registered_date DATE NOT NULL";
	
	$name = test_input("name");
	$data_number = test_input("data_number");

	$data_names = array();
	$data_types = array();

	//Part of query that contains column names & types
	$columnstring = "";

	for($i = 0; $i < $data_number; $i++){
		$data_name = test_input("data_name_".$i);
		$data_type = test_input("data_type_".$i);
		$data_length = test_input("data_length_".$i);
		$data_notnull = test_input("data_notnull_".$i)==1 ? "NOT NULL":"NULL";

		//Save names & types for "stockattrs" table
		$data_names[] = $data_name;
		$data_types[] = $data_type;

		//Formatting column names and types
		$columnstring .= ",c".$i;
		switch($data_type){
			case 0: $data_type = "int";
			case 1: $data_type = "char(".$data_length.")";
			case 2: $data_type = "varchar(".$data_length.")";
			case 3: $data_type = "date";
			case 4: $data_type = "text";
			default: {
				//Replace
				die("Invalid data type for '".$data_name."'");
			}
		}

		$columnstring .= $data_type.$data_notnull;
	}

	//Add column names, types and end of query /")"/
	$query .= $columnstring.");";

	//Creating table
	if(!$conn->query($query)){
		//Replace
		die("Error on executing query:".$query);
	}

	//Add table prorperties to stocknames and stockattrs
	$query = "INSERT INTO stocknames(name, tablename) VALUES (?, ?)";
	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on executing query:".$query);
	}

	if(!$stmt->bind_param("ss", $name, $tablename)){
		//Replace 
		die("Error on binding parameters for query: ".$query);
	}

	if(!$conn->execute()){
		//Replace
		die("Error on executing query:".$query);
	}

	//Preparing connection to insert new stock attributes /Name, Columnname, Type/
	$query = "INSERT INTO stockattrs(id, name, columnname) VALUES (?, ?, ?)";
	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on executing query:".$query);
	}

	//Inserting stock attributes
	for($i = 0; $i < $data_number; $i++){
		if(!$stmt->bind_param("issi", $largest_id, $data_names[$i], "c".$i, $data_types[$i])){
			//Replace 
			die("Error on binding parameters for query: ".$query);
		}

		if(!$conn->execute()){
			//Replace
			die("Error on executing query:".$query);
		}
	}
}

function editStock(){
}

function removeStock(){
}

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