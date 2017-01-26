<?php

defined('BASEPATH') OR exit('No direct script access allowed');


//global variables
$ERROR_CODE = '';
$USER_ID = '';

/**
 * Main Controller to handle most of the API requests
 * @author tarun
 */
class Api extends CI_Controller {

    private $commonLib;
    private $apiResonse;

    function __construct() {
        parent::__construct();
        //header('Content-Type: application/json');
        $this->load->helper('security');
        $this->load->library('CommonLib');
        $this->load->library('ApiResponse');
        $this->commonLib = new CommonLib();
        $this->apiResonse = new ApiResponse();


        //authenticate every API except LOGIN api
        try {
            if (!(isset($_REQUEST['USERNAME']) && isset($_REQUEST['PASSWORD']) && strpos($_SERVER['REQUEST_URI'], 'Api/login') )) {
                $this->authenticate();
            }
        } catch (Exception $exc) {            
            $this->handleException($exc);
        }
    }
    
    
    /**
     * To handle and respond corresponding to a Exception
     * @global string $ERROR_CODE
     * @global string $USER_ID
     * @param Exception $exc
     */
    private function handleException($exc) {
        global $ERROR_CODE, $USER_ID;
        if($ERROR_CODE > 100) {
            echo $this->apiResonse->getFalseJSONResponse($USER_ID, $exc->getMessage());
        }else {
            echo $this->apiResonse->getErrorJSONResponse($USER_ID, $ERROR_CODE, $exc->getMessage());
        }        
    }
    
    
    
    /**
     * Get XSS cleaned input from $_REQUEST
     * @global string $ERROR_CODE
     * @param string $key
     * @return string Input values xss cleared
     * @throws Exception
     */
    private function getXssCleanedInput($key) {
        if (!isset($_REQUEST[$key])) {
            global $ERROR_CODE;
            $ERROR_CODE = '10';
            throw new Exception("INPUT: $key not found");
        }
        return $this->security->xss_clean($_REQUEST[$key]);
    }

    /**
     * Authenticates user based on checksum
     */
    private function authenticate() {
        $checksum = $this->getXssCleanedInput('CHECKSUM');
        $this->commonLib->authenticateChecksum($checksum);
    }
    
    /**
     * API to login the user
     * RETURNS checksum
     */
    public function login() {
        try {
            $username = $this->getXssCleanedInput('USERNAME');
            $password = $this->getXssCleanedInput('PASSWORD');
            $this->commonLib->loginUser($username, $password);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    
    /**
     * TEST API
     */
    public function test() {
        //echo "This is test string";
        $username = $_POST['USER_NAME'];
        $password = $_POST['PASSWORD'];

        $data = [
            'STATUS' => 'OK',
            'ERRORCODE' => '',
            'MSG' => "THIS IS SAMPLE JSON",
            'DATA' => [
                'USERNAME' => $username,
                'PASSWORD' => md5($password)
            ],
            'USERID' => '-1'
        ];
        echo json_encode($data);
    }

}
