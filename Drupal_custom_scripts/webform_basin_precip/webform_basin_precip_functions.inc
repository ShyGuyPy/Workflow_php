<?php

function submit_data_array($nid,$data){
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
