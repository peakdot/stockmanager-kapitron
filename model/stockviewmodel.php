<?php

require_once("test_input.php");
require_once("conn.php");

$conn = null;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$type = test_input_ex("t",0,false);
	if($type == '0') {
		initcon();
		echo json_encode(getStockTypeNames());
		closecon();
	} else if($type == '1') {
		initcon();
		$stock_id = test_input_ex("id",0,false);
		echo json_encode(getStockTypeAttrInfos($stock_id));
		closecon();
	} else if($type == '2'){
		initcon();
		$stock_id = test_input_ex("id",0,false);
		echo json_encode(getStockData($stock_id));
		closecon();
	} else if($type == '3'){
		initcon();
		insertStock();
		closecon();
	}
} 

function getStockTypeNames(){
	global $conn;
	if($result = $conn->query("SELECT id, _name FROM stock_names")){
		$res = $result->fetch_all(MYSQLI_NUM); 
		echo json_encode($res);
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}
}

/*
SELECT stock_attrs._name, ext.COLUMN_NAME, ext.DATA_TYPE, ext.CHARACTER_MAXIMUM_LENGTH FROM stock_attrs INNER JOIN (SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'stock_1') as ext ON stock_attrs._columnname = ext.COLUMN_NAME WHERE stock_attrs.id = 1*/ 

function getStockTypeAttrInfos($stock_id){
	global $conn;

	if($result = $conn->query("SELECT stock_attrs._name, ext.COLUMN_NAME, ext.DATA_TYPE, ext.CHARACTER_MAXIMUM_LENGTH FROM stock_attrs INNER JOIN (SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'stock_".$stock_id."') as ext ON stock_attrs._columnname = ext.COLUMN_NAME WHERE stock_attrs.id =".$stock_id." OR stock_attrs.id = 0 ORDER BY stock_attrs.id ASC")) {

		$res = $result->fetch_all(MYSQLI_NUM); 
		echo json_encode($res);
		return $res;
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}	
}

function getStockData($stock_id){
	global $conn;
	$data = array();
	$columnnames = "";

	if($result = $conn->query("SELECT _name, _columnname FROM stock_attrs WHERE id=0 OR id=".$stock_id." ORDER BY id ASC")){
		$columns = array();
		while($res = $result->fetch_row()){
			if($columnnames != ""){
				$columnnames .= ",".$res[1];
			} else {
				$columnnames .= $res[1];
			}
			$columns[] = $res[0];
		} 
		$data[] = $columns;
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}	

	if($result = $conn->query("SELECT ".$columnnames." FROM stock_".$stock_id)){
		$res = $result->fetch_all(MYSQLI_NUM); 
		$data[]=$res;
	} else {
		//Replace
		die("Failed to retrieve data: ".$conn->error);
	}	

	return $data;
}

?>