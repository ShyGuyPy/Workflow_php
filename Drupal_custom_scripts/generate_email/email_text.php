<?php

// make readable on screen
//header("Content-Type: text/plain");
echo "<br>";
$dt = strtotime($today);
$day = date('l', $today);
//die();
echo "<strong>".'Daily Potomac flow and demand update ('.$day.' '.date('Y-m-d',$today).')'."</strong><br><br>";
//echo "<strong>".'Daily Potomac flow and demand update ('.date("D",$today).' '.$today.')'."</strong><br><br>";
if(isset($_GET['email_summary'])){
  // Introduction always limits to sql querry to the end_date that is selected.
  // So if a table of past values is wanted, it won't pick the associated summary introductions as well.
 echo $email_summary."<br><br>";
}
if(isset($_GET['email_precipitation'])){
  echo "<strong>".'Recent basin-wide average precipitation (above Little Falls):'."</strong><br>";
  echo '(based on CO-OP\'s Low Flow Forecast System analysis of Middle Atlantic River Forecast Center (MARFC) gridded multisensor precipitation estimates)'."<br>";
  echo 'Yesterday\'s area-weighted average basin precip: '.$precip_1day.' (inches)'."<br>";
  echo 'Past 3-day cumulative area-weighted average basin precip: '.$precip_3day.' (inches)'."<br>";
  echo 'Past 7-day cumulative area-weighted average basin precip: '.$precip_7day.' (inches)'."<br><br>";
//  echo '***************** above calculations under review *******************'."<br><br>";

/*  echo 'Yesterday\'s basin-wide average precipitation (above Little Falls): X.XX inches'."<br><br>";
  echo '(based on CO-OP\'s Low Flow Forecast System analysis of MARFC gridded quantitative precipitation estimates)'."<br><br>";
*/
}
if(isset($_GET['email_flow'])){
  // USGS screenscrape. Need to switch to the USGS API.
  echo "<strong>".'Daily Flows:'."</strong><br>";
  echo '    Little Falls gage flow '.$yesterday.': '. $lfyesterday_mgd.' MGD  ('.$lfyesterday_cfs.' cfs)'."<br>";
  echo '    Little Falls gage flow '.date('Y-m-d',$today).': '. $lftoday_mgd.' MGD  (est., based on recently available real time data)  ('.$lftoday_cfs.' cfs)'."<br>";
  echo '          Note: Gage flow at Little Falls is measured after water supply withdrawals.'."<br>";
  echo '    Point of Rocks flow '.$yesterday.': '. $poryesterday_mgd.' MGD  ('.$poryesterday_cfs.' cfs)'."<br>";
  echo '    Point of Rocks flow '.date('Y-m-d',$today).': '. $portoday_mgd.' MGD  (est., based on recently available real time data)  ('.$portoday_cfs.' cfs)'."<br><br>";
}
if(isset($_GET['email_yesterday_net_potomac_production'])){
  echo "<strong>".'Yesterday\'s Net Potomac withdrawal ('.$yesterday.'):'."</strong><br>";
  echo '    FW Corbalis withdrawal (Potomac): '.round($email_yesterday_net_potomac_production[0]['yesterdays_potomac_withdrawal']).' MGD'."<br>";
  echo '    WSSC Potomac withdrawal: '.round($email_yesterday_net_potomac_production[1]['yesterdays_potomac_withdrawal']).' MGD'."<br>";
  echo '    Aqueduct withdrawal: '.round(($email_yesterday_net_potomac_production[2]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[3]['yesterdays_potomac_withdrawal'])).' MGD'."<br>";
  echo '    Loudoun withdrawal: '.round($email_yesterday_net_potomac_production[4]['yesterdays_potomac_withdrawal']).' MGD'."<br>";
  echo '    Total Potomac withdrawal: '.round($email_yesterday_net_potomac_production[0]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[1]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[2]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[3]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[4]['yesterdays_potomac_withdrawal']).' MGD'."<br><br>";
}
if(isset($_GET['email_yesterday_net_other_production'])){
  echo "<strong>".'Yesterday\'s Patuxent, Occoquan, and Net Total System Withdrawal ('.$yesterday.'):'."</strong><br>";
  echo '    FW Occoquan withdrawal:'.round($email_yesterday_net_other_production[0]['yesterdays_other_withdrawal']).' MGD'."<br>";
  echo '    WSSC Patuxent withdrawal:'.round($email_yesterday_net_other_production[1]['yesterdays_other_withdrawal']).' MGD'."<br>";
  echo '    Total system withdrawal:'.round($email_yesterday_net_other_production[0]['yesterdays_other_withdrawal']+$email_yesterday_net_other_production[1]['yesterdays_other_withdrawal']+$email_yesterday_net_potomac_production[0]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[1]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[2]['yesterdays_potomac_withdrawal']+$email_yesterday_net_potomac_production[3]['yesterdays_potomac_withdrawal']).' MGD'."<br><br>";
}
if(isset($_GET['email_loudoun'])){
  echo "<strong>".'Loudoun Water Broad Run discharge:'."</strong><br>";
  echo '    Yesterday\'s ('.$yesterday.'):'.round($email_loudoun['yesterday']).' MGD'."<br>";
  echo '    Today\'s ('.date('Y-m-d',$today).'):'.round($email_loudoun['today']).' MGD'."<br>";
  echo '    Tomorrow\'s ('.$tomorrow.') :'.round($email_loudoun['tomorrow']).' MGD'."<br><br>";
}
if(isset($_GET['email_today_demand'])){
  echo "<strong>".'Today\'s estimated production ('.date('Y-m-d',$today).'):'."</strong><br>";
  echo '    FW estimated production:'.round($email_today_demand[0]['demand_forecast']).' MGD'."<br>";
  echo '    WSSC estimated production:'.round($email_today_demand[1]['demand_forecast']).' MGD'."<br>";
  echo '    Aqueduct estimated production:'.round($email_today_demand[2]['demand_forecast']).' MGD'."<br>";
  echo '    Total estimated production:'.round($email_today_demand[0]['demand_forecast']+$email_today_demand[1]['demand_forecast']+$email_today_demand[2]['demand_forecast']).' MGD'."<br><br>";
}
if(isset($_GET['email_tomorrows_demand'])){
  echo "<strong>".'Tomorrow\'s estimated production ('.$tomorrow.'):'."</strong><br>";
  echo '    FW estimated production:'.round($email_tomorrows_demand[0]['demand_forecast']).' MGD'."<br>";
  echo '    WSSC estimated production:'.round($email_tomorrows_demand[1]['demand_forecast']).' MGD'."<br>";
  echo '    Aqueduct estimated production:'.round($email_tomorrows_demand[2]['demand_forecast']).' MGD'."<br>";
  echo '    Total estimated production:'.round($email_tomorrows_demand[0]['demand_forecast']+$email_tomorrows_demand[1]['demand_forecast']+$email_tomorrows_demand[2]['demand_forecast']).' MGD'."<br><br>";
}
if(isset($_GET['email_operations_am'])){
  echo "<strong>".'Recommended operations for today ('.date('Y-m-d',$today).' A.M.):'."</strong><br>";
  echo 'Fairfax Water:'."<br>";
  echo $email_operations_am['fw']."<br><br>";
  echo 'WSSC:'."<br>";
  echo $email_operations_am['wssc']."<br><br>";
  echo 'Seneca (release date, time, amount in MGD):'."<br>";
  echo $email_operations_am['ls']."<br><br>";
  echo 'Aqueduct:'."<br>";
  echo $email_operations_am['wa']."<br><br>";
}
if(isset($_GET['email_operations_pm'])){
  echo "<strong>".'Recommended operations for today ('.date('Y-m-d',$today).' P.M.):'."</strong><br>";
  echo 'Fairfax Water:'."<br>";
  echo $email_operations_pm['fw']."<br><br>";
  echo 'WSSC:'."<br>";
  echo $email_operations_pm['wssc']."<br><br>";
  echo 'Seneca (release date, time, amount in MGD):'."<br>";
  echo $email_operations_pm['ls']."<br><br>";
  echo 'Aqueduct:'."<br>";
  echo $email_operations_pm['wa']."<br><br>";
}
if(isset($_GET['email_northbranch_releases_am'])){
  echo "<strong>".'North Branch Reservoirs A.M. Summary:'."</strong><br>";
  echo $email_northbranch_releases_am."<br><br>";
}
if(isset($_GET['email_northbranch_releases_pm'])){
  echo "<strong>".'North Branch Reservoirs P.M. Summary:'."</strong><br>";
  echo $email_northbranch_releases_pm."<br><br>";
}

