<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 职场设备
 */
class Welcome extends CI_Controller {
     public function index()
    {
         header('Location:/pages/sos/index.html');
    }
}
