<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 员工福利
 * User: liubin
 * Date: 2018/3/22
 * Time: 15:37
 */
class Operation_log extends MY_Controller
{
    function init() {
        $this->check_permission(['bgm-sos.system-manage.log']);
        $this->load->biz(['operation_log_biz']);
    }

    /**
     * 操作日志列表
     *
     * @author liubin
     * @Time:2018/5/17 10:46
     */
    public function get_list()
    {
        $args['sso_username'] = trim($this->input->zget_post('sso_username',''));
        $args['type'] = trim($this->input->zget_post('type',''));
        $args['act'] = trim($this->input->zget_post('act',''));
        $args['start_date'] = trim($this->input->zget_post('start_date',''));
        $args['end_date'] = trim($this->input->zget_post('end_date',''));
        $args['limit'] = (int)$this->input->zget_post('limit',20);
        $args['page'] = (int)$this->input->zget_post('page',1);
        $r = $this->operation_log_biz->getList($args);
        return $this->response($r['error_code'],$r['error_msg'],$r['data']);
    }

}