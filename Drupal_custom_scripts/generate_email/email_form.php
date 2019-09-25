<?php
//require_once 'sites/all/custom/functions.php';
// Defaults
date_default_timezone_set('America/New_York');
$error = array();
$show_form = true;
$today = strtotime( 'now' );
$email_parts = array(
    'email_summary',
    'email_precipitation',
    'email_flow',
    'email_yesterday_net_potomac_production',
    'email_yesterday_net_other_production',
    'email_loudoun',
    // 'email_today_demand_am',
    'email_today_demand',
    // 'email_tomorrows_demand_am',
    'email_tomorrows_demand',
    'email_operations',
    'email_northbranch_releases',
    'email_reservoirs_usable_storage',
    'email_footnote'
);

// Process GET Data
if(isset($_GET['submit'])){
    if ( isset($_GET['today_date']) and trim($_GET['today_date']) ) {
        $today = strtotime( $_GET['today_date'] );
    }
    // if ( isset($_GET['enddate']) and trim($_GET['enddate']) ) {
    //     $end_time = strtotime( $_GET['enddate'] );
    // }
    // if ($start_time > $end_time) {
    //     $errors[] =  'The specified start time must be equal to or greater than the specified end time.';
    // }
    if (isset($_GET['format'])) {
        $show_form = false;
        if ($_GET['format']=="daily") {
            $format = 'daily';
        } elseif ($_GET['format']=="enhanced_am") {
            $format = 'enhanced_am';
        } elseif ($_GET['format']=="enhanced_pm") {
            $format = 'enhanced_pm';
        }
    }

    if (count($email_settings)==1) { //just today
      $errors[] = 'Please check an email part to include.';
    }
    if (!empty($errors)) $show_form = true;

} //end if isset submit

