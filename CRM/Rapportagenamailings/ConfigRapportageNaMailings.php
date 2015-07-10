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
  
  // Mailings
  protected $mailings = array();
  protected $mailingStatus = array(
    //'Scheduled' => 1, 
    'Complete' => 1, 
    //'Running' => 1, 
    'Canceled' => 1
  );

  // txt file
  protected $txtFilePath = 'RapportageNaMailings.txt';
  protected $txtFileExists = false;
  protected $txtData = array();


  /**
   * Constructor
   */
  function __construct() {
    // Mailings
    $this->setMailings();
    
    // txt
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
  
  // Mailings
  /*protected function setMailings(){  
    // Initialise our pagesize and offset
    $page_size = 100;
    $offset = 0;
    
    try{

      // Start out loop
      do {
        $params = array(
          'version' => 3,
          'sequential' => 1,
          'is_completed' => 1,
          'is_archived' => 0,
          //'filter.scheduled_date' => date('Y-m-d') . ' 00:00:00',
          'options' => array(
            'limit'  => $page_size,
            'offset' => $offset
          )
        );
        $result = civicrm_api('Mailing', 'get', $params);

        // do something with the results if we didn't error
        if ($result['is_error'] == 0) {
          foreach ($result['values'] as $value) {
            $this->mailings[$value['id']] = $value;
          }
        }

        // Increment the offset by the page size
        $offset = $offset + $page_size;

      } while ($result['count'] >= $page_size); // Check if we still need to fetch results
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find contact, '
        . 'error from CRM_Core_DAO: '.$ex->getMessage());
    }
  }*/
  
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
    
    echo('$query: ' . $query);
    
    $dao = CRM_Core_DAO::executeQuery($query);
    while($dao->fetch){
      echo('<pre>');
      print_r($dao);
      echo('</pre>');
    }*/
    
    try {
      //$query = 'SELECT * FROM civicrm_contact WHERE id= %1';
      //$params = array(1 => array($this->contact_id, 'Positive'));
      //$dao = CRM_Core_DAO::executeQuery($query, $params);
            
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
  
  // txt
  // 
  public function getTxtFilePath(){
    return $this->txtFilePath;
  }

  // set txt file exists
  protected function setTxtFileExists(){
    if(file_exists($this->txtFilePath)){
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
      $file = fopen($this->txtFilePath, "r");
      $data = fread($file, filesize($this->txtFilePath));
      fclose($file);
    }
    $this->txtData = json_decode($data, true);
  }
  
  public function getTxtData() {
    return $this->txtData;
  }
  
  // write txt data
  public function writeTxtData($data){
    $file = fopen($this->txtFilePath, "w");
    fwrite($file, json_encode($data));
    fclose($file);
  }
}