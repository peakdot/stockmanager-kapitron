<?php

//Get table names starts with 'stocki' from stockmanager database 
if($result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES	WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='stockmanager' AND TABLE_NAME LIKE 'stocki%'")){
	while($row = $result->fetch_row()){
		if(substr($row[0],0,6)==""){

		}
	}
}
?>