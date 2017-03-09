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
        if(strtolower($userName) == 'deepika' && $passWord == 'icandoit') {
            session_start();
            $_SESSION['user_name']='DEEPS';
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
        if($_SESSION['user_name'] == 'DEEPS'){
            $this->load->view('HBD.php');
            return;
        }else {
            header("Location: https://firest0ne.me/SHEapp/Launch/simpleTry");
            //header("Location: http://www.she-app.com/Api/simpleTry?CHECKSUM=a439e60cfc525a3e21b9768f4b2152afa6ab6ed3");
            return;
        }        
    }
    
}
