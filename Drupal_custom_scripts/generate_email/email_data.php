<?php
if(isset($_GET['email_summary'])){
      if($format == "daily" or $format=="enhanced_am"){
        $view = 'webform_views_data_submission_icprb_am';
        $field = 'intro_text_am';
      }
      if($format == "enhanced_pm"){
        $view = 'webform_views_data_submission_icprb_pm';
        $field = 'intro_text_pm';
      }
        $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
        $query = db_query($sql);
        $results = $query->fetchAll();
        foreach ($results as $result){
          $email_summary = $result->$field;
        }
        //$email_summary = $mydata['summary'];
}
if(isset($_GET['email_precipitation'])){
        module_load_include('inc', 'webform', 'includes/webform.submissions');
        $submissions = webform_get_submissions(array('nid'=> 74));

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

        $lookup_date1 = date('Y-m-d',$today); // this should be yesterday but the daterange function skips today so it works out, could improve
        $lookup_date2 = date('Y-m-d', strtotime("-7 days",$today));
        $daterange = createDateRange($lookup_date2,$lookup_date1);
    //    print_r($daterange);die();

        $view = 'webform_views_manage_precipitation_data';
        $field = 'weight_avg';
        $sql = "SELECT * FROM `".$view."` WHERE `data_date` BETWEEN '".$lookup_date2."' AND '".$lookup_date1."'";
        $query = db_query($sql);
        $results = $query->fetchAll();
        foreach ($results as $result){
          $temp_date = date('Y-m-d', strtotime($result->data_date));
          $key = max(array_keys($daterange, $temp_date));
          if(is_numeric($key)){
            $weighted_precip[$key] = $result->$field;
          }
        }

        // One Day Weighted Average
        $weighted_precip_1 = $weighted_precip[6];
        if(empty($weighted_precip_1)==1){
          $precip_1day = "insufficient data";
        } else {
          $precip_1day = round($weighted_precip_1,6);
        }

        //Three Day Weighted Average
        $weighted_precip_3 = array($weighted_precip[6],$weighted_precip[5],$weighted_precip[4]);
        if(empty($weighted_precip_3[0])==1 OR empty($weighted_precip_3[1])==1 OR empty($weighted_precip_3[2])==1 ){
          $precip_3day = "insufficient data";
        } else {
          $precip_3day =round(array_sum($weighted_precip_3),6);
        }
        // Seven Day Weighted Average
        if(count($weighted_precip)==7){
          $precip_7day =round(array_sum($weighted_precip),6);
        } else {
          $precip_7day = "insufficient data";
        }

}
if(isset($_GET['email_flow'])){
        require_once 'sites/all/custom/flows/flows_functions.php';
        $lfsite_no = '01646500';
        $porsite_no = '01638500';

        //This code better follows the api to collect data
        function create_url_today($site){
          return $url_inst_hourly='https://waterservices.usgs.gov/nwis/iv/?format=json,1.1&sites='.$site.'&parameterCd=00060&siteStatus=all'; //most recent instantaneous (hourly) flow data
          //echo $url_inst_hourly;
          //die();
        }
        function create_url_yesterday($date,$site){
          $args = '&startDT='.$date.'&endDT='.$date.'&parameterCd=00060';
          return $url_daily='https://waterservices.usgs.gov/nwis/dv/?format=json&sites='.$site.$args;
          //echo $url_daily;
        }

        if(date('Y-m-d',$today)<date('Y-m-d',strtotime( 'now' ))){
          $results = get_usgs_json_vals(create_url_yesterday(date('Y-m-d',$today),$lfsite_no));
          $results = $results[1]['00060'];
          $key = array_keys($results)[0];
          $lftoday_cfs=$results[$key][$lfsite_no];
          $lftoday_mgd = round($lftoday_cfs*0.646316889697);

          $results = get_usgs_json_vals(create_url_yesterday(date('Y-m-d',$today),$porsite_no));
          $results = $results[1]['00060'];
          $key = array_keys($results)[0];
          $portoday_cfs=$results[$key][$porsite_no];
          $portoday_mgd = round($portoday_mgd*0.646316889697);
        } else {
          $results = get_usgs_json_vals(create_url_today($lfsite_no));
          $results = $results[1]['00060'];
          $temp = array_map(function ($val) {return max($val);}, transpose($results));
          $lftoday_cfs = $temp[1];
          $lftoday_mgd = round($lftoday_cfs*0.646316889697);

          $results = get_usgs_json_vals(create_url_today($porsite_no));
          $results = $results[1]['00060'];
          $temp = array_map(function ($val) {return max($val);}, transpose($results));
          $portoday_cfs = $temp[1];
          $portoday_mgd = round($portoday_cfs*0.646316889697);
        }

        $results = get_usgs_json_vals(create_url_yesterday($yesterday,$lfsite_no));
        $results = $results[1]['00060'];
        $key = array_keys($results)[0];
        $lfyesterday_cfs=$results[$key][$lfsite_no];
        $lfyesterday_mgd = round($lfyesterday_cfs*0.646316889697);

        $results = get_usgs_json_vals(create_url_yesterday($yesterday,$porsite_no));
        $results = $results[1]['00060'];
        $key = array_keys($results)[0];
        $poryesterday_cfs=$results[$key][$porsite_no];
        $poryesterday_mgd = round($poryesterday_cfs*0.646316889697);

}
if(isset($_GET['email_yesterday_net_potomac_production']) OR isset($_GET['email_yesterday_net_other_production'])){

        $suppliers = array('fw','wssc','wa','wa2','lw');

        $views = array();
        $views['fw'] = 'webform_views_data_submission_fw_am';
        $views['wssc'] = 'webform_views_data_submission_wssc_am';
        $views['wa'] = 'webform_views_data_submission_wa_am';
        $views['wa2'] = 'webform_views_data_submission_wa_am';
        $views['lw'] = 'webform_views_data_submission_lw_am';

        $fields = array();
        $fields['fw'] = 'yesterdays_daily_average_withdrawals_potomac_river';
        $fields['wssc'] = 'yesterdays_daily_average_withdrawal_potomac_river';
        $fields['wa'] = 'yesterdays_daily_average_withdrawals_potomac_river';
        $fields['wa2'] = '13_yesterdays_daily_average_withdrawals_potomac_river';
        $fields['lw'] = 'yesterdays_daily_average_withdrawals_potomac_river';

         foreach ($suppliers as $supplier) {
              $sql = "SELECT * FROM `".$views[$supplier]."` WHERE `today` = '".date('Y-m-d',$today)."'";
              $query = db_query($sql);
              $result = $query->fetchAll();
               if(empty($result)){
                       $yesterday_net_potomac['today'] = date('Y-m-d',$today);
                       $yesterday_net_potomac['yesterdays_potomac_withdrawal'] = 0;
               } else {
                  foreach($result as $array_obj){
                     $yesterday_net_potomac['today'] = $array_obj->today;
                     $yesterday_net_potomac['yesterdays_potomac_withdrawal'] = $array_obj->$fields[$supplier];
                   }
               }
               $email_yesterday_net_potomac_production[] = $yesterday_net_potomac;
          }

}
if(isset($_GET['email_yesterday_net_other_production'])){

          $suppliers = array('fw','wssc');

          $views = array();
          $views['fw'] = 'webform_views_data_submission_fw_am';
          $views['wssc'] = 'webform_views_data_submission_wssc_am';

          $fields = array();
          $fields['fw'] = 'yesterdays_daily_average_withdrawals_occoquan_rese';
          $fields['wssc'] = 'yesterdays_daily_average_withdrawal_patuxent_reser';

           foreach ($suppliers as $supplier) {
                $sql = "SELECT * FROM `".$views[$supplier]."` WHERE `today` = '".date('Y-m-d',$today)."'";
                $query = db_query($sql);
                $result = $query->fetchAll();
                 if(empty($result)){
                         $yesterday_net_other['today'] = date('Y-m-d',$today);
                         $yesterday_net_other['yesterdays_other_withdrawal'] = 0;
                 } else {
                    foreach($result as $array_obj){
                       $yesterday_net_other['today'] = $array_obj->today;
                       $yesterday_net_other['yesterdays_other_withdrawal'] = $array_obj->$fields[$supplier];
                     }
                 }
                 $email_yesterday_net_other_production[] = $yesterday_net_other;
            }
}
if(isset($_GET['email_loudoun'])){
            $view = 'webform_views_data_submission_lw_am';

            $fields = array();
            $fields['yesterday'] = 'yesterdays_broad_run_discharge_daily';
            $fields['today'] = 'todays_estimated_average_discharge___broad_run_mgd';
            $fields['tomorrow'] = 'tommorrows_daily_average_discharge___broad_run_mgd';

            $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
            $query = db_query($sql);
            $result = $query->fetchAll();
            if(empty($result)){
              $email_loudoun['yesterday'] = 0;
              $email_loudoun['today'] = 0;
              $email_loudoun['tomorrow'] = 0;
            } else {
              foreach($result as $array_obj){
                $email_loudoun['yesterday'] = $array_obj->$fields['yesterday'];
                $email_loudoun['today'] = $array_obj->$fields['today'];
                $email_loudoun['tomorrow'] = $array_obj->$fields['tomorrow'];
              }
            }
}
if(isset($_GET['email_today_demand'])){

            $views = array();
            $views['fw'] = 'webform_views_fw__demand_forecast';
            $views['wssc'] = 'webform_views_wssc__demand_forecast';
            $views['wa'] = 'webform_views_wa__demand_forecast';

            foreach ($views as $view) {
              $sql = "SELECT * FROM `".$view."` WHERE `forecast_date` = '".date('Y-m-d',$today)."'";
              $query = db_query($sql);
              $results = $query->fetchAll();
              foreach ($results as $result){
                $today_demand['forecast_date'] = $result->forecast_date;
                $today_demand['demand_forecast'] = $result->demand_forecast;
              }
              $email_today_demand[] = $today_demand;
            }
}
if(isset($_GET['email_tomorrows_demand'])){
            $views = array();
            $views['fw'] = 'webform_views_fw__demand_forecast';
            $views['wssc'] = 'webform_views_wssc__demand_forecast';
            $views['wa'] = 'webform_views_wa__demand_forecast';

            foreach ($views as $view) {
              $sql = "SELECT * FROM `".$view."` WHERE `forecast_date` = '".$tomorrow."'";
              $query = db_query($sql);
              $results = $query->fetchAll();
              foreach ($results as $result){
                $tomorrows_demand['forecast_date'] = $result->forecast_date;
                $tomorrows_demand['demand_forecast'] = $result->demand_forecast;
              }
              $email_tomorrows_demand[] = $tomorrows_demand;
            }
}
if(isset($_GET['email_operations_am'])){
            $view = 'webform_views_data_submission_icprb_am';

            $fields = array();
            $fields['fw']='fw_ops_am'; //17 'email_tomorrows_demand_pm'
            $fields['wssc']='wssc_ops_am'; //17 'email_tomorrows_demand_pm'
            $fields['ls']='seneca_release_am'; //17 'email_tomorrows_demand_pm'
            $fields['wa']='wa_ops_am'; //17 'email_tomorrows_demand_pm'

            $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
            $query = db_query($sql);
            $result = $query->fetchAll();
            if(empty($result)){
              $email_operations_am['fw'] = 'none submitted';
              $email_operations_am['wssc'] = 'none submitted';
              $email_operations_am['ls'] = 'none submitted';
              $email_operations_am['wa'] = 'none submitted';
            } else {
              foreach($result as $array_obj){
                $email_operations_am['fw'] = $array_obj->$fields['fw'];
                $email_operations_am['wssc'] = $array_obj->$fields['wssc'];
                $email_operations_am['ls'] = $array_obj->$fields['ls'];
                $email_operations_am['wa'] = $array_obj->$fields['wa'];
              }
            }
}
if(isset($_GET['email_operations_pm'])){
          $view = 'webform_views_data_submission_icprb_pm';

          $fields = array();
          $fields['fw']='fw_ops_pm'; //17 'email_tomorrows_demand_pm'
          $fields['wssc']='wssc_ops_pm'; //17 'email_tomorrows_demand_pm'
          $fields['ls']='seneca_release_pm'; //17 'email_tomorrows_demand_pm'
          $fields['wa']='wa_ops_pm'; //17 'email_tomorrows_demand_pm'

          $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
          $query = db_query($sql);
          $result = $query->fetchAll();
          if(empty($result)){
            $email_operations_pm['fw'] = 'none submitted';
            $email_operations_pm['wssc'] = 'none submitted';
            $email_operations_pm['ls'] = 'none submitted';
            $email_operations_pm['wa'] = 'none submitted';
          } else {
            foreach($result as $array_obj){
              $email_operations_pm['fw'] = $array_obj->$fields['fw'];
              $email_operations_pm['wssc'] = $array_obj->$fields['wssc'];
              $email_operations_pm['ls'] = $array_obj->$fields['ls'];
              $email_operations_pm['wa'] = $array_obj->$fields['wa'];
            }
          }
}
if(isset($_GET['email_northbranch_releases_am'])){
          /*want to automate this by working on uploading a csv that either we or the USACE can upload*/
          $field = 'nb_release_am';
          $view = 'webform_views_data_submission_icprb_am';
          $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
          $query = db_query($sql);
          $result = $query->fetchAll();
          if(empty($result)){
            $email_northbranch_releases_am = 'none submitted';
          } else {
            foreach($result as $array_obj){
              $email_northbranch_releases_am = $array_obj->$field;
            }
          }
}
if(isset($_GET['email_northbranch_releases_pm'])){
            // /*want to automate this by working on uploading a csv that either we or the USACE can upload*/
            $field = 'nb_release_pm';
            $view = 'webform_views_data_submission_icprb_pm';
            $sql = "SELECT * FROM `".$view."` WHERE `today` = '".date('Y-m-d',$today)."'";
            $query = db_query($sql);
            $result = $query->fetchAll();
            if(empty($result)){
              $email_northbranch_releases_pm = 'none submitted';
            } else {
              foreach($result as $array_obj){
                $email_northbranch_releases_pm = $array_obj->$field;
              }
            }
}
if(isset($_GET['email_reservoirs_usable_storage'])){
    $sources = array('fw','wssc','icprb');

    //put this in a webform app that downloads JRR and SR automatically
    $capacities['occoquan_reservoir_total_usable_capacity']=8.5;
    $capacities['patuxent_reservoirs_total_usable_capacity']=10.2;
    $capacities['little_seneca_reservoir_total_usable_capacity']=3.9;
    $capacities['jrr_total_usable_capacity']=29.4;
    $capacities['sr_total_usable_capacity']=6.3;
    $ratio=0.4456;

    $fields = array();
    $fields['fw'][0]='occoquan_reservoir_current_usable_storage'; //17 'email_tomorrows_demand_pm'
    $fields['wssc'][0]='patuxent_reservoirs_current_usable_storage'; //17 'email_tomorrows_demand_pm'
    $fields['wssc'][1]='little_seneca_reservoir_current_usable_storage'; //17 'email_tomorrows_demand_pm'
    $fields['icprb'][0]='jrr_current_usable_storage'; //17 'email_tomorrows_demand_pm'
    $fields['icprb'][1]='jrr_current_usable_ws_storage'; //17 'email_tomorrows_demand_pm'
    $fields['icprb'][2]='sr_current_usable_storage'; //17 'email_tomorrows_demand_pm'

    $views = array();
    $views['fw']='webform_views_data_submission_fw_am';
    $views['wssc'] ='webform_views_data_submission_wssc_am';
    $views['icprb'] = 'webform_views_data_submission_icprb_am';

   foreach ($sources as $source) {
      $sql = "SELECT * FROM `".$views[$source]."` WHERE `today` = '".date('Y-m-d',$today)."'";
      $query = db_query($sql);
      $result = $query->fetchAll();
      if(empty($result)){
        foreach ($fields[$source] as $field){
          $usable_storage[$field] = 0;
        }
      } else {
        foreach($result as $array_obj){
            foreach ($fields[$source] as $field){
              if(empty($array_obj->$field)){
                $usable_storage[$field] = 0;

              }else {
              $usable_storage[$field] = $array_obj->$field;
            }
            }
        }
      }
    }
}
if(isset($_GET['email_footnote'])){
  if($format == "daily"){
    $email_footnote = 'Note: Potomac River flow at Point of Rocks has dropped below the low-flow threshold of 2,000 cfs. When this occurs, CO-OP begins conducting daily monitoring and reporting of Potomac flows and withdrawals. ICPRB asks that communications be sent by 8:00 am. Reporting can be submit through www.icprbcoop.org, by email to coop@icprb.org, or by telephone by leaving a message with the CO-OP operations number at 301-274-8132. Reports can be viewed at https://docs.google.com/document/pub?id=1FOUFA-9onczBUgmW1fS_3t8lO87jqsKpEoO3jDYGh6U. Thank you for your contribution to the monitoring efforts.';
  }
  if($format == "enhanced_pm" or $format=="enhanced_am"){
    $email_footnote = 'Note: Flow levels have dropped to a level that has triggered enhanced monitoring, per the drought operations manual of the Water Supply Coordination Agreement. Per the agreement, when adjusted flow at Little Falls (adjusted flow is gage flow plus upstream withdrawals) less the environmental flow is less than twice the Washington metropolitan area withdrawals, the water suppliers will begin reporting estimates of today\'s demand, estimates of tomorrow\'s demand, and yesterday\'s withdrawals from the Patuxent and Occoquan reservoirs in addition to the Potomac withdrawals. ICPRB asks that communications be sent by 8:00 am and updates sent by 1:00 pm. Reporting can be submit through www.icprbcoop.org, by email to coop@icprb.org, or by telephone by leaving a message with the CO-OP operations number at 301-274-8132. Reports can be viewed at https://docs.google.com/document/pub?id=1FOUFA-9onczBUgmW1fS_3t8lO87jqsKpEoO3jDYGh6U.  Thank you for your contribution to the monitoring efforts.';
  }
}
?>
