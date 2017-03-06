<?php

/**
 * To provide all the basic and miscellaneous DB level functions 
 * @author tarun
 */
class CommonModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function authenticateChecksum($checksum) {
        $query = "SELECT PROFILE_ID from USER_PROFILE where CHECKSUM = '$checksum' AND IS_ACTIVE='Y'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            $row = $result->row();
            return $row->PROFILE_ID;
        }
        return FALSE;
    }

    public function updateChecksum($userId, $checksum) {
        $query = "UPDATE USER_PROFILE set CHECKSUM='$checksum' where PROFILE_ID='$userId'";
        if ($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkChecksum($userId) {
        $query = "SELECT CHECKSUM from USER_PROFILE where PROFILE_ID='$userId' AND IS_ACTIVE='Y'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->row()->CHECKSUM;
        }
        return FALSE;
    }

    public function authenticateUser($username, $pass_hash) {
        $query = "SELECT PROFILE_ID, PASS_HASH from USER_PROFILE where USERNAME = '$username' AND IS_ACTIVE='Y'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            $row = $result->row();
            if ($pass_hash == $row->PASS_HASH) {
                $datetime = date("Y-m-d H:i:s", time());
                $query = "UPDATE USER_PROFILE SET LAST_LOGIN = '$datetime' where PROFILE_ID = '" . $row->PROFILE_ID . "'";
                $this->db->query($query);
                return $row->PROFILE_ID;
            } else {
                return 0; //password didn't matched
            }
        } else {
            return -1; //no such user found
        }
    }

    public function getFoodMenuFromDb() {
        $dateMonday = date('Y-m-d', strtotime('last monday'));
        $dateSunday = date('Y-m-d', strtotime('next sunday'));
        //echo $dateMonday.'-----'.$dateSunday;exit;
        $query = "SELECT * FROM FOOD_MENU where (IS_DEFAULT='Y' OR (FOR_DATE BETWEEN '$dateMonday' AND '$dateSunday')) AND IS_ACTIVE='Y'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }

    public function invalidateChecksum($userId) {
        $query = "UPDATE USER_PROFILE set CHECKSUM = NULL where PROFILE_ID='$userId'";
        if ($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function updateFoodMenuInDb($dishName, $date, $day, $meal, $isDefault) {
        if ($isDefault == 'Y') {
            $updateQuery = "UPDATE FOOD_MENU SET IS_DEFAULT = 'N' WHERE DAY='$day' AND MEAL='$meal' AND IS_DEFAULT='Y'";
            if (!$this->db->query($updateQuery)) {
                return FALSE;
            }
        }

        $deleteQuery = "DELETE FROM FOOD_MENU WHERE FOR_DATE='$date' AND DAY='$day' AND MEAL='$meal' AND IS_DEFAULT = 'N'";
        if (!$this->db->query($deleteQuery)) {
            return FALSE;
        }


        global $USER_ID;
        $insertQuery = "INSERT INTO FOOD_MENU(FOR_DATE, DAY, DISH_NAME, MEAL, IS_DEFAULT, ENTERED_BY) VALUES('$date','$day', '$dishName', '$meal', '$isDefault','$USER_ID')";
        if (!$this->db->query($insertQuery)) {
            return FALSE;
        }

        return TRUE;
    }

    public function getAllListsFromDb() {
        $query = "SELECT SL.TITLE, SL.IS_DONE,  LI.* , UP1.NAME as REQUESTED_BY_NAME, UP2.NAME as BOUGHT_BY_NAME, UP3.NAME as CREATED_BY_NAME  FROM SHOPPING_LIST SL JOIN LIST_ITEM LI ON LI.LIST_ID = SL.ID JOIN USER_PROFILE UP1 ON UP1.PROFILE_ID = LI.REQUESTED_BY JOIN USER_PROFILE UP3 ON UP3.PROFILE_ID = SL.CREATED_BY LEFT JOIN USER_PROFILE UP2 on UP2.PROFILE_ID = LI.BOUGHT_BY WHERE SL.IS_ACTIVE='Y' AND LI.IS_ACTIVE='Y' ORDER BY SL.CREATED_ON, LI.LAST_MODIFIED DESC";
        //echo $query;exit;
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }

    public function updateListItem($itemId, $updateSet) {
        //var_dump($updateSet);
        $updateStr = '';
        foreach ($updateSet as $key => $value) {
            $updateStr .= $key . "= '" . $value . "',";
        }  
        $updateStr = trim($updateStr, ",");
        $updateQuery = "UPDATE LIST_ITEM SET $updateStr WHERE ID=$itemId";
        //echo $updateQuery;exit;
        if (!$this->db->query($updateQuery)) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    public function updateShoppingList($listId, $updateSet) {
        $updateStr = '';
        foreach ($updateSet as $key => $value) {
            $updateStr .= $key . "= '" . $value . "',";
        }  
        $updateStr = trim($updateStr, ",");
        $updateQuery = "UPDATE SHOPPING_LIST SET $updateStr WHERE ID=$listId";
        if (!$this->db->query($updateQuery)) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    public function getListItem($itemId) {
        $query = "SELECT * from LIST_ITEM where ID=$itemId and IS_ACTIVE='Y'";
        $result = $this->db->query($query);
        if($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }
    
    /**
     * For INSERT, UPDATE, DELETE QUERY on mySql master server
     * @param string $tableName dbName.tableName
     * @param array $dataArr with keys as table column names and values containing corresponding values
     * @return boolean true on success
     * @throws Exception : if query fails     
     */
    public function insertIntoTableGeneric($tableName, $dataArr) {
        $keys =  '('.implode(',', array_keys($dataArr)) . ')';
        $values = "('" . implode("','", array_values($dataArr)). "')";
        $sql = "INSERT INTO $tableName $keys VALUES $values";
        if(!$this->db->query($sql)){
            throw new \Exception('Query error.');
        }
        return true;
    }
    
    
    public function getLastInsertedTupple($tableName, $primaryKey = 'ID') {
        $query = "SELECT $primaryKey FROM $tableName order by $primaryKey DESC limit 1";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }
    
    
    
    public function getBoughtStatusOfItem($itemId) {
        $query = "select IS_BOUGHT from LIST_ITEM where ID = $itemId";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }
    
    
    
    public function getAllItemsOfList($listId) {
        $query = "select * from LIST_ITEM where LIST_ID = $listId";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->result_array();
        }
        return FALSE;
    }

}
