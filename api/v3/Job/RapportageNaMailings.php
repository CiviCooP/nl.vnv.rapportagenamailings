<?php

/**
 * Job.RapportageNaMailings API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_job_rapportagenamailings_spec(&$spec) {
  
}

/**
 * Job.RapportageNaMailings API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_job_rapportagenamailings($params) {  
    
  // Check if the Job is run directly, if so the GET['job'] must exists and
  // the GET['job'] must be RapportageNaMailings. If i do not check it it will 
  // run always even if the job is disabled in the scheduled jobs  
  //if('RapportageNaMailings' == CRM_Utils_Request::retrieve('job', 'String')){ // we dicide to run this job always   
    $configRapportageNaMailings = CRM_Rapportagenamailings_ConfigRapportageNaMailings::singleton();   
        
    // check if file exists
    // If the file does not exists it is probably the first time, so
    // we have to create the file and set all the mailing id on done 
    if(!$configRapportageNaMailings->getTxtFileExists()){      
      $data = array();
      foreach($configRapportageNaMailings->getMailings() as $key => $mailing){
        $data[$mailing['id']] = array('id' => $mailing['id'], 'status' => 'done');
      }
      
      $configRapportageNaMailings->writeTxtData($data);
      
      return true;
    }
    
    // Loop trough the mailings and check in the .txt file if
    // we already have done that mailing
    $data = $configRapportageNaMailings->getTxtData(); 
        
    foreach($configRapportageNaMailings->getMailings() as $key => $mailing){
      if(!isset($data[$mailing['id']]['status']) or 'done' != $data[$mailing['id']]['status']){ // if we didden do this mailing
        // we only get the mailing who are completed so we do not have to check this
        civicrm_api3_job_rapportagenamailings_mail($mailing['id']); // send mail

        // set the status on done
        $data[$mailing['id']] = array('id' => $mailing['id'], 'status' => 'done');
      }
    }
    
    $configRapportageNaMailings->writeTxtData($data);
  //}
    
  return true;
}

/**
 * Send a email to ?? with the mailing report of one mailing
 * 
 * @param type $mailing_id
 */
function civicrm_api3_job_rapportagenamailings_mail($mailing_id){
  // create a new Cor Page
  $page = new CRM_Core_Page();
  $page->_mailing_id = $mailing_id;

  // create a new template
  $template = CRM_Core_Smarty::singleton();

  // from CRM/Mailing/Page/Report.php
  // check that the user has permission to access mailing id
  CRM_Mailing_BAO_Mailing::checkPermission($mailing_id);

  $report = CRM_Mailing_BAO_Mailing::report($mailing_id);

  //get contents of mailing
  CRM_Mailing_BAO_Mailing::getMailingContent($report, $page);

  $subject = ts('CiviMail Report: %1', array(1 => $report['mailing']['name']));
  
  $template->assign('report', $report);

  // from CRM/Core/page.php
  // only print
  $template->assign('tplFile', 'CRM/Rapportagenamailings/Page/RapportMailing.tpl');

  $content = $template->fetch('CRM/common/print.tpl');

  CRM_Utils_System::appendTPLFile('CRM/Rapportagenamailings/Page/RapportMailing.tpl', $content, $page->overrideExtraTemplateFileName());

  //its time to call the hook.
  CRM_Utils_Hook::alterContent($content, 'page', 'CRM/Rapportagenamailings/Page/RapportMailing.tpl', $page);

  //echo $content;

  // send mail
  $params = array(
    'from' => 'j.vos@bosqom.nl', // complete from envelope
    'toName' => 'Jan-Derek Vos', // name of person to send email
    'toEmail' => 'j.vos@bosqom.nl', // email address to send to
    'subject' => $subject, // subject of the email
    'text' => $subject, // text of the message
    'html' => $content, // html version of the message
    'replyTo' => 'j.vos@bosoqm.nl', // reply-to header in the email
  );

  CRM_Utils_Mail::send($params);
}