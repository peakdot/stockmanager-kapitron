<?php

//Get table names starts with 'stocki' from stockmanager database 
if($result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES	WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='stockmanager' AND TABLE_NAME LIKE 'stocki%'")){
	while($row = $result->fetch_row()){
		if(substr($row[0],0,6)==""){

		}
	}
}
/*
SELECT stock_attrs._name, ext.COLUMN_NAME, ext.DATA_TYPE, ext.CHARACTER_MAXIMUM_LENGTH FROM stock_attrs INNER JOIN (SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE 
     TABLE_NAME = 'stock_1') as ext ON stock_attrs._columnname = ext.COLUMN_NAME WHERE stock_attrs.id=1
     */
?>