<?php



/**
 * To provide all the basic and miscellaneous DB level functions 
 * @author tarun
 */

class CommonModel extends CI_Model{
    
    function __construct()
	{
		parent::__construct();
	}
    
    
    
    public function authenticateChecksum($checksum) {
        $query = "SELECT PROFILE_ID from USER_PROFILE where CHECKSUM = '$checksum'";
        $result = $this->db->query($query);        
        if($result->num_rows() > 0) {
            $row = $result->row();
            return $row['PROFILE_ID'];            
        }
        return FALSE;
    }
    
    
    public function updateChecksum($userId, $checksum) {
        $query = "UPDATE USER_PROFILE set CHECKSUM='$checksum' where PROFILE_ID='$userId'";
        if($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    public function authenticateUser($username, $pass_hash) {
        
    }
    
    
}
