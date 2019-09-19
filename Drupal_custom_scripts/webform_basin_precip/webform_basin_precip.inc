<?php
  //Basin Precipitation page
  $nid=74;

  //get previous submission dates
  module_load_include('inc', 'webform', 'includes/webform.submissions');
  $submissions = webform_get_submissions(array('nid'=>$nid));
  foreach ($submissions as $submission){
      $dates[$submission->sid] = $submission->data[1][0];
  }

  //shared the google spreadsheet as a public link to a csv file
  $link = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSROnf12LhQdtrN4WGiIolxmDlL0Cz-pUjgpTj7ukNgTg9pcx3fNAg4MXJrvcBq5AjGI3AV_CqLor8_/pub?gid=0&single=true&output=csv";

  //open and process csv
  if ( ($handle = fopen($link, 'r')) !== FALSE) {
        //these two lines remove the header from the submission
        $headers = fgetcsv($handle, 0, ",");
        $legend = array_filter($headers);
        while ($r = fgetcsv($handle, 0, ",")) {

              //removes rows with no data so they do not get submitted
              $values = array_filter($r);

              //decide to do nothing, update, or create new submittion
              if(count($values)==1){
                 // don't submit if row is empty
                 $create_data_submission = FALSE;
                 $update_data_submission = FALSE;
              } else {
                  $searchValue= date('Y-m-d H:i:s', strtotime($r[0]));
                  $sid = max(array_keys($dates, $searchValue));
                  if ($sid){
                      // update old submission
                      $create_data_submission = FALSE;
                      $update_data_submission = TRUE;
                  } else {
                      // submit new submission
                      $update_data_submission = FALSE;
                      $create_data_submission = TRUE;
                  }
              }

              // create submission array for new or updated submissions
              if ($create_data_submission OR $update_data_submission){
                          $data = array(
                          'data_date' => $r[0],
                          'huc_2060006b' => $r[1],
                          'huc_2070001' => $r[2],
                          'huc_2070002' => $r[3],
                          'huc_2070002b' => $r[4],
                          'huc_2070002c' => $r[5],
                          'huc_2070003' => $r[6],
                          'huc_2070004' => $r[7],
                          'huc_2070005' => $r[8],
                          'huc_2070006' => $r[9],
                          'huc_2070007' => $r[10],
                          'huc_2070008' => $r[11],
                          'huc_2070008b' => $r[12],
                          'huc_2070009' => $r[13],
                          'huc_2070010' => $r[14],
                          'huc_2070010b' => $r[15],
                          'huc_2070011' => $r[16],
                          'weight_avg' => $r[17],
                          'avg' => $r[18],
                        );
                        if ($create_data_submission){
                          submit_data_array($nid,$data);
                        }
                        if ($update_data_submission){
//                          module_load_include('inc', 'webform', 'includes/webform.submissions');
                          $node = node_load($nid);
                          $existing_submission = webform_get_submission($nid, $sid);
                        //  print_r($existing_submission);
                          $existing_submission->completed = strtotime('NOW');//'today'
                          $existing_submission->modified = strtotime('NOW');//'today'
                          $existing_submission->data[1][0]=$r[0];
                          $existing_submission->data[2][0]=$r[1];
                          $existing_submission->data[3][0]=$r[2];
                          $existing_submission->data[4][0]=$r[3];
                          $existing_submission->data[5][0]=$r[4];
                          $existing_submission->data[6][0]=$r[5];
                          $existing_submission->data[7][0]=$r[6];
                          $existing_submission->data[8][0]=$r[7];
                          $existing_submission->data[9][0]=$r[8];
                          $existing_submission->data[10][0]=$r[9];
                          $existing_submission->data[11][0]=$r[10];
                          $existing_submission->data[12][0]=$r[11];
                          $existing_submission->data[13][0]=$r[12];
                          $existing_submission->data[14][0]=$r[13];
                          $existing_submission->data[15][0]=$r[14];
                          $existing_submission->data[16][0]=$r[15];
                          $existing_submission->data[17][0]=$r[16];
                          $existing_submission->data[18][0]=$r[17];
                          $existing_submission->data[19][0]=$r[18];
                    //      print_r($existing_submission); //die();
                          webform_submission_update($node,$existing_submission);
                        }
              } // end creating submission array
        } // end while
  } //if handle
  fclose($handle);


  //precipitation totals are area weighted
  //HUC8_plus	mi2	fraction of area above L Falls	HUC8_plus description
  //not required because the weighted average is already calculated
  // $area_weights_by_huc = array(
  //   "2060006b"	=> 0.000000,  //Patuxent reservoir watershed
  //   "2070001"	=> 0.128469,  	//S Branch Potomac HUC8
  //   "2070002a" =>	0.082120,	  //N Branch Potomac HUC8
  //   "2070002b" => 0.024918,	  //CBP WM river segment containing Jennings Randolph Reservoir watershed
  //   "2070002c" => 0.009045, 	//Savage Reservoir watershed
  //   "2070003" => 0.103818,	  //2070003 includes Potomac mainstem and Cacapon and Town Creek watersheds
  //   "2070004" => 0.196698,	  //2070004 includes Potomac mainstem and Licking
  //   "2070005" => 0.144593,  	//S Fork Shenandoah River HUC8
  //   "2070006" => 0.089376,  	//N Fork Shenandoah River HUC8
  //   "2070007" => 0.030397,	  //Shenandoah River below N Fork & S Fork confluence - a HUC8
  //   "2070008a" => 0.098089, 	//includes Potomac mainstem and Goose and the two Catoctin creeks
  //   "2070008b" => 0.008789, 	//CBP WM river segment containing Little Seneca Reservoir watershed
  //   "2070009" => 0.083688,	  //Monocacy basin HUC8
  //   "2070010a" => 0.000000,	    //includes Potomac estuary below DC and Anacostia River and Accotink and Piscataway creek watersheds
  //   "2070010b" => 0.000000,    //Occoquon Reservoir watershed
  //   "2070011" => 0.000000,
  // );
