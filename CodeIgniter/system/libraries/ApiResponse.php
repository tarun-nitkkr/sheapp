<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library with functions to format response JSON with error codes, data, etc
 * @author tarun
 */
class ApiResponse {
    
    /**
     * To get sample JSON response for testing
     * @return JSON string
     */
    public function getSampleJSONResponse() {
        $data = [
            'STATUS' => 'OK',
            'ERRORCODE' => '',
            'MSG' => "THIS IS SAMPLE JSON",
            'DATA' => [
                'USERNAME' => 'username',
                'PASSWORD' => 'somestring'
            ],
            'USERID' => '-1'            
        ];
        
        return json_encode($data);
    }
    
    
    
    /**
     * JSON with Ok response
     * @param string $userId
     * @param array $data
     * @param string $msg
     * @return JSON string
     */
    public function getOkJSONResponse($userId, $data, $msg = "API RESPONDED SUCCESSFULLY" ) {
        $data = [
            'STATUS' => 'OK',
            'ERRORCODE' => '',
            'MSG' => $msg,
            'DATA' => $data,
            'USERID' => $userId            
        ];
        
        return json_encode($data);
    }
    
    
    /**
     * To get JSON in case of ERROR
     * @param string $userId
     * @param string $errorCode
     * @param string $msg
     * @return JSON string
     */
    public function getErrorJSONResponse($userId, $errorCode, $msg = "ERROR ENCOUNTERED") {
        $data = [
            'STATUS' => 'ERROR',
            'ERRORCODE' => $errorCode,
            'MSG' => $msg,
            'DATA' => array(),
            'USERID' => $userId
        ];
        
        return json_encode($data);
    }
    
    
    /**
     * To get JSON in case of FALSE i.e. when no targeted data is present in DB
     * @param string $userId
     * @param string $msg
     * @return JSON string
     */
    public function getFalseJSONResponse($userId, $msg = "NO SUCH TUPPLE FOUND") {
        $data = [
            'STATUS' => 'FALSE',
            'ERRORCODE' => '-1',
            'MSG' => $msg,
            'DATA' => array(),
            'USERID' => $userId
        ];
        
        return json_encode($data);
    }
    
}
