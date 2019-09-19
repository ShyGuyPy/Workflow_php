<?php
//script uses imap to connect to gmail and search inbox
//https://stuporglue.org/downloads/mailReader.txt
//http://www.perlmonks.org/?node_id=117686
//https://davidwalsh.name/gmail-php-imap
//https://mail.google.com/mail/u/0/#settings/fwdandpop
//http://php.net/manual/en/function.imap-fetchstructure.php
$all_data_for_database = array();

/* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'coop@icprb.org';
$password = '2667_Cashz';

/* try to connect */
$imap = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* if emails are returned, cycle through each... */
function get_data_from_emails($emails,$imap,$supplier_label){ //begin function

  /*define mimetypes*/
  $accepted_mime_types = array(

      'csv' => 'text/csv',
      'xls' => 'application/vnd.ms-excel',
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  );

  $attachments = array();
  $data_for_database = array();

  if($emails) { // begin if emails

    /* put the newest emails on top */
    rsort($emails);

    /* for every email... */
    foreach($emails as $email_number) { //begin foreach emails
      // echo $email_number;
      /* get information specific to this email */
      $header = imap_header($imap, $email_number);
      $overview = imap_fetch_overview($imap,$email_number,0); // returns array of objects for subject, from, to, date, message-id, references, in_reply_to, size, uid, msgno, recent, flagged, answered, deleted, seen, draft, udate
      $message = imap_fetchbody($imap,$email_number,2); // should return  multi-part message in MIME format
      $structure = imap_fetchstructure($imap,$email_number);

      imap_mail_move($imap,$email_number,"Imported");
  //    $status = imap_setflag_full($imap, $email_number, '\\Seen');

      /*get overview*/
      $size=sizeof($overview);
      for($i=$size-1;$i>=0;$i--){
        $val=$overview[$i];
        $msg=$val->msgno;
        $from=$val->from;
        $date1=$val->date;
        $subj=$val->subject;
       //echo "#$msg: From:'$from' Date:'$date' Subject:'$subj'<br><br>";
        // this variable is used later to rename the files with the filedate as well.
        $date = date_parse($date1);
        $date = $date['year'].'_'.$date['month'].'_'.$date['day'];
        $timestamp = strtotime($date1);
      //  echo $date."<br><br>";
      }

      /*get structures*/
      if(isset($structure->parts) && count($structure->parts)) { //begin if structure

        for($i = 0; $i < count($structure->parts); $i++) { //begin for structure

          if($structure->parts[$i]->ifdparameters) {
            foreach($structure->parts[$i]->dparameters as $object) {
              if(strtolower($object->attribute) == 'filename') {
                $attachments[$i]['is_attachment'] = true;
                $attachments[$i]['filename'] = $object->value;
                $attachments[$i]['timestamp'] = $timestamp;//date_format($date, 'U');
                //$attachments[$i]['filesize'] = $structure->parts[$i]->bytes;
                $attachments[$i]['extension'] = strtolower(array_pop(explode('.',$object->value)));
                $attachments[$i]['filemime'] = $accepted_mime_types[$attachments[$i]['extension']];
              }
            }
          }

          if($attachments[$i]['is_attachment']) {
            // All my Excel files have encoding set to 3.
              $attachments[$i]['attachment'] = imap_fetchbody($imap, $email_number, $i+1);
              if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                  $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
              }
          }
          if (strlen(trim($attachments[$i]['filename'])) > 0) {
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $attachments[$i]['filename']);
            $file_name=$supplier_label.$withoutExt.'_'.$date.'.'.$attachments[$i]['extension'];
//            $file_name='FW_Auto_'.$withoutExt.'_'.$date.'.'.$attachments[$i]['extension'];
  //          $file_name='WSSC_Auto_'.$withoutExt.'_'.$date.'.'.$attachments[$i]['extension'];

            $attachments[$i]['filename']=$file_name;
            $attachments[$i]['uri']='public://webform/submitted/'.$file_name;
            global $base_path;
  //          $link = file_create_url($_SERVER['DOCUMENT_ROOT'].$base_path.'sites/default/files/webform/submitted/'.$file_name);
          $link = $base_path.'sites/default/files/webform/submitted/'.$file_name;
          $attachments[$i]['filesize'] = filesize($link);
  //          echo filesize($base_path.'sites/default/files/webform/submitted/'.$file_name)."<br><br>";
          //  echo '<a href="'.$link.'" target="_blank">'.$file_name.'</a>'."<br><br>";

          // the file_save_data function stores file in file_managed table
          $file = file_save_data($attachments[$i]['attachment'], $attachments[$i]['uri'], $replace=FILE_EXISTS_REPLACE);
          $fid = $file -> fid;
          $attachments[$i]['fid']=$fid;
          $data_for_database[]=$attachments;

          }
        } //end for structure
      } //end if structure
    } //end foreach emails
  } //end if emails
  return $data_for_database;
}