// Start Output
if ($show_form) {
  //if not showing the csv, then show the form
  ?>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
    $( function() {
      $( ".datepicker" ).datepicker();
    } );


    $('document').ready( function() {

        function get_date_string(my_date) {
          if (!my_date) my_date = new Date();
          console.log(my_date);
          var dd = my_date.getDate();
          var mm = my_date.getMonth()+1; //January is 0!
          var yyyy = my_date.getFullYear();
          if(dd<10) dd='0'+dd;
          if(mm<10) mm='0'+mm;
          var my_date = mm+'/'+dd+'/'+yyyy;
          return my_date;
        }

        function changeValues() {
          var radio_value = $('input:radio[name=format]:checked').val();
          $format_radio_group = $('input[type=radio][name=format]');
          if (radio_value == 'daily') {
            $('input[name=today_date]').val( get_date_string() );
            $('input[name=email_summary]').prop('checked',true);
            $('input[name=email_precipitation]').prop('checked',true);
            $('input[name=email_flow]').prop('checked',true);
            $('input[name=email_yesterday_net_potomac_production]').prop('checked',true);
            $('input[name=email_yesterday_net_other_production]').prop('checked',false);
            $('input[name=email_loudoun]').prop('checked',false);
            // $('input[name=email_today_demand_am]').prop('checked',false);
            $('input[name=email_today_demand]').prop('checked',false);
            // $('input[name=email_tomorrows_demand_am]').prop('checked',false);
            $('input[name=email_tomorrows_demand]').prop('checked',false);
            $('input[name=email_operations_am]').prop('checked',false);
            $('input[name=email_operations_pm]').prop('checked',false);
            $('input[name=email_northbranch_releases_am]').prop('checked',false);
            $('input[name=email_northbranch_releases_pm]').prop('checked',false);
            $('input[name=email_reservoirs_usable_storage]').prop('checked',false);
            $('input[name=email_footnote]').prop('checked',true);
          } else if (radio_value == 'enhanced_am') {
//            var yesterday = new Date( Date.now() - 60*60*24*1000*7 )
//            $('input[name=today_date]').val( get_date_string(yesterday) );
            $('input[name=today_date]').val( get_date_string() );
            $('input[name=email_summary]').prop('checked',true);
            $('input[name=email_precipitation]').prop('checked',true);
            $('input[name=email_flow]').prop('checked',true);
            $('input[name=email_yesterday_net_potomac_production]').prop('checked',true);
            $('input[name=email_yesterday_net_other_production]').prop('checked',true);
            $('input[name=email_loudoun]').prop('checked',true);
            $('input[name=email_today_demand]').prop('checked',true);
            $('input[name=email_tomorrows_demand]').prop('checked',true);
            $('input[name=email_operations_am]').prop('checked',false);
            $('input[name=email_operations_pm]').prop('checked',false);
            $('input[name=email_northbranch_releases_am]').prop('checked',false);
            $('input[name=email_northbranch_releases_pm]').prop('checked',false);
            $('input[name=email_reservoirs_usable_storage]').prop('checked',false);
            $('input[name=email_footnote]').prop('checked',true);
          } else if (radio_value == 'enhanced_pm') {
  //          var yesterday = new Date( Date.now() - 60*60*24*1000*7 )
  //  $('input[name=today_date]').val( get_date_string(yesterday) );
            $('input[name=today_date]').val( get_date_string() );
            $('input[name=email_summary]').prop('checked',true);
            $('input[name=email_precipitation]').prop('checked',true);
            $('input[name=email_flow]').prop('checked',true);
            $('input[name=email_yesterday_net_potomac_production]').prop('checked',true);
            $('input[name=email_yesterday_net_other_production]').prop('checked',true);
            $('input[name=email_loudoun]').prop('checked',true);
            $('input[name=email_today_demand]').prop('checked',true);
            $('input[name=email_tomorrows_demand]').prop('checked',true);
            $('input[name=email_operations_am]').prop('checked',false);
            $('input[name=email_operations_pm]').prop('checked',false);
            $('input[name=email_northbranch_releases_am]').prop('checked',false);
            $('input[name=email_northbranch_releases_pm]').prop('checked',false);
            $('input[name=email_reservoirs_usable_storage]').prop('checked',false);
            $('input[name=email_footnote]').prop('checked',true);
          }
        }

        $('input[type=radio][name=format]').change( function() {
            changeValues();
        })

        changeValues();

    });

  </script>

  <form method="GET">
    <p><b>Generate the drought monitoring email below.</b></p>

    <p><b>Select format</b></p>
    <input
      <?=((isset($_GET['format']) AND $_GET['format']=="daily") ? 'checked="checked"' : '' ) ?>
      type = "radio" class="formatpicker" name="format" value="daily" checked="checked"
      id="format_daily"
    /> <label style="font-weight:normal; display:inline;" for="format_daily" >Daily</label><br/>

    <input
      <?=((isset($_GET['format']) AND $_GET['format']=="enhanced_am") ? 'checked="checked"' : '' ) ?>
      type = "radio" class="formatpicker" name="format" value="enhanced_am"
      id="format_enhanced_am"
    /> <label style="font-weight:normal; display:inline;" for="format_enhanced_am" >Enhanced 9:00 AM</label><br/>

    <input
      <?=((isset($_GET['format']) AND $_GET['format']=="enhanced_pm") ? 'checked="checked"' : '' ) ?>
      type = "radio" class="formatpicker" name="format" value="enhanced_pm"
      id="format_enhanced_pm"
    /> <label style="font-weight:normal; display:inline;" for="format_enhanced_pm" >Enhanced 2:00 PM</label><br/>

    <br/>

    <p><b>Select today's date</b></p>
    Date: <input class="datepicker" name="today_date" value="<?=date('m/d/Y',$today) ?>"></br>

    </br>

    <p><b>Select information to include</b></p>
    <input type="checkbox" name="email_summary" value="email_summary" id="email_summary"/>
      <label style="font-weight:normal; display:inline;" for="email_summary"> Summary</label><br/>
    <input type="checkbox" name="email_precipitation" value="email_precipitation" id="email_precip"/>
      <label style="font-weight:normal; display:inline;" for="email_precip"> Basin-wide average precipitation (above Little Falls)</label><br/>
    <input type="checkbox" name="email_flow" value="email_flow" id="email_flow"/>
      <label style="font-weight:normal; display:inline;" for="email_flow"> Daily flows</label><br/>
    <input type="checkbox" name="email_yesterday_net_potomac_production" value="email_yesterday_net_potomac_production" id="email_net_potomac"/>
      <label style="font-weight:normal; display:inline;" for="email_net_potomac"> Net Potomac withdrawal yesterday</label><br/>
    <input type="checkbox" name="email_yesterday_net_other_production" value="email_yesterday_net_other_production" id="email_net_other"/>
      <label style="font-weight:normal; display:inline;" for="email_net_other"> Patuxent, Occoquan, and net total system withdrawal</label><br/>
    <input type="checkbox" name="email_loudoun" value="email_loudoun" id="email_loudoun"/>
      <label style="font-weight:normal; display:inline;" for="email_loudoun"> Loudoun Water Broad Run discharge</label><br/>
    <input type="checkbox" name="email_today_demand" value="email_today_demand" id="email_today_demand"/>
      <label style="font-weight:normal; display:inline;" for="email_today_demand"> Today's estimated demand</label><br/>
    <input type="checkbox" name="email_tomorrows_demand" value="email_tomorrows_demand" id="email_tomorrows_demand"/>
      <label style="font-weight:normal; display:inline;" for="email_tomorrows_demand"> Tomorrow's estimated demand</label><br/>
    <input type="checkbox" name="email_operations_am" value="email_operations_am" id="email_operations_am">
      <label style="font-weight:normal; display:inline;" for="email_operations"> Operations A.M.</label><br/>
    <input type="checkbox" name="email_operations_pm" value="email_operations_pm" id="email_operations_pm">
      <label style="font-weight:normal; display:inline;" for="email_operations"> Operations P.M.</label><br/>
    <input type="checkbox" name="email_northbranch_releases_am" value="email_northbranch_releases_am" id="email_northbranch_releases_am"/>
      <label style="font-weight:normal; display:inline;" for="email_northbranch_releases_am"> Northbranch release summary A.M.</label><br/>
    <input type="checkbox" name="email_northbranch_releases_pm" value="email_northbranch_releases_pm" id="email_northbranch_releases_pm"/>
      <label style="font-weight:normal; display:inline;" for="email_northbranch_releases_pm"> Northbranch release summary P.M.</label><br/>
    <input type="checkbox" name="email_reservoirs_usable_storage" value="email_reservoirs_usable_storage_pm" id="email_reservoirs_usable_storage"/>
      <label style="font-weight:normal; display:inline;" for="email_reservoirs_usable_storage"> Reservoirs usable storage</label><br/>
    <input type="checkbox" name="email_footnote" value="email_footnote" id="email_footnote"/>
      <label style="font-weight:normal; display:inline;" for="email_footnote"> Footnote</label><br/>
    <br/>

    <input type="submit" name="submit" value="Submit" />

  </form>
  <?php
}

// Show email
if(isset($_GET['submit'])){
  if ( !empty($errors) ) {
    // if there are errors, show them
    echo '<div style="padding:1em; border:1px solid red; color:red; margin-top:1em;">
      Error'.(count($errors)>1 ? 's' : '').'<ul><li>'.implode('</li><li>',$errors).'</li></ul>
    </div>';
  } else {


    if ( empty($errors) ) {
        $yesterday = date('Y-m-d', strtotime("-1 days",$today));
        $tomorrow = date('Y-m-d', strtotime("+1 days",$today));

        include('email_data.php');
        include('email_text.php');

    }


  } //end if no errors
} //end if isset submit
?>
