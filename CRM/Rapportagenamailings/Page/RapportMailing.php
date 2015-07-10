<?php

require_once 'CRM/Core/Page.php';

class CRM_Rapportagenamailings_Page_RapportMailing extends CRM_Core_Page {
  public $_mailing_id;

  /**
   * Get BAO Name
   *
   * @return string Classname of BAO
   */
  function getBAOName() {
    return 'CRM_Mailing_BAO_Mailing';
  }

  function &links() {
    return CRM_Core_DAO::$_nullObject;
  }

  function editForm() {
    return NULL;
  }

  function editName() {
    return 'CiviMail Report';
  }

  /**
   * Get user context.
   *
   * @return string user context.
   */
  function userContext($mode = NULL) {
    return 'civicrm/mailing/report';
  }

  function userContextParams($mode = NULL) {
    return 'reset=1&mid=' . $this->_mailing_id;
  }

  function run() {
    $this->_mailing_id = CRM_Utils_Request::retrieve('mid', 'Positive', $this);

    // check that the user has permission to access mailing id
    CRM_Mailing_BAO_Mailing::checkPermission($this->_mailing_id);

    $report = CRM_Mailing_BAO_Mailing::report($this->_mailing_id);

    //get contents of mailing
    CRM_Mailing_BAO_Mailing::getMailingContent($report, $this);

    //assign backurl
    /*$context = CRM_Utils_Request::retrieve('context', 'String', $this);
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this);

    if ($context == 'activitySelector') {
      $backUrl = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$cid}&selectedChild=activity");
      $backUrlTitle = ts('Back to Activities');
    }
    elseif ($context == 'activity') {
      $atype = CRM_Utils_Request::retrieve('atype', 'Positive', $this);
      $aid = CRM_Utils_Request::retrieve('aid', 'Positive', $this);

      $backUrl = CRM_Utils_System::url('civicrm/activity/view',
        "atype={$atype}&action=view&reset=1&id={$aid}&cid={$cid}&context=activity"
      );
      $backUrlTitle = ts('Back to Activity');
    }
    elseif ($context == 'mailing') {
      $backUrl = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$cid}&selectedChild=mailing");
      $backUrlTitle = ts('Back to Mailing');
    }
    else {
      $backUrl = CRM_Utils_System::url('civicrm/mailing', 'reset=1');
      $backUrlTitle = ts('Back to CiviMail');
    }
    $this->assign('backUrl', $backUrl);
    $this->assign('backUrlTitle', $backUrlTitle);*/

    $this->assign('report', $report);
    CRM_Utils_System::setTitle(ts('CiviMail Report: %1',
        array(1 => $report['mailing']['name'])
      ));

    return CRM_Core_Page::run();
  }
}
