
<?php
$before = microtime(true);
$before_m = memory_get_usage();

require("stockmodel.php");

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objReader->setReadDataOnly(true);

$objPHPExcel = $objReader->load(getFileFromUser());
$objWorksheet = $objPHPExcel->getActiveSheet();

rowCol($objWorksheet);

$after = microtime(true);
$after_m = memory_get_usage();
echo 'Time:'.($after-$before) . " sec\n";
echo 'Memory:'.($after_m-$before_m) . " byte\n";

function toArray($objWorksheet) {
	$array = $objWorksheet->toArray();
	$highestColumnIndex = count($array[0]);
	$highestRow = count($array);

	echo '<table>' . "\n";
	for ($row = 1; $row < $highestRow; ++$row) {
		echo '<tr>' . "\n";

		for ($col = 0; $col < $highestColumnIndex; ++$col) {
			echo '<td>' . $array[$row][$col] . '</td>' . "\n";
		}

		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
}

function iterator($objWorksheet){
	echo '<table>' . "\n";
	foreach ($objWorksheet->getRowIterator() as $row) {
		echo '<tr>' . "\n";

		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell) {
			echo '<td>' . $cell->getValue().'</td>' . "\n";
		}

		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
}

function rowCol($objWorksheet){
	$highestRow = $objWorksheet->getHighestRow(); 
	$highestColumn = $objWorksheet->getHighestColumn(); 

	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 

	echo '<table>' . "\n";
	for ($row = 1; $row <= $highestRow; ++$row) {
		echo '<tr>' . "\n";

		for ($col = 0; $col <= $highestColumnIndex; ++$col) {
			echo '<td>' . $objWorksheet->getCellByColumnAndRow($col, $row)->getValue() . '</td>' . "\n";
		}

		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";

}
?>
