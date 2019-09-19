<?php

  //code to generate the json file for the precipitation map
  // could put code here, but not currently using this.
  // seems to run fast enough on map.

  $nid = 74;

  $lookup_date1 = date('Y-m-d', strtotime('now'));
  $lookup_date1= date('Y-m-d', strtotime($lookup_date1. '-0 day'));
  $lookup_date2= date('Y-m-d', strtotime($lookup_date1. '-7 day'));

  // gets cid codes for array keys in database
  $my_compMap = array();
  $node= node_load($nid);
  foreach ($node->webform['components'] as $c) {
      $my_compMap[$c['form_key']] = $c['cid'];
  }
  array_shift($my_compMap);

  function createDateRange($startDate, $endDate, $format = "Y-m-d")
  {
      $begin = new DateTime($startDate);
      $end = new DateTime($endDate);
      $interval = new DateInterval('P1D'); // 1 Day
      $dateRange = new DatePeriod($begin, $interval, $end);
      $range = [];
      foreach ($dateRange as $date) {
          $range[] = $date->format($format);
      }
      return $range;
  }

  $daterange = createDateRange($lookup_date2,$lookup_date1);
  $view = 'webform_views_manage_precipitation_data';
  $sql = "SELECT * FROM `".$view."` WHERE `data_date` BETWEEN '".$lookup_date2."' AND '".$lookup_date1."'";
  $query = db_query($sql);
  $results = $query->fetchAll();

  foreach ($my_compMap as $compkey=>$compkeyval){
        if ($compkey != 'data_date'){
        foreach ($results as $result){
          $temp_date = date('Y-m-d', strtotime($result->data_date));
          $key = max(array_keys($daterange, $temp_date));
          if(is_numeric($key)){
            $my_data[$key] = $result->$compkey;
          }
        }

      // One Day Weighted Average
      $weighted_precip_1 = $my_data[6];
      if(empty($weighted_precip_1)==1){
        $precip_1day[$compkey] = -9999;
      } else {
        $precip_1day[$compkey] = round($weighted_precip_1,6);
      }

      //Three Day Weighted Average
      $weighted_precip_3 = array($my_data[6],$my_data[5],$my_data[4]);
      if(empty($weighted_precip_3[0])==1 OR empty($weighted_precip_3[1])==1 OR empty($weighted_precip_3[2])==1 ){
        $precip_3day[$compkey] = -9999;
      } else {
        $precip_3day[$compkey] =round(array_sum($weighted_precip_3),6);
      }
      // Seven Day Weighted Average
      if(count($my_data)==7){
        $precip_7day[$compkey] =round(array_sum($my_data),6);
      } else {
        $precip_7day[$compkey] = -9999;
      }
      }
  }

  $json_string_to_write = json_encode(array(
    'date' => $lookup_date1,
    'one_day' => $precip_1day,
    'three_day' => $precip_3day,
    'seven_day' => $precip_7day,
  ));
  $file_path_to_write_to = getcwd().'/sites/all/custom/leaflet/leaflet_precip_data.json';
  $json_file = fopen($file_path_to_write_to, "w") or die('Unable to open file to :'.$file_path_to_write_to);
  fwrite($json_file, $json_string_to_write);
  fclose($myfile);
