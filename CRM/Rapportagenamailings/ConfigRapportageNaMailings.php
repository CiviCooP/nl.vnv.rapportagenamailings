<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Jan-Derek (CiviCooP) <j.vos@bosqom.nl>
 */
class CRM_Rapportagenamailings_ConfigRapportageNaMailings {
  
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  
  // Setting
  protected $settings = array();

  // Mailings
  protected $mailings = array();
  protected $mailingStatus = array(
    //'Scheduled' => 1, 
    'Complete' => 1, 
    //'Running' => 1, 
    'Canceled' => 1
  );

  // txt file
  protected $txtDir = '';
  protected $txtFilePath = 'RapportageNaMailings.txt';
  protected $txtFilePathFull = '';
  protected $txtFileExists = false;
  protected $txtData = array();


  /**
   * Constructor
   */
  function __construct() {
    // Settings
    $this->setSettings();
    
    // Mailings
    $this->setMailings();
    
    // txt
    $this->setTxtDir();
    $this->setTxtFilePathFull();
    $this->setTxtFileExists();
    $this->setTxtData();
  }
  
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Rapportagenamailings_ConfigRapportageNaMailings();
    }
    return self::$_singleton;
  }
  
  // Settings
  protected function setSettings(){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
      );
      $this->settings = civicrm_api('Setting', 'getsingle', $params);
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find setting, '
        . 'error from Setting getsingle: '.$ex->getMessage());
    }
  }
  
  public function getSettings(){
    return $this->settings;
  }
  
  public function getSetting($name){
    return $this->settings[$name];
  }
  
  protected function setMailings(){
    /*$query = "SELECT civicrm_mailing.id, civicrm_mailing_job.status
      FROM  civicrm_mailing 
      LEFT JOIN  civicrm_mailing_job ON ( civicrm_mailing.id = civicrm_mailing_job.mailing_id AND civicrm_mailing_job.is_test = 0 AND civicrm_mailing_job.parent_id IS NULL ) 
      LEFT JOIN  civicrm_contact createdContact ON ( civicrm_mailing.created_id = createdContact.id ) 
      LEFT JOIN  civicrm_contact scheduledContact ON ( civicrm_mailing.scheduled_id = scheduledContact.id )     
      WHERE ( 1 ) 
      AND civicrm_mailing.sms_provider_id IS NULL 
      AND (civicrm_mailing_job.status IN ('Complete', 'Canceled')) 
      AND (civicrm_mailing.is_archived IS NULL OR civicrm_mailing.is_archived = 0)";
    }*/
    
    try {
            
      $query = "SELECT civicrm_mailing.id, civicrm_mailing_job.status
        FROM  civicrm_mailing 
        LEFT JOIN  civicrm_mailing_job ON ( civicrm_mailing.id = civicrm_mailing_job.mailing_id AND civicrm_mailing_job.is_test = 0 AND civicrm_mailing_job.parent_id IS NULL ) 
        LEFT JOIN  civicrm_contact createdContact ON ( civicrm_mailing.created_id = createdContact.id ) 
        LEFT JOIN  civicrm_contact scheduledContact ON ( civicrm_mailing.scheduled_id = scheduledContact.id )     
        WHERE ( 1 ) 
        AND civicrm_mailing.sms_provider_id IS NULL 
        AND (civicrm_mailing_job.status IN ('" . implode("', '", array_keys($this->mailingStatus)) . "')) 
        AND (civicrm_mailing.is_archived IS NULL OR civicrm_mailing.is_archived = 0)";
      
      $dao = CRM_Core_DAO::executeQuery($query);
            
      while ($dao->fetch()) {
        $this->mailings[$dao->id] = (array) $dao;
      }
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find contact, '
        . 'error from CRM_Core_DAO: '.$ex->getMessage());
    }
  }


  public function getMailings() {
    return $this->mailings;
  }
  
  // txt extension dir
  protected function setTxtDir(){
    $this->txtDir = $this->settings['customFileUploadDir'];
  }
  
  public function getTxtDir(){
    return $this->txtDir;
  }

  // txt file path full
  protected function setTxtFilePathFull(){
    $this->txtFilePathFull = $this->txtDir . $this->txtFilePath;
  }

  public function getTxtFilePathFull(){
    return $this->txtFilePathFull;
  }

  // get txt file path
  public function getTxtFilePath(){
    return $this->txtFilePath;
  } 

  // set txt file exists
  protected function setTxtFileExists(){
    if(file_exists($this->txtFilePathFull)){
      $this->txtFileExists = true;
    }else {
      $this->txtFileExists = false;
    }
  }
  
  public function getTxtFileExists(){
    return $this->txtFileExists;
  }

  // set txt data
  protected function setTxtData(){
    if($this->txtFileExists){
      $file = fopen($this->txtFilePathFull, "r");
      if(!$file){
        echo('Extension nl.vnv.rapportagenamailings. Could not open ' . $this->txtFilePathFull);
        return false;
      }
      $data = fread($file, filesize($this->txtFilePathFull));
      fclose($file);
    }
    $this->txtData = json_decode($data, true);
  }
  
  public function getTxtData() {
    return $this->txtData;
  }
  
  // write txt data
  public function writeTxtData($data){
    $file = fopen($this->txtFilePathFull, "w");
    if(!$file){
      echo('Extension nl.vnv.rapportagenamailings. Could not open ' . $this->txtFilePathFull);
      return false;
    }
    fwrite($file, json_encode($data));
    fclose($file);
  }
}