<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class MY_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->mysql("garden");
    }
    
    public function on_shutdown() {
        #$this->pb_db->close();
    }
}