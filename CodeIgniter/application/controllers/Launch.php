<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Launch
 *
 * @author tarun
 */
class Launch extends CI_Controller {

    //put your code here

    function __construct() {
        parent::__construct();
    }

    public function simpleTry() {
        header('Content-Type: text/html');
        $this->load->view("tokenView.php");
        return;
    }

    public function loginHWD() {
        header('Content-Type: text/html');
        $userName = $_REQUEST['USERNAME'];
        $passWord = $_REQUEST['PASSWORD'];
        if (strtolower($userName) == 'deepika' && $passWord == 'icandoit') {
            session_start();
            $_SESSION['user_name'] = 'DEEPS';
            header("Location: https://firest0ne.me/SHEapp/Launch/happyWomensDay");
            //header("Location:  http://www.she-app.com/Api/happyWomensDay?CHECKSUM=a439e60cfc525a3e21b9768f4b2152afa6ab6ed3");
            return;
        } else {
            header("Location: https://firest0ne.me/SHEapp/Launch/simpleTry");
            //header("Location: http://www.she-app.com/Api/simpleTry?CHECKSUM=a439e60cfc525a3e21b9768f4b2152afa6ab6ed3");
            return;
        }
    }

    public function happyWomensDay() {
        header('Content-Type: text/html');
        session_start();
        if ($_SESSION['user_name'] == 'DEEPS') {
            $this->load->view('HBD.php');
            return;
        } else {
            header("Location: https://firest0ne.me/SHEapp/Launch/simpleTry");
            //header("Location: http://www.she-app.com/Api/simpleTry?CHECKSUM=a439e60cfc525a3e21b9768f4b2152afa6ab6ed3");
            return;
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    public function hitIt() {
        header('Content-Type: text/html');



        if (isset($_REQUEST['TOKEN'])) {
            if ($_REQUEST['TOKEN'] == 'ferry77760') {
                echo "Authentication Successful.<br><br>";
            } else {
                echo "Authentication Failed!<br><br>";
                return;
            }
        } else {
            echo "No authentication token passed!<br><br>";
            return;
        }

        $giftValueParam = "giftCode=agc2000";

        if (isset($_REQUEST['VALUE'])) {
            if ($_REQUEST['VALUE'] == '2000') {
                $giftValueParam = "giftCode=agc2000";
            }
            if ($_REQUEST['VALUE'] == '1000') {
                $giftValueParam = "giftCode=agc1000";
            }
            if ($_REQUEST['VALUE'] == '500') {
                $giftValueParam = "giftCode=agc500";
            }
        }

        if (isset($_REQUEST['COOKIE'])) {
            $cookieValue = $_REQUEST['COOKIE'];
        } else {
            echo 'NO cookie sent!';
            return;
        }


        if(isset($_REQUEST['RANDOM'])) {
            $random = rand(1,3);
            if($random == 1) {
                $giftValueParam = "giftCode=agc2000";
            } elseif ($random == 2) {
                $giftValueParam = "giftCode=agc1000";
            } else {
                $giftValueParam = "giftCode=agc500";
            }
        }
        
        

        echo "initiating with cookie value:- <br>" . $cookieValue;
        echo "<br><br> and Gift Value:" . $giftValueParam . "<br><br>-----------------------------------------------------------------------------<br>";
        //return;

        $cookieSet = explode(';', $cookieValue);

        $cookieArr = [];
        foreach ($cookieSet as $cookie) {
            $cookie = trim($cookie);
            $cSet = explode('=', $cookie);
            $cookieArr[$cSet[0]] = $cSet[1];
        }

        $updateArr = ['opbct', 'opnt', 'optime_browser', 'opnt_event'];

        //print_r($cookieValue);

        $timeToSet = 1492583400100;

        foreach ($updateArr as $value) {
            if(isset($cookieArr[$value])) {
                $cookieArr[$value] = $timeToSet;
                $timeToSet += 24;
            }            
        }
        
        $userName = $cookieArr['username'];
        
        $cookieArr['opstep'] = intval($cookieArr['opstep']) + 2;
        $cookieArr['_gali'] = 'getAmazonGift';
        $finalCookieStr = '';
        $tempArr = [];
        foreach ($cookieArr as $key => $value) {
            $tempArr[] = $key . "=" . $value;
        }

        $finalCookieStr = implode(';', $tempArr);

        $cookieValue = $finalCookieStr; //cookie updated
        //print_r($cookieValue);exit;

        $ch = curl_init();

        while (@ob_end_flush());

        while (1) {
            $currentTime = time();
            $scheduledTime = strtotime('2017-04-19 11:59:40');

            if ($currentTime < $scheduledTime) {
                echo "Waiting for 2017-04-19 11:59:59.<br>CurrentTime:-" . date('Y-m-d H:i:s', time()) . "<br><br>";
                flush();
                sleep(10);
                continue;
            }
            break;
        }


        //fine waiting
        while (1) {
            $currentTime = time();
            $scheduledTime = strtotime('2017-04-19 11:59:58');

            if ($currentTime < $scheduledTime) {
                echo "Waiting for 2017-04-19 11:59:59.<br>CurrentTime:-" . date('Y-m-d H:i:s', time()) . "<br><br>";
                flush();
                sleep(1);
                continue;
            }
            break;
        }

        //more fine waiting
        while (1) {
            $currentTime = time();
            $scheduledTime = strtotime('2017-04-19 12:00:00');

            if ($currentTime < $scheduledTime) {
                echo "Waiting for 2017-04-19 11:59:59.<br>CurrentTime:-" . date('Y-m-d H:i:s', time()) . "<br><br>";
                //flush();
                usleep(50000);
                continue;
            }
            break;
        }
        
        
        echo "-------------------Initiating request --------------------------<br><br>";
        flush();

        $i = 0;
        //$file = fopen("log_$userName.txt", "a");
        while ($i < 400) {
            curl_setopt($ch, CURLOPT_URL, "https://oneplusstore.in/xman/tvc/activity/claim");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $giftValueParam);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

            $headers = array();
            $headers[] = "Pragma: no-cache";
            $headers[] = "Origin: https://oneplusstore.in";
            $headers[] = "Accept-Encoding: gzip, deflate, br";
            $headers[] = "Accept-Language: en-GB,en-US;q=0.8,en;q=0.6";
            $headers[] = "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.100 Safari/537.36";
            $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
            $headers[] = "Accept: application/json, text/javascript, */*; q=0.01";
            $headers[] = "Cache-Control: no-cache";
            $headers[] = "X-Requested-With: XMLHttpRequest";
            $headers[] = "Cookie: " . $cookieValue;
            $headers[] = "Connection: keep-alive";
            $headers[] = "Referer: https://oneplusstore.in/onecrore/user";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }

            $resultArr = json_decode($result, true);


            //var_dump($resultArr);

            echo $result . "<br><br>";
            flush();
            
            
            //fwrite($file, "Time:" . date("Y-m-d H:i:s") . ",OUTPUT:" . $result);
            
            //echo "1\n";
            //sleep();
            usleep(200000);
            //echo "2";
            $i++;

            if ($resultArr['errCode'] == 0) {
                echo "<br> Stoping code since request is successful<br>";
                flush();
                break;
            }
        }
        //fclose($file);
        curl_close($ch);

        return;
    }

    
    
    
    
    
    
    
    
    
    public function test() {
        header('Content-Type: text/html');
        if (isset($_REQUEST['TOKEN'])) {
            if ($_REQUEST['TOKEN'] == 'ferry77760') {
                echo "Authentication Successful.<br><br>";
            } else {
                echo "Authentication Failed!<br><br>";
                return;
            }
        } else {
            echo "No authentication token passed!<br><br>";
            return;
        }

        $giftValueParam = "giftCode=agc2000";

        if (isset($_REQUEST['VALUE'])) {
            if ($_REQUEST['VALUE'] == '2000') {
                $giftValueParam = "giftCode=agc2000";
            }
            if ($_REQUEST['VALUE'] == '1000') {
                $giftValueParam = "giftCode=agc1000";
            }
            if ($_REQUEST['VALUE'] == '500') {
                $giftValueParam = "giftCode=agc500";
            }
        }

        if (isset($_REQUEST['COOKIE'])) {
            $cookieValue = $_REQUEST['COOKIE'];
        } else {
            echo 'NO cookie sent!';
            return;
        }
        echo "initiating with cookie value:- <br>" . $cookieValue;
        echo "<br><br> and Gift Value:" . $giftValueParam . "<br><br>-----------------------------------------------------------------------------<br>";
        

        $ch = curl_init();

        while (@ob_end_flush());
        echo "-------------------Initiating request --------------------------<br><br>";
        flush();
        $i = 0;
        //$file = fopen("log_test.txt", "a");
        while ($i < 1) {
            curl_setopt($ch, CURLOPT_URL, "https://oneplusstore.in/xman/tvc/activity/claim");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $giftValueParam);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

            $headers = array();
            $headers[] = "Pragma: no-cache";
            $headers[] = "Origin: https://oneplusstore.in";
            $headers[] = "Accept-Encoding: gzip, deflate, br";
            $headers[] = "Accept-Language: en-GB,en-US;q=0.8,en;q=0.6";
            $headers[] = "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.100 Safari/537.36";
            $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
            $headers[] = "Accept: application/json, text/javascript, */*; q=0.01";
            $headers[] = "Cache-Control: no-cache";
            $headers[] = "X-Requested-With: XMLHttpRequest";
            $headers[] = "Cookie: " . $cookieValue;
            $headers[] = "Connection: keep-alive";
            $headers[] = "Referer: https://oneplusstore.in/onecrore/user";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }

            $resultArr = json_decode($result, true);


            //var_dump($resultArr);

            echo $result . "<br><br>";
            flush();
            //fwrite($file, "[TEST]Time:" . date("Y-m-d H:i:s") . ",COOKIE:".$cookieValue.",OUTPUT:" . $result);
            //echo "1\n";
            //sleep();
            usleep(200000);
            //echo "2";
            $i++;

            if ($resultArr['errCode'] == 0) {
                echo "<br> Stoping code since request is successful<br>";
                flush();
                break;
            }
        }
        curl_close($ch);
        //fclose($file);
        return;
    }

}
