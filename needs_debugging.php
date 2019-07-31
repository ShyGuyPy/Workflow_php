//This is the script in icprbcoop.org drupal site that need debugging
//location from cpanel is: public_html/drupal4/sites/all/modules/custom/webform_autodatadownload
// this is a module file(.module)
<?php
// load functions
require_once 'webform_autodatadownload_configure.inc';
require_once 'webform_autodatadownload_fw_array.inc';
require_once 'webform_autodatadownload_wssc_array.inc';
require_once 'webform_autodatadownload_wa_array.inc';
require_once 'webform_autodatadownload_wa_monthly_array.inc';
require_once 'webform_autodatadownload_submission.inc';

function webform_autodatadownload_cron() {
  // Run only once a day so find out if we need to run it yet today.
  // haven't got this check working yet...
 //  $last_dowload = variable_get('webform_autodatadownload_last_download');
 //  $last_dowload_date = strtotime($last_dowload[year].'-'.$last_dowload[month].'-'.$last_dowload[day]);
 //  $days_since = round((strtotime('now')-$last_run_date)/60/60/24);
 //
 //  if ($days_since > 0) {
 //    $run = 1;
 //  } else {
 //    $run = 0;
 // }
$run=1;
  if($run){

      $today_date = date("Y-m-d");
      $today_date = date('Y-m-d',strtotime( 'now' ));
      //get settings from configure link on modules page
      $download_inc['fw'] = variable_get('webform_autodatadownload_fwinc');
      $download_inc['wssc'] = variable_get('webform_autodatadownload_wsscinc');
      $download_inc['wa'] = variable_get('webform_autodatadownload_wainc');
      $download_inc['wa_monthly'] = variable_get('webform_autodatadownload_wamonthlyinc');

      //get excel files from emails and save in variable $all_data_for_database
      require_once 'webform_autodatadownload_imap.inc';
      //process $all_data_for_database['fw'] for database and submit
      require_once 'webform_autodatadownload_fw.inc';
      //process $all_data_for_database['wssc'] data for database and submit
      require_once 'webform_autodatadownload_wssc.inc';
      //process $all_data_for_database['wa'] data for database and submit
      require_once 'webform_autodatadownload_wa.inc';

    }
  }
