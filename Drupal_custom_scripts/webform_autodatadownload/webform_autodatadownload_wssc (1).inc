<?php
  if($download_inc['wssc']){

    $data_for_database=$all_data_for_database['wssc'];

    foreach ($data_for_database as $file_attributes){
      $mime = $file_attributes[1][filemime];
      $uri = $file_attributes[1][uri];
      $fid = $file_attributes[1][fid];
      $timestamp = $file_attributes[1][timestamp]; //in Y-m-d format

      //define webform node id
      $nid=3; //wssc water AM data submission page

      // get the data from the excel files
      $myarray=get_wssc_data_array($uri,$mime,$timestamp);
      $mydata = $myarray['daily']; //daily values for the current month

      // get all values of today for node 3
      // purpose is to check for duplicates
      webform_autodatadownload_submission($nid,$mydata,$fid);

    } //for each $data_for_database end
  }
