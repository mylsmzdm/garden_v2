<?php

/**
 * 队员service
 */
class Dy_biz extends CI_Biz
{
    const DING_MAPPING = [
        'sys00-name'=>'name',
        'sys00-email'=>'email',
        'sys00-mobile'=>'mobile',
        'sys00-jobNumber'=>'job_number',
        'sys00-workPlace'=>'zc_name',
        'sys02-sexType'=>'sex_type',
    ];
    public function __construct()
    {
        $this->load->model([ 'chat_model', 'ding_model']);
    }

    /**
     * 获取队员列表
     */
    public function get_dy_list($page=1,$page_size=20,$id = 1, $app_id = 1,$name='',$jobNumber='',$zc_name='',$sex=-1)
    {
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $chat_info = $this->chat_model->get_chat_info($id);
        if (!$chat_info) {
            //没有创建群，先去创建群
            $this->load->config('ding');
            $ding_config = $this->config->item('ding');
            $chat_name = $ding_config['chat']['name'];
            $owner_mobile = $ding_config['chat']['owner'];
            $owner_user_info = $this->ding_model->get_user_info_by_mobile($app_id, $owner_mobile);
            $owner_user_id = 0;
            if (FALSE === $owner_user_info) {
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '钉钉接口请求报错';
                goto ARCHOR_RESULT;
            }
            $owner_user_id = $owner_user_info['result']['userid'];

            $useridlistArr[] = $owner_user_id;
            $create_chat_info = $this->ding_model->create_chat_group($app_id, $chat_name, $owner_user_id, $useridlistArr);
            if (FALSE === $create_chat_info) {
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '钉钉接口请求报错';
                goto ARCHOR_RESULT;
            }

            $chat_id = $create_chat_info['chatid'];
            //保存群信息
            $add_chat = $this->chat_model->add_chat_record($chat_name, $owner_user_id, $create_chat_info['chatid'], $create_chat_info['openConversationId'], $create_chat_info['conversationTag'], $app_id);
            if (FALSE === $add_chat) {
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                goto ARCHOR_RESULT;
            }
        } else {
            $chat_id = $chat_info['chatid'];
        }

        //从钉钉同步获取群成员列表
        $ding_chat_info = $this->ding_model->get_chat_group($chat_id, $app_id);
        if (FALSE === $ding_chat_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        $ownerid = $ding_chat_info['chat_info']['owner'];
        $useridList = $ding_chat_info['chat_info']['useridlist'];

        $ding_user_list_info = $this->ding_model->get_user_list_by_userids(implode(',',$useridList),implode(',',array_keys($this::DING_MAPPING)));
        if (FALSE === $ding_user_list_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }

        $zc_list = [];
        $userList = [];
        $use_list_result = $ding_user_list_info['result'];
        foreach ($use_list_result as $ding_user_info) {
            $field_list = $ding_user_info['field_list'];
            $item = [
                'name'=>'',
                'email'=>'',
                'mobile'=>'',
                'job_number'=>'',
                'zc_name'=>'',
                'sex_type'=>-1,
                'userid'=>$ding_user_info['userid'],
            ];
            foreach($field_list as $filed_info){
                $filed_code = $filed_info['field_code'];
                if(!array_key_exists($filed_code,$this::DING_MAPPING)){
                    continue;
                }
                $key = $this::DING_MAPPING[$filed_code];
                if(!array_key_exists('value',$filed_info)){
                    continue;
                }
                $item[$key] = $filed_info['value'];
            }
            if($item['zc_name'] && !in_array($item['zc_name'],$zc_list)){
                $zc_list [] = $item['zc_name'];
            }
            //添加职场信息
            $this->load->model('zc_model');
            $zc_info = $this->zc_model->get_zc_by_name($item['zc_name']);
            if (!$zc_info) {
                $this->zc_model->add_zc($item['zc_name'], '', '', '', '', 2);
            }
        
            if ($name) {
                if (strpos($item['name'], $name) === false) {
                    continue;
                }
            }

            if ($jobNumber) {
                if (strpos($item['job_number'], $jobNumber) === false) {
                    continue;
                }
            }

            if ($zc_name) {
                if (strpos($item['zc_name'], $zc_name) === false) {
                    continue;
                }
            }

            if ($sex!=-1) {
                if (intval($item['sex_type'] )!==intval($sex)) {
                    continue;
                }
            }
            $userList[] = $item;
      }
      
        $user_return_list = array_slice($userList,$page_size*($page-1),$page_size);
        // $this->load->biz(['zc_biz']);
        // $zc_list = $this->zc_biz->get_zc_list();
        // $zc_list = $zc_list['data']
        $app_info = $this->ding_model->get_app_info($app_id);
        $corpid  = '';
        if($app_info){
            $corpid = $app_info['corpid'];
        }

        $r['data'] = [
            'user_list'=>$user_return_list,
            'zc_list'=>$zc_list,
            'total_user_count'=>count($userList),
            'chat_info'=>$this->chat_model->get_chat_info($id),
            'corpid'=>$corpid
        ];
        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 更新机器人webhook
     */
    public function update_webhook($webhook,$id = 1)
    {
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $chat_info = $this->chat_model->get_chat_info($id);
        if (FALSE === $chat_info) {
            $r['error_code'] = ERROR_PARAMS;
            $r['error_msg'] = '群组还没创建';
            goto ARCHOR_RESULT;
        }
        $data['webhook']  = $webhook;
        //保存群信息
        $result = $this->chat_model->update_chat_info($id, $data);
        if (FALSE === $result) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        ARCHOR_RESULT:
        return $r;
    }
}
