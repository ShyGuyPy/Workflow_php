<?php
//link to include phpexcel class
require_once 'sites/all/classes/PHPExcel/IOFactory.php';

//create object to read the file
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$file_name ='public://webform/submitted/FW_Auto_Daily Withdrawal Report_2017_9_15.xls';

$objPHPExcel = $objReader->load($file_name);

foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
     $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
       }

$sheet_names = array_keys($arrayData);

echo $sheet_names;

$sheet_data = $arrayData[$sheet_name];

echo $sheet_data;


?>
