<?php
  if($download_inc['wa']){

   $data_for_database=$all_data_for_database['wa'];

   foreach ($data_for_database as $file_attributes){
      $mime = $file_attributes[1][filemime];
      $uri = $file_attributes[1][uri];
      $fid = $file_attributes[1][fid];
      $timestamp = $file_attributes[1][timestamp]; //in Y-m-d format

      //define webform node id
      $nid=5; //wa water AM data submission page

      // get the data from the excel files
      $myarray=get_wa_data_array($uri,$mime,$timestamp);
      $mydata = $myarray['daily'];

      webform_autodatadownload_submission_wa($nid,$mydata,$fid,'wa',$today_date);
    } //for each $data_for_database end
  }

  if($download_inc['wa_monthly']){

   $data_for_database=$all_data_for_database['wa_monthly'];

   foreach ($data_for_database as $file_attributes){
      $mime = $file_attributes[1][filemime];
      $uri = $file_attributes[1][uri];
      $fid = $file_attributes[1][fid];
      $timestamp = $file_attributes[1][timestamp]; //in Y-m-d format

      //define webform node id
      $nid=5; //wa water AM data submission page

      // get the data from the excel files
      $myarray=get_wa_monthly_data_array($uri,$mime,$timestamp);
      $mydata = $myarray['daily']; //daily values for the current month

      webform_autodatadownload_submission_wa($nid,$mydata,$fid,'wa_monthly',$today_date);
    } //for each $data_for_database end
  }