if(isset($_GET['email_reservoirs_usable_storage'])){
  $jrr_total = $capacities['jrr_total_usable_capacity'];
  $jrr_usable = $usable_storage['jrr_current_usable_storage'];
  $jrr_ws_usable = $usable_storage['jrr_current_usable_ws_storage'];
  $jrr_wq_usable = $jrr_usable-$jrr_ws_usable;
  $jrr_ws_total = $jrr_total*$ratio;
  $jrr_wq_total = $jrr_total-$jrr_ws_total;
  echo "<strong>".'Reservoirs - Usable storage ('.date('Y-m-d',$today).' A.M., BG):'."</strong><br>";
  echo 'Facility, %Full, Current, Capacity*'."<br>";
  echo "WSSC’s Patuxent reservoirs, %".round($usable_storage['patuxent_reservoirs_current_usable_storage']/$capacities['patuxent_reservoirs_total_usable_capacity']*100).','.round($usable_storage['patuxent_reservoirs_current_usable_storage'],2).','.round($capacities['patuxent_reservoirs_total_usable_capacity'],2)."<br>";
  echo "Fairfax Water’s Occoquan reservoir, %".round($usable_storage['occoquan_reservoir_current_usable_storage']/$capacities['occoquan_reservoir_total_usable_capacity']*100).','.round($usable_storage['occoquan_reservoir_current_usable_storage'],2).','.round($capacities['occoquan_reservoir_total_usable_capacity'],2)."<br>";
  echo 'Little Seneca Reservoir, %'.round($usable_storage['little_seneca_reservoir_current_usable_storage']/$capacities['little_seneca_reservoir_total_usable_capacity']*100).','.round($usable_storage['little_seneca_reservoir_current_usable_storage'],2).','.round($capacities['little_seneca_reservoir_total_usable_capacity'],2)."<br>";
  echo 'Jennings Randolph Total Reservoir, %'.round($jrr_usable/$jrr_total*100).','.round($jrr_usable,2).','.round($jrr_total,2)."<br>";
  echo 'Jennings Randolph water supply**, %'.round($jrr_ws_usable/$jrr_ws_total*100).','.round($jrr_ws_usable,2).','.round($jrr_ws_total,2)."<br>";
  echo 'Jennings Randolph water quality**, %'.round($jrr_wq_usable/$jrr_wq_total*100).','.round($jrr_wq_usable,2).','.round($jrr_wq_total,2)."<br>";
  echo 'Savage Reservoir, %'.round($usable_storage['sr_current_usable_storage']/$capacities['sr_total_usable_capacity']*100).','.round($usable_storage['sr_current_usable_storage'],2).','.round($capacities['sr_total_usable_capacity'],2)."<br>";
  echo '*Storage and capacities for Occoquan, Patuxent and Little Seneca reservoirs are provided by Washington metropolitan area water utilities, and based on best available information.  Storage and capacities for Jennings Randolph and Savage reservoirs are based on observed water levels and available US ACE water level/storage tables from 1998.  ICPRB estimates that sedimentation has resulted in a loss of total available storage in Jennings Randolph Reservoir of 1.6 BG in recent years, and this loss is not reflected in the numbers above.'."<br>";
  echo '** ICPRB\'s initial estimate.  Final accounting of Jennings Randolph water supply versus water quality storage will be provided at a later date by the US ACE.'."<br><br>";
}
if(isset($_GET['email_footnote'])){
  echo $email_footnote;
}
?>
