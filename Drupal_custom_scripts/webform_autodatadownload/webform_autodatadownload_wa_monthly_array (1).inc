<?php
function get_wa_monthly_data_array($file_name,$file_type,$timestamp){
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
      $data_date = date('Y-m-d',strtotime($sheet_data[2][0]));
      $data_year = date('Y',strtotime($sheet_data[2][0]));
      $data_month = date('m',strtotime($sheet_data[2][0]));
      $number = cal_days_in_month(CAL_GREGORIAN, $data_month, $data_year); // 31

      for ($i = 4; $i<=($number+3); $i++){
        // $point[0] = date( 'Y-m-d', strtotime("+1 day",strtotime($data_year.'-'.$data_month.'-'.$sheet_data[$i][1])));
        $point[0] = date( 'Y-m-d', strtotime($data_year.'-'.$data_month.'-'.$sheet_data[$i][1]));
        $point[1] = $sheet_data[$i][2]; //great falls
        $point[2] = $sheet_data[$i][3]; //little falls
        $point[3] = 'monthly '.$file_name;
        $daily[] = $point;
      }
    }
  }
  $mydata['daily'] = $daily;
  return($mydata);
}