/* grab emails */
if($download_inc['wssc']){
  $emails_wssc = imap_search($imap, 'UNSEEN SUBJECT "Water Supply ICPRB" FROM "notifier@wsscwater.com"'); //one used when automated emails were working
  $all_data_for_database['wssc'] = get_data_from_emails($emails_wssc,$imap,'WSSC_Auto_');
}
if($download_inc['fw']){
  $emails_fw = imap_search($imap, 'UNSEEN SUBJECT "DAILY GTP AND CTP WITHDRAWAL REPORT" FROM "hachwims_client@fairfaxwater.org"'); //one used when automated emails were working
//  $emails_hachwims_gnr = imap_search($imap, 'UNSEEN SUBJECT "DAILY GTP AND CTP WITHDRAWAL REPORT" FROM "hachwims_gnr@opssys.com"'); //one used when automated emails were working
//  $emails_niffy_forward = imap_search($imap, 'UNSEEN SUBJECT "FW: DAILY GTP AND CTP WITHDRAWAL REPORT" FROM "nsaji@fairfaxwater.org"');//one used when Niffy forwards email
//  if($emails_hachwims){
//    $emails_fw=$emails_hachwims;
//    foreach($emails_niffy_forward as $email_number) {
//      imap_mail_move($imap,$email_number,"Imported");
    //  imap_setflag_full($imap,$email_number,"\\Seen \\Flagged");
//    }
    //archive Niffy's forward so it doesn't get imported twice
//  } else {
//    $emails_fw = $emails_niffy_forward;
    //there aren't any hachwims_gnr emails to archive so do nothing
//  }
  $all_data_for_database['fw'] = get_data_from_emails($emails_fw,$imap,'FW_Auto_');
}
// THIS IS WHERE YOU INTEGRATE WA EMAIL DOWNLOAD
if($download_inc['wa']){
   $emails_wa = imap_search($imap, 'UNSEEN SUBJECT "Daily Pumping Report" FROM "@usace.army.mil"'); //one used when automated emails were working
   $all_data_for_database['wa'] = get_data_from_emails($emails_wa,$imap,'WA_Auto_');
}
if($download_inc['wa_monthly']){
//  $date = date("M Y");
  $date = date("F Y", strtotime(" -1 months"));
//  $date = date("Y");
//  echo $date;
  //$emails_wa_monthly = imap_search($imap, 'UNSEEN SUBJECT "'.$date.' Potomac Withdrawals (UNCLASSIFIED)" FROM "sahmed@icprb.org"'); //one used when automated emails were working
  $emails_wa_monthly = imap_search($imap, 'UNSEEN SUBJECT "Fwd: July 2018 Potomac Withdrawals (UNCLASSIFIED)" FROM "sahmed@icprb.org"'); //one used when automated emails were working
  $all_data_for_database['wa_monthly'] = get_data_from_emails($emails_wa_monthly,$imap,'WA_Auto_');
}

/* close the connection */
imap_close($imap);
