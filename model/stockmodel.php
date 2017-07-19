<?php

require("test_input.php");
require("conn.php");

$conn = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$type = test_input("type");
	if($type == '0') {
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
	if($result = $conn->query("SELECT MAX(id) FROM stock_names")){
		$large = $result->fetch_row();
		if((int) $large[0] >= 0){
			if($large[0] != null){
				$stock_id = (int) $large[0] + 1;
			} else {
				$stock_id = 1;
			}
			$tablename = "stock_".$stock_id;
		} else {
		//Replace
			die("Can't get stock_names highest id");
		}
	} else {
		//Replace
		die("Failed to set tablename: ".$conn->error);
	}

	//Query to be executed
	$query = "CREATE TABLE ".$tablename." (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, model VARCHAR(200) character set utf8 NOT NULL, registered_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP";

	$name = test_input("stock_name");
	$data_number = test_input("data_number");

	$data_names = array();
	$data_types = array();
	$data_lengths = array();

	//Part of query that contains column names & types
	$columnstring = "";

	for($i = 0; $i < $data_number; $i++){
		$data_name = test_input("data_name_".($i+1));
		$data_type = test_input("data_type_".($i+1));
		$data_notnull = test_input_radio("data_notnull_".($i+1))==1 ? "NOT NULL":"NULL";

		if($data_type==1 || $data_type==2) {
			$data_length = test_input("data_length_".($i+1));
		} else {
			$data_length = 0;
		}

		//Save names & types for "stockattrs" table
		$data_names[] = $data_name;
		$data_types[] = $data_type;

		//Formatting column names and types
		$columnstring .= ",c".$i." ";
		switch($data_type){
			case 0: $data_type = "int"; break; 
			case 1: $data_type = "char(".$data_length.") character set utf8"; break; 
			case 2: $data_type = "varchar(".$data_length.") character set utf8"; break; 
			case 3: $data_type = "date"; break; 
			case 4: $data_type = "text character set utf8"; break; 
			default: {
				//Replace
				die("Invalid data type for '".$data_name."' : ".$data_type);
			}
		}

		$data_lengths[] = $data_length;
		$columnstring .= $data_type." ".$data_notnull;
	}

	//Add column names, types and end of query /")"/
	$query .= $columnstring.");";

	//Creating table
	if(!$conn->query($query)){
		//Replace
		die("Error on executing query:".$query."<br>".$conn->error);
	}

	//Add table prorperties to stocknames and stockattrs
	$query = "INSERT INTO stock_names(id, _name, _tablename) VALUES (?, ?, ?)";
	if(!$stmt = $conn->prepare($query)){
		//Replace
		$conn->query("DROP TABLE ".$tablename);
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	if(!$stmt->bind_param("iss",$stock_id ,$name, $tablename)){
		//Replace 
		$conn->query("DROP TABLE ".$tablename);
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	}

	if(!$stmt->execute()){
		//Replace
		$conn->query("DROP TABLE ".$tablename);
		die("Error on executing query:".$query."<br>".$conn->error);
	}

	//Preparing connection to insert new stock attributes /id, Name, Columnname, Type/
	$query = "INSERT INTO stock_attrs(id, _name, _columnname, _datatype, _datalength) VALUES (?, ?, ?, ?, ?)";
	if(!$stmt = $conn->prepare($query)){
		//Replace
		$conn->query("DELETE FROM stock_names WHERE id=".$stock_id);
		$conn->query("DROP TABLE ".$tablename);
		die("Error on prepare query:".$query."<br>".$conn->error);
	}

	$column_name =  "";
	$data_name = "";
	$data_type = 0;
	$data_length = 0;
	
	if(!$stmt->bind_param("issii", $stock_id, $data_name, $column_name, $data_type, $data_length)){
		//Replace 
		$conn->query("DELETE FROM stock_names WHERE id=".$stock_id);
		$conn->query("DROP TABLE ".$tablename);
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	}

	//Inserting stock attributes
	for($i = 0; $i < $data_number; $i++){
		$column_name = "c".$i;
		$data_name = $data_names[$i];
		$data_type = $data_types[$i];
		$data_length = $data_lengths[$i];

		if(!$stmt->execute()){
			//Replace
			$conn->query("DELETE FROM stock_attrs WHERE id=".$stock_id);
			$conn->query("DELETE FROM stock_names WHERE id=".$stock_id);
			$conn->query("DROP TABLE ".$tablename);
			die("Error on executing query:".$query."<br>".$conn->error);
		}
	}
}

function editStockType(){
	global $conn;
}

function insertStock(){
	$stock_id = test_input("stock_id");
	$data = array();
	$datatype = "";
	$query1 = "INSERT INTO stock_".$stock_id."( model";
	$query2 = " VALUES (? ";

	$data[] = test_input("model");

	//Retrieving table column info and input from user
	if($result = $conn->query("SELECT _columnname, _datatype, _datalength, _name FROM stock_attrs WHERE id=".$stock_id)){
		while ($column = $result->fetch_row()){
			//Building query
			$query1 .= ",".$column[0];
			$query2 .= ", ?";

			$data = test_input($column[0]);

			//Building 'issss' param
			if($column[1]=="0"){
				$datatype .= "i";
			} else {
				$datatype .= "s";
			}

			//Checking if data lengths exceeded from 
			if($column[1]=="1" || $column[1]=="2"){
				if((int)$column[2] < strlen($data)){
					//Replace
					die($column[3]." data length exceeded: ".$conn->error);
				}
			}

		}
	} else {
		//Replace
		die("Failed to retrieve table column info: ".$conn->error);
	}

	//Building complete query
	$query = $query1.")".$query2.")";

	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Almost equal to bind_param("isss",[params]);
	$refArr = array_merge(array($datatype),$data); 
	$ref = new ReflectionClass('mysqli_stmt'); 
	$method = $ref->getMethod("bind_param"); 

	if(!$method->invokeArgs($stmt,$refArr)){
		die($data_types[$i]."Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	if(!$stmt->execute()){
		//Replace
		die("Error on executing query:".$query."<br>".$conn->error);
	}

}

function removeStock(){
}

?>