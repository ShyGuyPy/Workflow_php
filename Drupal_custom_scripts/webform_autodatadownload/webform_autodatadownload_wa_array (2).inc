<?php
function get_wa_data_array($file_name,$file_type,$timestamp){
  if ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' or
  $file_type == 'application/vnd.ms-excel') {

    require_once 'sites/all/classes/PHPExcel/IOFactory.php';
    if ($file_type == 'application/vnd.ms-excel'){
      $objReader = PHPExcel_IOFactory::createReader('Excel5');
    } elseif ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    }

    global $base_path;
    $path_part1 = file_create_url($_SERVER['DOCUMENT_ROOT'].$base_path);
    $link = file_create_url($base_path.'sites/default/files/webform/submitted/');
    $objPHPExcel = $objReader->load($file_name);
    //Iterating through all the sheets in the excel workbook and storing the array data
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
      $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
    }

    //Get sheet names
    $sheet_names = array_keys($arrayData);
    foreach( $sheet_names as $sheet_name){
      $sheet_data = $arrayData[$sheet_name];
      $sheet_title = array_values(array_filter($sheet_data[4]));
      $sheet_settings = explode(' hours ending Midnight, ',$sheet_title[0]);
      $num_days = floatval($sheet_settings[0])/24;
      $last_date = date('Y-m-d',strtotime($sheet_settings[1]));

    //  echo $num_days; echo "<br><br>";
      for( $i=0 ; $i < $num_days; $i++ ){
        $temp= array_values(array_filter($sheet_data[17+$i]));
        $point[0] = date('Y-m-d',strtotime($temp[0]));
        $point[1] = strval(floatval($temp[4])); //total assume all great falls
        $point[2] = '0.00'; // assume zero little falls
        $point[3] = 'daily '.$file_name;
        $point[4] = strval(floatval($temp[1])); //dc
        $point[5] = strval(floatval($temp[2])); //arlington
        $point[6] = strval(floatval($temp[3])); //fairfax water
        $daily[] = $point;
      }
    }
  }
  $mydata['daily'] = $daily;
  return($mydata);
}
