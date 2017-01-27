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
        $checksum = $this->updateChecksum($userId);
        return [ 'CHECKSUM' => $checksum];
    }
    
    
    
    
    public function updateChecksum($userId) {
        $salt = mcrypt_create_iv(50);
        $salt .= time();
        $checksum = hash('sha1', $salt);
        $result = $this->commonModel->updateChecksum($userId, $checksum);
        if(!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '41';
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
}
