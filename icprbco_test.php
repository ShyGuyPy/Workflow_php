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

//echo $sheet_names;

$sheet_data = $arrayData[$sheet_name];

//echo $sheet_data;

require_once 'public_html/drupal4/sites/all/modules/custom/webform_autodatadownload.module';

?>

<?php
//9/18/19
require_once 'sites/all/modules/custom/webform_autodatadownload/webform_autodatadownload.module';
require_once 'sites/all/modules/custom/webform_autodatadownload/webform_autodatadownload_fw.inc';
require_once 'sites/all/modules/custom/webform_autodatadownload/webform_autodatadownload_fw_array.inc';
require_once 'sites/all/modules/custom/webform_autodatadownload/webform_autodatadownload_wa_array.inc';

require_once 'sites/all/modules/custom/webform_autodatadownload/webform_autodatadownload_configure.inc';

webform_autodatadownload_cron();

$download_inc['fw'] = variable_get('webform_autodatadownload_fwinc');
$download_inc['wa'] = variable_get('webform_autodatadownload_wainc');

echo $download_inc['fw'];
echo "<br>";
echo $download_inc['wa'];
echo "<br>";
echo $download_inc;
echo "<br>";
echo $data;
?>
