<?php
  // currently unused code...could be used to create a viewable comma seperated view of data from browser
  //shared the google spreadsheet as a public link to a csv file
  $link = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSROnf12LhQdtrN4WGiIolxmDlL0Cz-pUjgpTj7ukNgTg9pcx3fNAg4MXJrvcBq5AjGI3AV_CqLor8_/pub?gid=0&single=true&output=csv";

  //open and process csv
  if ( ($handle = fopen($link, 'r')) !== FALSE) {
    $headers = fgetcsv($handle, 0, ",");
    $legend = array_filter($headers);
    header("Content-Type: text/plain");
    echo implode($legend,', ');
    while ($r = fgetcsv($handle, 0, ",")) {
      echo "\n".implode($r, ', ');
    }
    fclose($handle);
  } else {
    echo "The Google spreadsheet doesn't exist."); ///this error message is not printing early enough in the code
  }
  die();
