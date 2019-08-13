<?php
if($download_inc['fw']){
  echo "<script>console.log( 'FW is included' );</script>";
  echo "FW is included";

  $data_for_database=$all_data_for_database['fw'];
  if ($data_for_database){
    echo "<script>console.log( 'If emails were found then run' );</script>";
  }

  //print_r($data_for_database);die();
  foreach ($data_for_database as $file_attributes){
    $mime = $file_attributes[1][filemime];
    $uri = $file_attributes[1][uri];
    $fid = $file_attributes[1][fid];

    //define webform node id
    $nid=4; //fw water AM data submission page

    // get the data from the excel files
    $myarray=get_fw_data_array($uri,$mime);
    $mydata = $myarray['points'];
    $daily = $myarray['average'];

    // store hourly data - REVISIT - not wokring
    //cycle through data and upload to webform for hourly values
    /*    foreach ( $mydata as $row ) {
            $row[0]= date( 'Y-m-d', strtotime(substr($row[0],0,10)));
            $data = array(
              'date' => array($row[0]),
              'val1' => array($row[1]),
              'val2' => array($row[2]),
              'comment' => array('fw automated email'),
              'file' => array($fid),
            );
    */

    $daily[0][0]= date( 'Y-m-d', strtotime("+1 day",strtotime(substr($daily[0][0],0,10))));
    $data = array(
      'today' => array($daily[0][0]),
      'yesterdays_daily_average_withdrawals_potomac_river' => array($daily[0][1]),
      'yesterdays_daily_average_withdrawals_occoquan_reservoir' => array($daily[0][2]),
      'comments' => array('fw automated email'),
      'hourlyfile_id' => array($fid),
    );

    webform_autodatadownload_submit($nid,$data);
  }
}
