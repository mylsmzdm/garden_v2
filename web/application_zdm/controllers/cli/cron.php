<?php
if (!defined('BASEPATH'))    exit('No direct script access allowed');

/**
 * 脚本集合
 *
 * @property employee_biz employee_biz
 * @property sso_record_log_biz sso_record_log_biz
 * @author Dacheng Chen
 * @date 2019-7-9
 */
class cron extends MY_Controller {

    function init()
    {
        @set_time_limit(0);
        #禁止非命令行方式访问
        if (!$this->input->is_cli_request()) {
            exit;
        }
        #监控防止同一套参数同时开多个进程
        $this->cron_manager();

        set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit','512M');

        $this->db = $this->load->mysql('sos');
    }
}
