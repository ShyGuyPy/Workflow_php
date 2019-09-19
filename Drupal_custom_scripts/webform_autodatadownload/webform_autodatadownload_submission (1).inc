<?php

//submissions
function webform_autodatadownload_submit($nid,$data){
  $node = node_load($nid);
  module_load_include('inc', 'webform', 'webform.module');
  module_load_include('inc', 'webform', 'includes/webform.submissions');

  // This methods will arrange $data in the right way
  $data = _webform_client_form_submit_flatten($node, $data);
  $data = webform_submission_data($node, $data);

  $submission = (object) array(
    'nid' => $nid,
    'uid' => 1,
    'submitted' => REQUEST_TIME,
    'remote_addr' => ip_address(),
    'is_draft' => FALSE,
    'data' => $data,
  );

  module_load_include('inc', 'webform', 'includes/webform.submissions');
  webform_submission_insert($node, $submission);
}

//data for submissions
function webform_autodatadownload_submission($nid,$data,$fid){
//get previous submission dates
module_load_include('inc', 'webform', 'includes/webform.submissions');
$submissions = webform_get_submissions(array('nid'=>$nid));
foreach ($submissions as $submission){
    $datetimes[$submission->sid] = $submission->data[22][0];
}
//    print_r($data); echo "<br><br>";
foreach ($data as $r) {
      // only process dates for today or before. ignore anything after today.
      if ($r[0]>=$today_date){
          $values = array_filter($r); //removes rows with no data so they do not get submitted
          if(count($values)<=1){
              // don't submit if row is empty
              $create_data_submission = FALSE;
              $update_data_submission = FALSE;

          } else {
              $searchValue= date('Y-m-d', strtotime('+1 day', strtotime($r[0])));
              $sid = max(array_keys($datetimes, $searchValue));
              if ($sid){
                // update old submission
                $create_data_submission = FALSE;
                $update_data_submission = TRUE;
              } else {
                // submit new submission
                $create_data_submission = TRUE;
                $update_data_submission = FALSE;
              }
          }

          if ($create_data_submission){
            $data_array = array(
              'today' => array($searchValue),
              'yesterdays_daily_average_withdrawal_potomac_river' => array($r[2]),
              'yesterdays_daily_average_withdrawal_patuxent_reservoirs' => array($r[1]),
              'comments' => array('wssc automated email'),
          //    'automatedfile_id' => array($fid),
            );
            webform_autodatadownload_submit($nid,$data_array);
          }
          if ($update_data_submission){
           module_load_include('inc', 'webform', 'includes/webform.submissions');
           $node = node_load($nid);
           $existing_submission = webform_get_submission($nid, $sid);
           $existing_submission->completed = strtotime('NOW');//'today'
           $existing_submission->modified = strtotime('NOW');//'today'
           $existing_submission->data[22][0] = $searchValue;//'today'
           $existing_submission->data[5][0] = $r[2]; //'yesterdays_daily_average_withdrawal_potomac_river'
           $existing_submission->data[4][0] = $r[1]; //'yesterdays_daily_average_withdrawal_patuxent_reservoirs'
           $existing_submission->data[31][0] = 'wssc automated email corrected'; //'comment'
    //       $existing_submission->data[32][0] = $fid; //'automatedfile_id'

           webform_submission_update($node,$existing_submission);
          }
    }
  }
}

//data for submissions
function webform_autodatadownload_submission_wa($nid,$data,$fid,$type,$today_date){

  $my_compMap = array();
  $node= node_load($nid);
  foreach ($node->webform['components'] as $c) {
  $my_compMap[$c['form_key']] = $c['cid'];
  }
  // print_r($my_compMap['today']);

  //get previous submission dates
  module_load_include('inc', 'webform', 'includes/webform.submissions');
  $submissions = webform_get_submissions(array('nid'=>$nid));
  foreach ($submissions as $submission){
    $datetimes[$submission->sid] = $submission->data[$my_compMap['today']][0];
  }

  foreach ($data as $r) {
    if ($r[0]<=$today_date){ //ignores dates from the future
      $values = array_filter($r); //removes rows with no data so they do not get submitted
      if(count($values)<=1){
          $create_data_submission = FALSE;
          $update_data_submission = FALSE;

      } else {
          $searchValue= date('Y-m-d', strtotime('+1 day', strtotime($r[0])));
          $sid = max(array_keys($datetimes, $searchValue));
          if ($sid){
              $create_data_submission = FALSE;
              $update_data_submission = TRUE;
          } else {
              $create_data_submission = TRUE;
              $update_data_submission = FALSE;
          }
      }
      // echo $r[0].": create: ".$create_data_submission."; update: ".$update_data_submission."<br>";
      if ($create_data_submission){
          $data_array = array(
              'today' => array($searchValue),
              'yesterdays_daily_average_withdrawals_potomac_river_at_great_falls' => array($r[1]), //great falls
              'yesterdays_daily_average_withdrawals_potomac_river_at_little_falls' => array($r[2]), //little falls
              'comments' => array($r[3]),
          );
          if ($type == 'wa'){
            echo "got here";
            $data_array = array(
                'today' => array($searchValue),
                'yesterdays_daily_average_withdrawals_potomac_river_at_great_falls' => array($r[1]), //great falls
                'yesterdays_daily_average_withdrawals_potomac_river_at_little_falls' => array($r[2]), //little falls
                'comments' => array($r[3]),
                'dc_demand_mgd' => array($r[4]),//dc
                'arlington_demand_mgd' => array($r[5]), //arlington
                'fw_demand_mgd' => array($r[6])//fw
            );
          }
          // print_r($data_array); echo "<br><br>";
          webform_autodatadownload_submit($nid,$data_array);
      }
      if ($update_data_submission){
    //      module_load_include('inc', 'webform', 'includes/webform.submissions');
          $node = node_load($nid);
          $existing_submission = webform_get_submission($nid, $sid);
          $existing_submission->completed = strtotime('NOW');//'today'
          $existing_submission->modified = strtotime('NOW');//'today'
          $existing_submission->data[$my_compMap['today']][0] = $searchValue;//'today'
          $existing_submission->data[12][0] = $r[1]; //'great falls'
          $existing_submission->data[13][0] = $r[2]; //'little falls'
          $existing_submission->data[$my_compMap['comments']][0] = $r[3]; //'comment'
          if ($type == 'wa'){
            $existing_submission->data[27][0] = $r[4]; //'dc'
            $existing_submission->data[28][0] = $r[5]; //'arlington'
            $existing_submission->data[29][0] = $r[6]; //'fw'
          }
          webform_submission_update($node,$existing_submission);
          // print_r($existing_submission); echo "<br><br>";
      }
    }
  }
}
