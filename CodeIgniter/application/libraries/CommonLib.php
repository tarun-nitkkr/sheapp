<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * Library for all common functionalities and APIs
 * @author tarun
 */
class CommonLib {
    
    private $commonModel;
    
    function __construct()
	{        
        $this->CI =& get_instance();
		$this->CI->load->model('CommonModel');
        $this->commonModel = new CommonModel();
	}
    
    
    public function loginUser($username, $password) {
        $pass_hash = hash('sha256', $password);
        $userId = $this->commonModel->authenticateUser($username, $pass_hash);
        if($userId == 0) {
            global $ERROR_CODE;
            $ERROR_CODE = '40';
            throw new Exception("Authentication Failure: Credentials mismatch");
        }
        if($userId == -1) {
            global $ERROR_CODE;
            $ERROR_CODE = '41';
            throw new Exception("Authentication Failure: No such username found");
        }
        global $USER_ID;
        $USER_ID = $userId;
        $checksum = $this->checkChecksum($userId);
        if(!$checksum) {
            $checksum = $this->updateChecksum($userId);
        }        
        return [ 'CHECKSUM' => $checksum];
    }
    
    
    public function checkChecksum($userId) {
        $checksum = $this->commonModel->checkChecksum($userId);
        return $checksum;
    }
    
    
    
    
    public function updateChecksum($userId) {
        $salt = uniqid(mt_rand(), true);
        $salt .= time();
        $checksum = hash('sha1', $salt);
        $result = $this->commonModel->updateChecksum($userId, $checksum);
        if(!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '42';
            throw new Exception("Checksum Updation Failed");
        }
        return $checksum;
    }
    
    
    
    public function authenticateChecksum($checksum) {
        $userId = $this->commonModel->authenticateChecksum($checksum);
        if(!$userId) {            
            $GLOBALS['ERROR_CODE'] = '12';
            throw new Exception("Checksum Authentication Failed");
        }
        global $USER_ID;
        $USER_ID = $userId;
    }
    
    
    
    public function getFoodMenu() {
        $allData = $this->commonModel->getFoodMenuFromDb();
        //var_dump($allData);exit;
        if(!$allData) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $resultData = [];
        foreach($allData as $tupple) {
            if($tupple['IS_DEFAULT'] == 'Y' && !isset($resultData[$tupple['DAY']][$tupple['MEAL']])) {
                $resultData[$tupple['DAY']][$tupple['MEAL']] = $tupple;
            } else if($tupple['IS_DEFAULT'] != 'Y'){
                $resultData[$tupple['DAY']][$tupple['MEAL']] = $tupple;
            }
        }
        return $resultData;
    }
    
    
    public function logoutUser() {
        global $USER_ID;        
        $result = $this->commonModel->invalidateChecksum($USER_ID);
        if(!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '80';
            throw new Exception("DB Updation failed!");
        }
        return [];
    }
    
    
    public function updateFoodMenu($date, $dishName, $day, $meal, $isDefault) {
        //validateInput
        $dayValArr = [
            'MONDAY',
            'TUESDAY',
            'WEDNESDAY',
            'THURSDAY',
            'FRIDAY',
            'SATURDAY',
            'SUNDAY'
        ];
        
        $mealValArr = [
            'BREAKFAST',
            'DINNER'
        ];
        
        $isDefaultValArr = ['Y', 'N'];       
        
        
        
        if(!in_array($meal, $mealValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: MEAL");
        }
        if(!in_array($day, $dayValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DAY");
        }
        if(!in_array($isDefault, $isDefaultValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: ISDEFAULT");
        }
        if(!preg_match('/^20[0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $date)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DATE");
        }
        
        //validate input--done
        
        $result = $this->commonModel->updateFoodMenuInDb($dishName, $date, $day, $meal, $isDefault);
        if(!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '80';
            throw new Exception("DB Updation failed!");
        }
        return [];
    }
    
    
    
    public function getAllLists() {
        $data = $this->commonModel->getAllListsFromDb();
        if(!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $result = [];
        foreach ($data as $tupple) {
            if(!isset($result[$tupple['LIST_ID']])) {
                $result[$tupple['LIST_ID']]['TITLE'] = $tupple['TITLE'];
                $result[$tupple['LIST_ID']]['LIST_ID'] = $tupple['LIST_ID'];
                $result[$tupple['LIST_ID']]['LIST_ITEMS'][$tupple['ITEM']] = $tupple;
                $result[$tupple['LIST_ID']]['LAST_MODIFIED'] = $tupple['LAST_MODIFIED'];
                $result[$tupple['LIST_ID']]['LAST_MODIFIED_BY'] = $tupple['IS_BOUGHT'] == 'Y' ? $tupple['BOUGHT_BY_NAME'] : $tupple['REQUESTED_BY_NAME'];
                continue;
                
            }
            $result[$tupple['LIST_ID']]['TITLE'] = $tupple['TITLE'];
            $result[$tupple['LIST_ID']]['LIST_ID'] = $tupple['LIST_ID'];
            $result[$tupple['LIST_ID']]['LIST_ITEMS'][$tupple['ITEM']] = $tupple;
        }        
        
        return $result;
    }
    
    
    public function getFilteredLists() {
        $data = $this->commonModel->getAllListsFromDb();
        if(!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $result = [];
        foreach($data as $tupple) {
            $result[$tupple['TYPE']][] = $tupple;
        }
        //var_dump($result);exit;
        return $result;
    }
      
    
    
    public function updateList($list) {
        if(!isset($list['ID'])) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid JSON");
        }
        if($list['ID'] == -1) {
            
        }
    }
    
    
}
