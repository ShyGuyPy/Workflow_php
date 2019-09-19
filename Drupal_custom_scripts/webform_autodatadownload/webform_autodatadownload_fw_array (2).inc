<?php

function get_fw_data_array($file_name,$file_type){
  if ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' or
  $file_type == 'application/vnd.ms-excel') {

      // right now only working for FW;
    require_once 'sites/all/classes/PHPExcel/IOFactory.php';
    if ($file_type == 'application/vnd.ms-excel'){
      $objReader = PHPExcel_IOFactory::createReader('Excel5');
    } elseif ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    }
    //echo $file_name;
    global $base_path;
    $path_part1 = file_create_url($_SERVER['DOCUMENT_ROOT'].$base_path);
    //$link = $path_part1.'sites/default/files/webform//';
    //$link = file_create_url($base_path.'sites/default/files/webform/submitted/');
    //$link= $base_path.'sites/default/files/webform/submitted/';
    echo '<a href="'.$link.str_replace('public://webform/submitted/','',$file_name).'" target="_blank">'.$file_name.'</a>';

    $objPHPExcel = $objReader->load($file_name);
     //Iterating through all the sheets in the excel workbook and storing the array data
      foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
          $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
      }

      //Get sheet names
      $sheet_names = array_keys($arrayData);
      $count_sheets = count($sheet_names);
      $count_points = $count_sheets*24;
      print_r($sheet_names);
        //Single out each day's data
      $legend= array('DateTime');
      $mydata = array();
      $points = array();
      $average = array();
      foreach( $sheet_names as $sheet_name){
          $sheet_data = $arrayData[$sheet_name];
          //Each sheet always has 0-29 rows of data
          // row 1 has dates
          // row 2 labels and units
          // row 3-26 daily data that we want
          // row 27 Average
          // row 28 Max
          // row 29 Min
        //  print_r($sheet_data);
          for ($i = 0; $i<=27; $i++){
              while (list($key, $value) = each($sheet_data[$i])) {
                  // get legend
                  if ($i==0 AND $key==0){
                    $legend_val1 = str_replace(' Report for:', '', $value);
                  }
                  if ($i==0 AND $key==2){
                    $legend_val2 = str_replace(' Report for:', '', $value);
                  }
                  // get dates
                  if ($i==1 AND $key==0){$datestring = $value; }
                  // get hourly values
                  if ($i>=3 AND $i<=26){
                      if ($key==0){
                        $datetime_value = date('Y-m-d H:i:s',strtotime("$datestring $value"));
                      }
                      if ($key==1){
                        $potomac = $value;
                      }
                      if ($key==2){
                        $occoquan = $value;
                      }
                  }
                  // get daily values
                  if ($i==27){
                    if ($key==0){
                      $date_value = date('Y-m-d',strtotime("$datestring"));
                    }
                    if ($key==1){
                      $potomac_daily = $value;
                    }
                    if ($key==2){
                      $occoquan_daily = $value;
                    }
                  }
              }
              if ($i>=3 AND $i<=26){
                  $legend[1] = $legend_val1;
                  $legend[2] = $legend_val2;
                  $point[0] = $datetime_value;
                  $point[1] = $potomac;
                  $point[2] = $occoquan;
                  $points[] = $point;
              }
              if ($i==27){
                $daily[0] = $date_value;
                $daily[1] = $potomac_daily;
                $daily[2] = $occoquan_daily;
                $average[] = $daily;
              }
            }
    }
    $mydata['points'] = $points;
    $mydata['legend'] = $legend;
    $mydata['average'] = $average;
  }
  return($mydata);
}
