<?php
//--------------------BEGIN CONFIGURE CODE -----------------//
// configure form elements
function webform_autodatadownload_admin($form, $form_state) {
  $form = array();
  $form['webform_autodatadownload_fwinc'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fairfax Water (FW)'),
    '#default_value' => variable_get('webform_autodatadownload_fwinc', 0),
    '#description' => t("Includes FW in the automated data update."),
  );
  $form['webform_autodatadownload_wsscinc'] = array(
    '#type' => 'checkbox',
    '#title' => t('Washington Subburban Sanitary Commission (WSSC)'),
    '#default_value' => variable_get('webform_autodatadownload_wsscinc', 0),
    '#description' => t("Includes WSSC in the automated data update."),
  );
  $form['webform_autodatadownload_wainc'] = array(
    '#type' => 'checkbox',
    '#title' => t('Washington Aqueduct (WA)'),
    '#default_value' => variable_get('webform_autodatadownload_wainc', 0),
    '#description' => t("Includes WA in the automated data update for pumping report emailed near daily."),
  );
  $form['webform_autodatadownload_wamonthlyinc'] = array(
    '#type' => 'checkbox',
    '#title' => t('Washington Aqueduct (WA)'),
    '#default_value' => variable_get('webform_autodatadownload_wamonthlyinc', 0),
    '#description' => t("Includes WA in the automated data update for withdrawal report emailed monthly."),
  );
  //$form['webform_autodatadownload_last_download'] = array(
  //  '#type' => 'date',
  //  '#title' => t('Last Download'),
  //  '#default_value' => variable_get('webform_autodatadownload_last_download',array(year => date('Y',strtotime( 'now' )), month =>date('m',strtotime( 'now' )), day =>date('d',strtotime( 'now' )))),
  //);
  return system_settings_form($form);
}
// configure form link
function webform_autodatadownload_menu() {
  $items = array();
  $items['admin/config/content/webform_autodatadownload'] = array(
    'title' => 'Webform automate data download settings',
    'page callback' => 'drupal_get_form',
    'page arguments' => array('webform_autodatadownload_admin',1),
  //  'access callback' => 'webform_automate_fw_node_update_access',
    'access arguments' => array('administer webform_autodatadownload settings'),
    'type' => MENU_NORMAL_ITEM,
   );
  return $items;
}
//--------------------END CONFIGURE CODE -----------------//
