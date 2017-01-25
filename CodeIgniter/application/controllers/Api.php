<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Main Controller to handle most of the API requests
 * @author tarun
 */
class Api extends CI_Controller {
    
    
    function __construct()
	{
		parent::__construct();
		

	}
    
    
    public function test(){
        echo "This is test string";
    }
    
}
