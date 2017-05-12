<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library for all common functionalities and APIs
 * @author tarun
 */
class CommonLib {

    private $commonModel;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model('CommonModel');
        $this->commonModel = new CommonModel();
    }

    public function loginUser($username, $password) {
        $pass_hash = hash('sha256', $password);
        $userId = $this->commonModel->authenticateUser($username, $pass_hash);
        if ($userId == 0) {
            global $ERROR_CODE;
            $ERROR_CODE = '40';
            throw new Exception("Authentication Failure: Credentials mismatch");
        }
        if ($userId == -1) {
            global $ERROR_CODE;
            $ERROR_CODE = '41';
            throw new Exception("Authentication Failure: No such username found");
        }
        global $USER_ID;
        $USER_ID = $userId;
        $checksum = $this->checkChecksum($userId);
        if (!$checksum) {
            $checksum = $this->updateChecksum($userId);
        }
        return ['CHECKSUM' => $checksum];
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
        if (!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '42';
            throw new Exception("Checksum Updation Failed");
        }
        return $checksum;
    }

    public function authenticateChecksum($checksum) {
        $userId = $this->commonModel->authenticateChecksum($checksum);
        if (!$userId) {
            $GLOBALS['ERROR_CODE'] = '12';
            throw new Exception("Checksum Authentication Failed");
        }
        global $USER_ID;
        $USER_ID = $userId;
    }

    public function getFoodMenu() {
        $allData = $this->commonModel->getFoodMenuFromDb();
        //var_dump($allData);exit;
        if (!$allData) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $resultData = [];
        foreach ($allData as $tupple) {
            if ($tupple['IS_DEFAULT'] == 'Y' && !isset($resultData[$tupple['DAY']][$tupple['MEAL']])) {
                $resultData[$tupple['DAY']][$tupple['MEAL']] = $tupple;
            } else if ($tupple['IS_DEFAULT'] != 'Y') {
                $resultData[$tupple['DAY']][$tupple['MEAL']] = $tupple;
            }
        }
        return $resultData;
    }

    public function logoutUser() {
        global $USER_ID;
        $result = $this->commonModel->invalidateChecksum($USER_ID);
        if (!$result) {
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



        if (!in_array($meal, $mealValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: MEAL");
        }
        if (!in_array($day, $dayValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DAY");
        }
        if (!in_array($isDefault, $isDefaultValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: ISDEFAULT");
        }
        if (!preg_match('/^20[0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $date)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DATE");
        }

        //validate input--done

        $result = $this->commonModel->updateFoodMenuInDb($dishName, $date, $day, $meal, $isDefault);
        //var_dump($result);exit;
        if (!$result) {
            global $ERROR_CODE;
            $ERROR_CODE = '80';
            throw new Exception("DB Updation failed!");
        }
        
        global $USER_ID;
        //NOTIFICATION
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),
            'MEAL' => $meal,
            'DATE' => $date,
            'DAY' => $day,
            'DISHNAME' => $dishName,
            'IS_DEFAULT' => $isDefault
        ];
        $this->generateAndSendNotificationMsg('FOOD_MENU', $notificationData);
        
        return ['INSERTION_ID' => $result];
    }
    
    
    public function attachListToFood($foodId, $listId) {
        $updateSet = [
            'LIST_ID' => $listId
        ];
        $status = $this->commonModel->updateFoodMenuTable($foodId, $updateSet);
        if(!$status) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        
        return [];
    }
    

    public function getAllLists() {
        $data = $this->commonModel->getAllListsFromDb();
        if (!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $result = [];
        foreach ($data as $tupple) {
            if (!isset($result[$tupple['LIST_ID']])) {
                $result[$tupple['LIST_ID']]['TITLE'] = $tupple['TITLE'];
                $result[$tupple['LIST_ID']]['LIST_ID'] = $tupple['LIST_ID'];
                $result[$tupple['LIST_ID']]['IS_DONE'] = $tupple['IS_DONE'];
                $result[$tupple['LIST_ID']]['LIST_CREATED_BY'] = $tupple['CREATED_BY_NAME'];
                $result[$tupple['LIST_ID']]['LIST_ITEMS'][$tupple['ITEM']] = $tupple;
                $result[$tupple['LIST_ID']]['LAST_MODIFIED'] = $tupple['LAST_MODIFIED'];
                $result[$tupple['LIST_ID']]['LAST_MODIFIED_BY'] = $tupple['IS_BOUGHT'] == 'Y' ? $tupple['BOUGHT_BY_NAME'] : $tupple['REQUESTED_BY_NAME'];
                continue;
            }
            $result[$tupple['LIST_ID']]['TITLE'] = $tupple['TITLE'];
            $result[$tupple['LIST_ID']]['LIST_ID'] = $tupple['LIST_ID'];
            $result[$tupple['LIST_ID']]['IS_DONE'] = $tupple['IS_DONE'];
            $result[$tupple['LIST_ID']]['LIST_CREATED_BY'] = $tupple['CREATED_BY_NAME'];
            $result[$tupple['LIST_ID']]['LIST_ITEMS'][$tupple['ITEM']] = $tupple;
        }

        return $result;
    }

    public function getFilteredLists() {
        $data = $this->commonModel->getAllListsFromDb();
        if (!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        $result = [];
        foreach ($data as $tupple) {
            $result[$tupple['TYPE']][] = $tupple;
        }
        //var_dump($result);exit;
        return $result;
    }

    public function updateList($list) {
        global $USER_ID;
        if (!isset($list['LIST_ID'])) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid JSON");
        }
        
        $addedItemsSet = []; //notification purpose
        $boughtItemsSet = []; //notification purpose
        $unBoughtItemsSet = []; //notification purpose
        
        if ($list['LIST_ID'] == -1) {
            //create a new list
            $tableName = 'SHOPPING_LIST';
            $dataArr = [];
            $dataArr['TITLE'] = $list['TITLE'];
            $dataArr['CREATED_ON'] = date('Y-m-d H:i:s', time());
            $dataArr['CREATED_BY'] = $USER_ID;
            $LIST_ID = $this->commonModel->insertIntoTableGeneric($tableName, $dataArr);
            //$lastTupple = $this->commonModel->getLastInsertedTupple($tableName);
            //$LIST_ID = $lastTupple[0]['ID'];
            //now insert items one by one from the JSON
            unset($tableName);
            $tableName = 'LIST_ITEM';
            $itemsArr = $list['ITEMS'];
            foreach ($itemsArr as $index => $item) {
                unset($dataArr);
                $dataArr['LIST_ID'] = $LIST_ID;
                $dataArr['TYPE'] = $item['TYPE'];
                $dataArr['REQUESTED_BY'] = $USER_ID;
                $dataArr['ITEM'] = $item['TITLE'];
                $dataArr['LAST_MODIFIED'] = date('Y-m-d H:i:s', time());
                $dataArr['REQUESTED_ON'] = $dataArr['LAST_MODIFIED'];
                if ($item['IS_BOUGHT'] == 'Y') {
                    $dataArr['IS_BOUGHT'] = $item['IS_BOUGHT'];
                    $dataArr['BOUGHT_BY'] = $USER_ID;
                    $dataArr['BOUGHT_ON'] = date('Y-m-d', time());
                    $boughtItemsSet[] = $item['TITLE'];
                }
                $addedItemsSet[] = $item['TITLE'];
                
                $this->commonModel->insertIntoTableGeneric($tableName, $dataArr);
            }
        } else {
            //old list
            //overwrite list title
            $tableName = 'SHOPPING_LIST';
            $updateSet['TITLE'] = $list['TITLE'];
            $LIST_ID = $list['LIST_ID'];
            $this->commonModel->updateShoppingList($LIST_ID, $updateSet);

            //now update/insert items
            unset($tableName);
            $tableName = 'LIST_ITEM';
            $itemsArr = $list['ITEMS'];
            foreach ($itemsArr as $index => $item) {

                if ($item['IS_DIRTY'] == '0') {
                    continue;
                }
                unset($dataArr);

                if ($item['ID'] == '-1') {
                    //new item
                    $dataArr['LIST_ID'] = $LIST_ID;
                    $dataArr['TYPE'] = $item['TYPE'];
                    $dataArr['REQUESTED_BY'] = $USER_ID;
                    $dataArr['ITEM'] = $item['TITLE'];
                    $dataArr['LAST_MODIFIED'] = date('Y-m-d H:i:s', time());
                    $dataArr['REQUESTED_ON'] = $dataArr['LAST_MODIFIED'];
                    if ($item['IS_BOUGHT'] == 'Y') {
                        $dataArr['IS_BOUGHT'] = $item['IS_BOUGHT'];
                        $dataArr['BOUGHT_BY'] = $USER_ID;
                        $dataArr['BOUGHT_ON'] = date('Y-m-d', time());
                        $boughtItemsSet[] = $item['TITLE'];
                    }
                    $addedItemsSet[] = $item['TITLE'];;
                    $this->commonModel->insertIntoTableGeneric($tableName, $dataArr);
                } else {
                    $ITEM_ID = $item['ID'];
                    $dataArr['ITEM'] = $item['TITLE'];
                    $dataArr['TYPE'] = $item['TYPE'];
                    if ($item['IS_BOUGHT'] == 'Y') {
                        //check if already bought
                        $isBoughtData = $this->commonModel->getBoughtStatusOfItem($ITEM_ID);
                        if ($isBoughtData[0]['IS_BOUGHT'] == 'N') {
                            $dataArr['IS_BOUGHT'] = $item['IS_BOUGHT'];
                            $dataArr['BOUGHT_BY'] = $USER_ID;
                            $dataArr['BOUGHT_ON'] = date('Y-m-d', time());
                            $boughtItemsSet[] = $item['TITLE'];
                        }
                    } else {
                        $isBoughtData = $this->commonModel->getBoughtStatusOfItem($ITEM_ID);
                        if ($isBoughtData[0]['IS_BOUGHT'] == 'Y') {
                            $dataArr['IS_BOUGHT'] = 'N';
                            $dataArr['BOUGHT_BY'] = null;
                            $dataArr['BOUGHT_ON'] = null;
                            $unBoughtItemsSet[] = $item['TITLE'];
                        }                        
                    }
                    //var_dump($dataArr);exit;
                    $this->commonModel->updateListItem($ITEM_ID, $dataArr);
                }
            }
        }
        //update list IS_DONE status if all items are bought
        $this->updateIsDoneOfList($LIST_ID);
        
        //NOTIFICATION
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),
            'LIST_ID' => $list['LIST_ID'],
            'TITLE' => $list['TITLE'],
            'NEW' => $addedItemsSet,
            'BOUGHT' => $boughtItemsSet,
            'UNBOUGHT' => $unBoughtItemsSet
        ];
        $this->generateAndSendNotificationMsg('SHOPPING_LIST', $notificationData);
        
        return ['INSERTION_ID' => $LIST_ID];
    }
    
    
    public function deleteList($listId, $listTitle) {
        $isDone = $this->commonModel->getIsDoneOfList($listId);
        if(!$isDone) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        
        if($isDone == 'N') {
            global $ERROR_CODE;
            $ERROR_CODE = '52';
            throw new Exception("Invalid Query: Some unbought items still in list");
        }
        
        
        $status = $this->commonModel->deleteListFromDb($listId);
        if(!$status) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        
        global $USER_ID;
        //NOTIFICATION
        
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),            
            'LIST_TITLE' => $listTitle
        ];
        $this->generateAndSendNotificationMsg('LIST_DELETE', $notificationData);
        return [];
    }

    public function updateIsDoneOfList($listId) {
        $items = $this->commonModel->getAllItemsOfList($listId);
        $is_done = true;
        foreach ($items as $item) {
            if ($item['IS_BOUGHT'] == 'N') {
                $is_done = FALSE;
            }
        }

        if ($is_done) {
            $updateSet['IS_DONE'] = 'Y';
            $this->commonModel->updateShoppingList($listId, $updateSet);
        } else {
            $updateSet['IS_DONE'] = 'N';
            $this->commonModel->updateShoppingList($listId, $updateSet);
        }
    }

    public function updateNotificationToken($token) {
        global $USER_ID;
        $status = $this->commonModel->updateNotificationToken($token, $USER_ID);
        if (!$status) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }

        return [];
    }

    public function getAbsentDays($empId, $month) {
        $data = $this->commonModel->getAbsentDays($empId, $month);
        if (!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        return $data;
    }

    public function getEmployeeDetails() {
        $data = $this->commonModel->getEmployeeDetails();
        if (!$data) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        return $data;
    }

    public function reportAbsent($date, $shift, $empId, $reason) {
        $shiftValArr = [
            'MORNING',
            'EVENING'
        ];

        if (!in_array($shift, $shiftValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: SHIFT");
        }
        if (!preg_match('/^20[0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $date)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DATE");
        }

        global $USER_ID;
        $status = $this->commonModel->reportAbsent($empId, $date, $shift, $reason, $USER_ID);
        if (!$status) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        
        
        //NOTIFICATION
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),
            'SHIFT' => $shift,
            'DATE' => $date,
            'ACTED_ON' => $this->commonModel->getEmployeeName($empId),
            'REASON' => $reason
        ];
        $this->generateAndSendNotificationMsg('ABSENT_SINGLE', $notificationData);
        
        return [];
    }

    public function reportAbsentRange($inputData, $empId, $reason) {
        global $USER_ID;

        $shiftValArr = [
            'MORNING',
            'EVENING'
        ];

        if (!in_array($inputData['FROM_SHIFT'], $shiftValArr) || !in_array($inputData['TO_SHIFT'], $shiftValArr)) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: SHIFT");
        }
        if (!preg_match('/^20[0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $inputData['FROM_DATE']) || !preg_match('/^20[0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $inputData['TO_DATE'])) {
            global $ERROR_CODE;
            $ERROR_CODE = '62';
            throw new Exception("Not a valid input under field: DATE");
        }


        $currDate = 0;
        $currShift = 0;

        while (!($currDate == $inputData['TO_DATE'] && $currShift == $inputData['TO_SHIFT'])) {
            if ($currDate == 0) {
                $currDate = $inputData['FROM_DATE'];
                $currShift = $inputData['FROM_SHIFT'];
            } else if ($currShift == 'MORNING') {
                $currShift = 'EVENING';
            } else {
                $currDate = date('Y-m-d', strtotime($currDate . ' +1 day'));
                $currShift = 'MORNING';
            }
            $status = $this->commonModel->reportAbsent($empId, $currDate, $currShift, $reason, $USER_ID);
            if (!$status) {
                global $ERROR_CODE;
                $ERROR_CODE = '120';
                throw new Exception("No data found/query failed!");
            }
        }
        
        
        //NOTIFICATION
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),
            'FROM_DATE' => $inputData['FROM_DATE'],
            'TO_DATE' => $inputData['TO_DATE'],
            'FROM_SHIFT' => $inputData['FROM_SHIFT'],
            'TO_SHIFT' => $inputData['TO_SHIFT'],
            'ACTED_ON' => $this->commonModel->getEmployeeName($empId),
            'REASON' => $reason
        ];
        $this->generateAndSendNotificationMsg('ABSENT_RANGE', $notificationData);
        
        
        return [];
    }

    public function removeAbsentEntry($id) {
        $status = $this->commonModel->deleteAbsentEntry($id);
        if (!$status) {
            global $ERROR_CODE;
            $ERROR_CODE = '120';
            throw new Exception("No data found/query failed!");
        }
        
        global $USER_ID;
        //NOTIFICATION
        
        $notificationData = [
            'ACTION_BY' => $this->commonModel->getUserName($USER_ID),
            'ID' => $id
        ];
        $this->generateAndSendNotificationMsg('ABSENT_DELETE', $notificationData);
        return [];
    }

    
       
    private function generateAndSendNotificationMsg($moduleName, $data) {
        $msg= '';
        $notificationModule = '';
        switch ($moduleName) {
            case 'ABSENT_SINGLE':
                $notificationModule = 'ATTENDANCE';
                $msg = $data['ACTION_BY']." marked a absent for ".$data['ACTED_ON']." for ".$data['DATE'].", ".$data['SHIFT']." shift because of reason: ".$data['REASON'].".";
                break;
            case 'ABSENT_RANGE':
                $notificationModule = 'ATTENDANCE';
                $msg = $data['ACTION_BY']." marked a absent for ".$data['ACTED_ON']." from ".$data['FROM_DATE'].", ".$data['FROM_SHIFT']." shift to ".$data['TO_DATE'].", ".$data['TO_SHIFT']." shift because of reason: ".$data['REASON'].".";
                break;
            case 'ABSENT_DELETE':
                $notificationModule = 'ATTENDANCE';
                $msg = $data['ACTION_BY']." deleted the marked absent against ID: ".$data['ID'].".";
                break;
            case 'LIST_DELETE':
                $notificationModule = 'SHOPPING_LIST';
                $msg = $data['ACTION_BY']." deleted shopping list with title: ".$data['LIST_TITLE'].".";
                break;
            case 'SHOPPING_LIST' :
                $notificationModule = 'SHOPPING_LIST';
                if($data['LIST_ID'] == '-1') {
                    $msg = $data['ACTION_BY'] ." added a new list with title: ". $data['TITLE']. ".";
                } else {
                    $msg = $data['ACTION_BY']." updated the list with title: ". $data['TITLE']. ".";
                }
                
                if(!empty($data['NEW'])) {
                    $msg .= ", Added items:-". implode(', ', $data['NEW']);
                }
                if(!empty($data['BOUGHT'])) {
                    $msg .= ", Bought items:-". implode(', ', $data['BOUGHT']);
                }
                if(!empty($data['UNBOUGHT'])) {
                    $msg .= ", Un-Bought items:-". implode(', ', $data['UNBOUGHT']);
                }                
                break;
            case 'FOOD_MENU' :
                $notificationModule = "FOOD_MENU";
                $msg = $data['ACTION_BY']." updated dish for ".$data['DAY']." date: ".$data['DATE']." meal: ".$data['MEAL']."\n";
                if($data['IS_DEFAULT'] == 'Y') {
                    $msg .= " as ". $data['DISHNAME']." which is marked as DEFAULT.";
                } else {
                    $msg .= " as ". $data['DISHNAME'].".";
                }
                break;
        }
        
        $reciepentArr = $this->commonModel->getNotificationToken();
        $today = date('Y-m-d');
        
        $notificationData = [
            'MODULE' => $notificationModule,
            'ACTIVITIES' => [
                [
                    'DATE' => $today,
                    'CONTENT' => $msg
                ]
            ]
        ];
        
        $this->sendNotification($reciepentArr, $notificationData);
    }
    //##################### NOTIFICATION ############################



    private function sendNotification($ids, $data) {

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'registration_ids' => $ids,
            'data' => $data
        );
        $fields = json_encode($fields);

        $serverKey = $this->commonModel->getFcmServerKey();
        if (!$serverKey) {
            return FALSE;
        }

        $headers = array(
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        //echo $result;
        curl_close($ch);
    }

    //##################### NOTIFICATION ############################
}
