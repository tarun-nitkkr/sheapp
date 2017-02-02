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
        $query = "SELECT PROFILE_ID from USER_PROFILE where CHECKSUM = '$checksum'";
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
        $query = "SELECT CHECKSUM from USER_PROFILE where PROFILE_ID='$userId'";
        $result = $this->db->query($query);
        if ($result && $result->num_rows() > 0) {
            return $result->row()->CHECKSUM;
        }
        return FALSE;
    }

    public function authenticateUser($username, $pass_hash) {
        $query = "SELECT PROFILE_ID, PASS_HASH from USER_PROFILE where USERNAME = '$username'";
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
        $query = "SELECT * FROM FOOD_MENU where IS_DEFAULT='Y' OR (FOR_DATE BETWEEN '$dateMonday' AND '$dateSunday')";
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
        
        $deleteQuery = "DELETE FROM FOOD_MENU WHERE FOR_DATE='$date'";
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

}
