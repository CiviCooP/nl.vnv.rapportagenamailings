<?php

require_once 'rapportagenamailings.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function rapportagenamailings_civicrm_config(&$config) {
  _rapportagenamailings_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function rapportagenamailings_civicrm_xmlMenu(&$files) {
  _rapportagenamailings_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function rapportagenamailings_civicrm_install() {
  return _rapportagenamailings_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function rapportagenamailings_civicrm_uninstall() {
  return _rapportagenamailings_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function rapportagenamailings_civicrm_enable() {
  return _rapportagenamailings_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function rapportagenamailings_civicrm_disable() {
  return _rapportagenamailings_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function rapportagenamailings_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _rapportagenamailings_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function rapportagenamailings_civicrm_managed(&$entities) {
  return _rapportagenamailings_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function rapportagenamailings_civicrm_caseTypes(&$caseTypes) {
  _rapportagenamailings_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function rapportagenamailings_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _rapportagenamailings_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * BOSW1506013 vnv.nl - rapportage na mailings
 * They want a email with information when a scheduled mailing is send, this
 * is done through the hook_civicrm_post with the objectname CRM_Mailing_DAO_Spool, with
 * this the objectId must exists in the civicrm_mailing_job and is_test must be 0 and 
 * status must be Complete, then it must exists in the civicrm_mailing and is_completed is 1 and
 * is_archived is 0 (this meens that it is a scheduled mailing and not a draft)
 * 
 * @param type $op
 * @param type $objectName
 * @param type $objectId
 * @param type $objectRef
 * @throws Exception
 */
function rapportagenamailings_civicrm_post( $op, $objectName, $objectId, &$objectRef ){
  /*if('CRM_Mailing_DAO_Spool' == $objectName){
    $myfile = fopen('/var/tmp/CRM_Mailing_DAO_Spool_' . date('Y-m-d H:i:s') . '.txt', 'w');
    
    $txt = '$op:' . $op . PHP_EOL;
    fwrite($myfile, $txt);
    
    $txt = '$objectName:' . $objectName . PHP_EOL;
    fwrite($myfile, $txt);
    
    $txt = '$objectId:' . $objectId . PHP_EOL;
    fwrite($myfile, $txt);
    
    ob_start();
    echo '$objectRef: ';
    echo('<pre>');
    print_r($objectRef);
    echo('</pre>') . PHP_EOL;
    $txt = ob_get_contents();
    ob_end_clean();
    fwrite($myfile, $txt);
        
    // get mailing job
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'id' => $objectId,
        //'status' => 'Complete',
        'is_test' => 0,
      );
      $result = civicrm_api('MailingJob', 'getsingle', $params);
      
      ob_start();
      echo 'MailingJob: ';
      echo('<pre>');
      print_r($result);
      echo('</pre>') . PHP_EOL;
      $txt = ob_get_contents();
      ob_end_clean();
      fwrite($myfile, $txt);
      
      if(isset($result['is_error']) and !$result['is_error']){ // if there is no error
        
        // get mailing
        try {
          $params = array(
            'version' => 3,
            'sequential' => 1,
            'id' => $result['mailing_id'],
            'is_completed' => 1,
            'is_archived' => 0,
          );
          $result = civicrm_api('Mailing', 'getsingle', $params);

          ob_start();
          echo 'Mailing: ';
          echo('<pre>');
          print_r($result);
          echo('</pre>') . PHP_EOL;
          $txt = ob_get_contents();
          ob_end_clean();
          fwrite($myfile, $txt);
          
          if(isset($result['is_error']) and !$result['is_error']){ // if there is no error
            
          }

        } catch (CiviCRM_API3_Exception $ex) {
          throw new Exception('Could not find mailing, '
            . 'error from Mailing getsingle: '.$ex->getMessage());
        }
      }
      
    } catch (CiviCRM_API3_Exception $ex) {
      $error = true;
      throw new Exception('Could not find mailing job, '
        . 'error from MailingJob getsingle: '.$ex->getMessage());
    }
        
    fclose($myfile);
    
    //CRM_Utils_System::civiExit();
  }*/
}