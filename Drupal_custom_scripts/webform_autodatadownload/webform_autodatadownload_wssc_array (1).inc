<?php
function get_wssc_data_array($file_name,$file_type,$timestamp){
  //print_r($timestamp); print_r($file_name);die();
  if ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' or
  $file_type == 'application/vnd.ms-excel') {

    require_once 'sites/all/classes/PHPExcel/IOFactory.php';
    if ($file_type == 'application/vnd.ms-excel'){
      $objReader = PHPExcel_IOFactory::createReader('Excel5');
    } elseif ($file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    }
    //echo $file_name;
    global $base_path;
    $path_part1 = file_create_url($_SERVER['DOCUMENT_ROOT'].$base_path);
    $link = file_create_url($base_path.'sites/default/files/webform/submitted/');
    //echo '<a href="'.$link.str_replace('public://webform/submitted/','',$file_name).'" target="_blank">'.$file_name.'</a>';
    $objPHPExcel = $objReader->load($file_name);
      //Iterating through all the sheets in the excel workbook and storing the array data
       foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
           $arrayData[$worksheet->getTitle()] = $worksheet->toArray();
       }

       //Get sheet names
       $sheet_names = array_keys($arrayData);
    //   $count_sheets = count($sheet_names);
    //   $count_points = $count_sheets*31; //31 because that is the maximum possible number of days in a month
       //Single out each day's data
       $date = date('Y-m-d',$timestamp);
       $month=date("F",$timestamp);
       $year=date("Y",$timestamp);
       $legend= array('DateTime');
       $daily = array();
//       $points = array();
       $yesterday = array();
    //   echo $date;
//       echo $year;
       //$month =
       //$year =
       foreach( $sheet_names as $sheet_name){
         if ($sheet_name=='PAX 16'){
           $sheet_data = $arrayData[$sheet_name];
           //Each sheet always has 0-33 rows of data
           // row 0 blank
           // row 1 source labels
           // row 2 date labels
           // row 2 begins daily average on day 1
           // row 33 ends daily average on day 31


           for ($i = 0; $i<=64; $i++){
               while (list($key, $value) = each($sheet_data[$i])) {
                   // get legend
              //     echo "i:"; print_r($i); echo " key:"; print_r($key); echo " value:"; print_r($value); echo "<br><br>";
                   if ($i==1 AND $key==2){
                     $legend_val1 = str_replace(' Group - Pax - Raw-Reclaim - Flow - MGD', '', $value);
                    # print_r($value);
                   }
                   if ($i==1 AND $key==3){
                     $legend_val2 = str_replace(' Group - Pot - Net Withdrawal - Flow - MGD', '', $value);
                   }


  //                 // get dates
                    // $i==3 AND $key==1 is the first day of every month
                    // to get the row $i for yesterday get today's day + 1
                    // so if today is the 7th, the row $i would by 8
                //   if ($i==1 AND $key==0){$datestring = $value; }
                   // get all monthly values
                   if ($i>=3 AND $i<=64){
                       if ($key==1){
                         $datetime_value = date('Y-m-d',strtotime($value));//date('Y-m-d H:i:s',strtotime("$datestring $value"));
                       }
                       if ($key==2){
                         $patuxent = $value;
                       }
                       if ($key==3){
                         $potomac = $value;
                       }
                   }
                  }
              if ($i>=3 AND $i<=64){
                   $legend[1] = $legend_val1;
                   $legend[2] = $legend_val2;
                   $point[0] = $datetime_value;
                   $point[1] = $patuxent;
                   $point[2] = $potomac;
                   $daily[] = $point;
                   if ($i==($today+1)){
                     $yesterday[] = $point;
              //       print_r($i);
                   }
              }

              }
            } //end PAX 16 check
      }
    }
    $mydata['daily'] = $daily;
    $mydata['legend'] = $legend;
    $mydata['yesterday'] = $yesterday;
  return($mydata);
}
