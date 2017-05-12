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
        header('Content-Type: application/json');
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
        if ($ERROR_CODE > 100) {
            echo $this->apiResonse->getFalseJSONResponse($USER_ID, $exc->getMessage());
        } else {
            echo $this->apiResonse->getErrorJSONResponse($USER_ID, $ERROR_CODE, $exc->getMessage());
        }
        exit(0);
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
     * To echo/return JSON response in case of API success
     * @global string $USER_ID
     * @param string $data
     */
    private function sendOkResponse($data) {
        global $USER_ID;
        echo $this->apiResonse->getOkJSONResponse($USER_ID, $data);
        exit(0);
    }

    /**
     * API to login the user
     * RETURNS checksum
     */
    public function login() {
        try {
            $username = $this->getXssCleanedInput('USERNAME');
            $password = $this->getXssCleanedInput('PASSWORD');
            $data = $this->commonLib->loginUser($username, $password);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    /**
     * API to logout the user based on the CHECKSUM
     */
    public function logout() {
        try {
            $data = $this->commonLib->logoutUser();
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    /**
     * API to get eligible food menu for current week i.e. (MONDAY to SUNDAY)
     * PARAMS: none
     * RETURNS:- dataJSON
     */
    public function getFoodMenu() {
        try {
            $data = $this->commonLib->getFoodMenu();
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    /**
     * API to update food menu
     * PARAMS: DISHNAME, DATE, DAY, ISDEFAULT, MEAL
     * RETURN: empty response
     */
    public function updateFoodMenu() {
        try {
            $dishName = $this->getXssCleanedInput('DISHNAME');
            $date = $this->getXssCleanedInput('DATE');
            $isDefault = $this->getXssCleanedInput('ISDEFAULT');
            $meal = $this->getXssCleanedInput('MEAL');
            $day = $this->getXssCleanedInput('DAY');
            $data = $this->commonLib->updateFoodMenu($date, $dishName, $day, $meal, $isDefault);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    /**
     * API to attach a Shopping List to a food menu item
     * PARAMS: ListId, foodMenuId
     * RETURNS: OK response
     */
    public function attachListToFood() {
       try {
            $foodId = $this->getXssCleanedInput('FOOD_ID');
            $listId = $this->getXssCleanedInput('LIST_ID');
            $data = $this->commonLib->attachListToFood($foodId, $listId);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        } 
    }

    /**
     * API to get all Lists i.e. even completed lists
     * PARAMS: none
     * RETURNS dataJSON
     */
    public function getAllLists() {
        try {
            $data = $this->commonLib->getAllLists();
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    /**
     * API to get filtered lists i.e. ['STAPLES' : [array of items], 'UTILITIES': [array of items],...]
     * PARAMS:- none
     */
    public function getFilteredLists() {
        try {
            $data = $this->commonLib->getFilteredLists();
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }

    public function updateList() {
        try {
            $listJson = $this->getXssCleanedInput('LIST_JSON');
            $listArr = json_decode(stripslashes($listJson), true);
            $data = $this->commonLib->updateList($listArr);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    public function deleteList() {
        try {
            $listId = $this->getXssCleanedInput('LIST_ID');
            $listTitle = $this->getXssCleanedInput('LIST_TITLE');            
            $data = $this->commonLib->deleteList($listId, $listTitle);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    
    public function setNotificationToken() {
        try {
            $token = $this->getXssCleanedInput('TOKEN');
            $data = $this->commonLib->updateNotificationToken($token);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    
    
    public function getAbsentDays() {
        try {
            $month = $this->getXssCleanedInput('MONTH');
            $empId = $this->getXssCleanedInput('EMP_ID');
            $data = $this->commonLib->getAbsentDays($empId, $month);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    
    
    public function getEmployeeDetails() {
        try {            
            $data = $this->commonLib->getEmployeeDetails();
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    
    public function removeAbsentEntry() {
        try {
            $id = $this->getXssCleanedInput('ID');
            $data = $this->commonLib->removeAbsentEntry($id);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    
    public function reportAbsentEntry() {
        try {
            $date = $this->getXssCleanedInput('DATE');
            $shift = $this->getXssCleanedInput('SHIFT');
            $empId = $this->getXssCleanedInput('EMP_ID');
            $reason = $this->getXssCleanedInput('REASON');
            $data = $this->commonLib->reportAbsent($date, $shift, $empId, $reason);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }    
        
    public function reportAbsentRange() {
        try {
            $inputData = [];
            $inputData['FROM_DATE'] = $this->getXssCleanedInput('FROM_DATE');
            $inputData['FROM_SHIFT'] = $this->getXssCleanedInput('FROM_SHIFT');
            $inputData['TO_DATE'] = $this->getXssCleanedInput('TO_DATE');
            $inputData['TO_SHIFT'] = $this->getXssCleanedInput('TO_SHIFT');
            $empId = $this->getXssCleanedInput('EMP_ID');
            $reason = $this->getXssCleanedInput('REASON');
            $data = $this->commonLib->reportAbsentRange($inputData, $empId, $reason);
            $this->sendOkResponse($data);
        } catch (Exception $exc) {
            $this->handleException($exc);
        }
    }
    
    /**
     * TEST API
     */
    public function test() {
        $this->sendOkResponse(array());
    }

//    public function dumpKey() {
//        $key = $_REQUEST['KEY'];
//        $file = fopen('key_dump.txt', "a");
//        fwrite($file, "Time:-" . date("Y-m-d h:i:sa") . "#### KEY:" . $key);
//        fclose($file);
//        $this->sendOkResponse(['dir' => getcwd()]);
//    }

}
