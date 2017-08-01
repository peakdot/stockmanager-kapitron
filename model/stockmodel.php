<?php

require_once("test_input.php");
require_once("conn.php");

$conn = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$type = test_input_ex_post("type", 0, false);
	if($type == '0') {
		initcon();
		insertStockType(getStockTypeData());
		closecon();
	} else if($type == '1') {
		initcon();
		editStockType();
		closecon();
	} else if($type == '2'){
		initcon();
		removeStockType();
		closecon();
	} else if($type == '3'){
		initcon();
		insertStock(getStockDataFromUser());
		closecon();
	} else if($type == '4'){
		initcon();
		insertStockFromFile();
		closecon();
	}
} 

function insertStockFromFile(){
	include_once('PHPExcel.php');
	include_once('stockviewmodel.php');

	//getFile from user through http post method
	$filepath = getFileFromUser();

	try {
		/** Load $inputFileName to a PHPExcel Object  **/
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');

		$objPHPExcel = $objReader->load($filepath);

		$objWorksheet = $objPHPExcel->getSheet(0);
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

		$state = 0;
		$stock_name = "";
		$data_number = 0;
		$data_names = array();
		$data_types = array();
		$data_notnulls = array();
		$data_lengths = array();

		$stock_id = -1;
		$isss = "";
		$column_names = array();
		$datas_array = array();
		$datas = array();

		$stock_name_row = 0;

		//Ingesting table
		for ($row = 1; $row <= $highestRow; ++$row) {
			if($state == 0) {
				$cell = $objWorksheet->getCellByColumnAndRow(0, $row);
				if($cell->getValue()==null || $cell->getValue()=="") {
					continue;
				} else {
					$stock_name = $cell->getValue();
					$stock_name_row = $row;
					$state = 1;
				}
			} else if($state == 1) {

				//Check if table have number column
				if($objWorksheet->getCellByColumnAndRow(0, $stock_name_row + 1)->getValue()!="№") {
					throw new RuntimeException("Invalid table ".$stock_name);
				}

				//Count stocks in table 
				$last_stock_index = 0;
				for ($temprow = $stock_name_row + 2; $temprow <= $highestColumnIndex; ++$temprow) {
					$cell = $objWorksheet->getCellByColumnAndRow(0, $temprow);
					if(!ctype_digit($cell->getValue()) || $cell->getValue()==null || $cell->getValue()=="") {
						$last_stock_index = $temprow - 1;
						break;
					} 

				} 

				//Calculate number of stocks
				$stock_number = $last_stock_index - $stock_name_row - 1;

				//Initialize stock datas array
				for($i = 0; $i < $stock_number; ++$i) {
					$data[] = array();
				}


				//Get stock attribute names and define type based on its data
				for($tempcol = 1; $tempcol < $highestColumnIndex; ++$tempcol) {
					$tempcell = $objWorksheet->getCellByColumnAndRow($tempcol, $stock_name_row);
					
					//Check cell actually in stock type
					if($tempcell->isInMergeRange() && !$tempcell->isMergeRangeValueCell()) {
						$attr_cell = $objWorksheet->getCellByColumnAndRow($tempcol, $stock_name_row + 1);

						//Defining data type and save data
						for($temprow = $stock_name_row + 2; $temprow <= $last_stock_index; ++$temprow){
							
							$stock_attr_cell = $objWorksheet->getCellByColumnAndRow($tempcol, $temprow);
							$stock_attr_value = $stock_attr_cell->getValue();
							$data[$temprow - $stock_name_row - 2][] = $stock_attr_value;


							/*
							float(6,2) 		-> 1
							varchar(255) 	-> 3
							date 			-> 4
							boolean 		-> 5
							text 			-> 6
							*/

							$data_type = -1;

							if(gettype($stock_attr_value) == "double" && $data_type == -1) {
								//Its float
								$data_type = 0;
							} else if(gettype($stock_attr_value) == "string") {
								if($data_type != 1){
									//Its string
									$data_type = 3;
								} else if(in_array($stock_attr_value, array("Тийм", "Үгүй") ) {
									//Its boolean
									$data_type = 5;
								}
							}
						}

					}
				} 

			}
		}

	} catch(Exception $e) {
		die('Error load data from file: <br>'.$e->getMessage());
	}

}

function getStockTypeData() {
	$stock_name = test_input_ex_post("stock_name", 1, false);
	$data_number = test_input_ex_post("data_number", 0, false);

	$data_names = array();
	$data_types = array();
	$data_notnulls = array();
	$data_lengths = array();

	for($i = 0; $i < $data_number; $i++){
		$data_names[] = test_input_ex_post("data_name_".($i+1), 1, false);
		$data_types[] = test_input_ex_post("data_type_".($i+1), 0, false);
		$data_notnulls[] = test_input_bin("data_notnull_".($i+1));
		$data_lengths[] = test_input_ex_post("data_length_".($i+1), 0, true);
	}

	$first = array($stock_name, $data_number);
	$second = array($data_names, $data_types, $data_notnulls, $data_lengths);
	return array($first, $second);
}


/*
$data param in insertStockType method format:
[
	[stock_name, data_number],
	[[data_name_N], [data_type_N], [data_notnull_N], [data_length_N]]
]

Data type:
int 0;
float 1;
char 2;
varchar 3;
date 4;
bool 5;
text 6;
*/

function insertStockType($data) {
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
	$query = "CREATE TABLE ".$tablename." (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, model TEXT character set utf8 NOT NULL, registered_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP";

	$name = &$data[0][0];
	$data_number = &$data[0][1];

	$data_names = &$data[1][0];
	$data_types = &$data[1][1];
	$data_notnulls = &$data[1][2];
	$data_lengths = &$data[1][3];

	//Part of query that contains column names & types
	$columnstring = "";

	for($i = 0; $i < $data_number; $i++){
		$data_notnull = $data_notnulls[$i]==1 ? "NOT NULL":"NULL";

		//Formatting column names and types
		$columnstring .= ",c".$i." ";
		switch($data_types[$i]){
			case 0: $data_type = "int"; break; 
			case 1: $data_type = "float(6,2)"; break; 
			case 2: $data_type = "char(".$data_lengths[$i].") character set utf8"; break; 
			case 3: $data_type = "varchar(".$data_lengths[$i].") character set utf8"; break; 
			case 4: $data_type = "date"; break; 
			case 5: $data_type = "bool"; break; 
			case 6: $data_type = "text character set utf8"; break; 
			default: {
				//Replace
				die("Invalid data type for '".$data_names[$i]."' : ".$data_types[$i]);
			}
		}

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

	//Preparing connection to insert new stock attributes /id, Name, Columnname, Type, Length/
	$query = "INSERT INTO stock_attrs(id, _name, _columnname) VALUES (?, ?, ?)";
	if(!$stmt = $conn->prepare($query)){
		//Replace
		$conn->query("DELETE FROM stock_names WHERE id=".$stock_id."; DROP TABLE ".$tablename.";");
		die("Error on prepare query:".$query."<br>".$conn->error);
	}

	$column_name =  "";
	$data_name = "";

	if(!$stmt->bind_param("iss", $stock_id, $data_name, $column_name)){
		//Replace 
		$conn->query("DELETE FROM stock_names WHERE id=".$stock_id."; DROP TABLE ".$tablename.";");
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	}

	//Inserting stock attributes
	for($i = 0; $i < $data_number; $i++){
		$column_name = "c".$i;
		$data_name = $data_names[$i];

		if(!$stmt->execute()){
			//Replace
			$conn->query("DELETE FROM stock_attrs WHERE id=".$stock_id."; DELETE FROM stock_names WHERE id=".$stock_id."; DROP TABLE ".$tablename.";");
			die("Error on executing query:".$query."<br>".$conn->error);
		}
	}
}

function &getStockDataFromUser() {
	global $conn;

	$stock_id = test_input_ex_post("stock_id", 0, false);

	$columnnames = array();
	$datas = array();
	$datatype = "";

	//Retrieving table column info and input from user
	if($result = $conn->query("SELECT ext.COLUMN_NAME, ext.DATA_TYPE FROM stock_attrs INNER JOIN (SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'stock_".$stock_id."') as ext ON stock_attrs._columnname = ext.COLUMN_NAME WHERE stock_attrs.id =".$stock_id." OR stock_attrs.id = 0 ORDER BY stock_attrs.id ASC")) {

		while ($column = $result->fetch_array()){
			$datas[] = test_input_ex_post($column[0], 1, false);
			$columnnames[] = $column[0];

			if($column[1]=="int"){
				$datatype.="i";
			} else {
				$datatype.="s";
			}
		}
		$result = array(array($stock_id,$datatype), $columnnames, array($datas));
		return $result;
	} else {
		//Replace
		die("Failed to retrieve table column info: ".$conn->error);
	}
	
}

/*
$data param in insertStock method format:
[
	[stock_id, isss]
	[model, columnnames,...], 
	[[model_data, datas,...],[model_data2, datas, ...]]
]
*/

function insertStock($input){
	global $conn;

	$stock_id = $input[0][0];
	$datatype = $input[0][1];
	$columnnames = $input[1];
	$datas_array = &$input[2];
	$datas = &$input[2][0];

	$query1 = "INSERT INTO stock_".$stock_id."(";
	$query2 = " VALUES (?";

	$query1 .= "".$columnnames[0];

	$len = count($datas);

	for($i = 1; $i < $len; $i++) {
		$query1 .= ",".$columnnames[$i];
		$query2 .= ",?";
	}

	//Building complete query
	$query = $query1.")".$query2.")";

	if(!$stmt = $conn->prepare($query)){
		//Replace
		die("Error on preparing query:".$query."<br>".$conn->error);
	}

	//Adding parameters to bind_param method. Almost equal to bind_param("isss",[params]);
	$params = array_merge(array($datatype),$datas); 

	$refArr = array();
	foreach($params as $key => $value) {
		$refArr[$key] = &$params[$key];
	}

	if(!call_user_func_array(array($stmt, 'bind_param'), $refArr)){
		die("Error on binding parameters for query: ".$query."<br>".$conn->error);
	} 

	$len = count($datas_array);
	for ($i = 0; $i < $len; $i++) {
		$datas = &$datas_array[$i];
		$params = array_merge(array($datatype),$datas); 

		if(!$stmt->execute()){
		//Replace
			die("Error on executing query:".$query."<br>".$conn->error);
		}
	}
}

function editStockType(){
	global $conn;
}

function removeStock(){
	global $conn;
}

function getFileFromUser(){
	try {
 	   	// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (!isset($_FILES['upfile']['error']) || is_array($_FILES['upfile']['error'])) {
			throw new RuntimeException('Invalid parameters.');
		}

  		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['upfile']['error']) {
			case UPLOAD_ERR_OK:
			break;
			case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Exceeded filesize limit.');
			default:
			throw new RuntimeException('Unknown errors.');
		}

    	// You should also check filesize here. 
		if ($_FILES['upfile']['size'] > 1000000) {
			throw new RuntimeException('Exceeded filesize limit.');
		}

	   	// You should name it uniquely.
	    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
	    // On this example, obtain safe unique name from its binary data.
		if (!is_uploaded_file(($_FILES['upfile']['tmp_name']))) {
			throw new RuntimeException('File is not uploaded.');
		}

		return $_FILES['upfile']['tmp_name'];

	} catch (RuntimeException $e) {
		//Replace
		echo $e->getMessage();

	}
}
?>